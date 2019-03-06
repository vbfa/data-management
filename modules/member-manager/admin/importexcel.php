<?php

/**
 * @Project VBFA MEMBER-MANAGER
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: GNU/GPL version 2 or any later version
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

$page_title = $lang_module['importexcel'];

// Kiểm tra thư viện Excel
if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
    trigger_error('No class PhpSpreadsheet', 256);
}

// Tải về file mẫu
if ($nv_Request->isset_request('template', 'get')) {
    $fileName = NV_ROOTDIR . '/modules/' . $module_file . '/excel-import-template.xlsx';
    $download = new NukeViet\Files\Download($fileName, NV_ROOTDIR . '/modules/' . $module_file, basename($fileName), true, 0);
    $download->download_file();
    exit();
}

$array_setting = [
    'group_id' => 0,
    'invalidmode' => 0,
    'existsmode' => 0
];
$array_invalidmode = [
    $lang_module['importexcel_select_invalidmode0'],
    $lang_module['importexcel_select_invalidmode1']
];
$array_existsmode = [
    $lang_module['importexcel_select_existsmode0'],
    $lang_module['importexcel_select_existsmode1']
];
// Xác định các nhóm được quyền quản lý
$array_group_managers = [];
$groups_list = nv_groups_list();
foreach ($groups_list as $gid => $gtitle) {
    if ($gid > 9 and isset($global_group_additions[$gid]) and (defined('NV_IS_GODADMIN') or defined('NV_IS_SPADMIN') or (
        isset($global_permissions[$admin_info['admin_id']]) and in_array($gid, $global_permissions[$admin_info['admin_id']])))
    ) {
        $array_group_managers[$gid] = [
            'group_id' => $gid,
            'group_title' => $gtitle,
            'group_code' => $global_group_additions[$gid]['group_code']
        ];
    }
}
$error = [];

// Xác định các ngành học và chuyển sang dạng alias => id
$array_alias_branch = [];
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_branch";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    if (preg_match('/^[\-]+$/', $row['title'])) {
        $alias = "";
    } else {
        $alias = nv_strtolower(change_alias($row['title']));
    }
    $array_alias_branch[$alias] = $row['id'];
}
// Xác định các trường đã học tại Bỉ và chuyển sang dạng alias => id
$array_alias_belgiumschool = [];
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_belgiumschool";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    if (preg_match('/^[\-]+$/', $row['title'])) {
        $alias = "";
    } else {
        $alias = nv_strtolower(change_alias($row['title']));
    }
    $array_alias_belgiumschool[$alias] = $row['id'];
}
// Xác định các trường đã học tại Việt Nam và chuyển sang dạng alias => id
$array_alias_vnschool = [];
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_vnschool";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    if (preg_match('/^[\-]+$/', $row['title'])) {
        $alias = "";
    } else {
        $alias = nv_strtolower(change_alias($row['title']));
    }
    $array_alias_vnschool[$alias] = $row['id'];
}
// Xác định các loại hình đào tạo và chuyển sang dạng alias => id
$array_alias_edutype = [];
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_edutype";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    if (preg_match('/^[\-]+$/', $row['title'])) {
        $alias = "";
    } else {
        $alias = nv_strtolower(change_alias($row['title']));
    }
    $array_alias_edutype[$alias] = $row['id'];
}
// Xác định các lĩnh vực quan tâm và chuyển sang dạng alias => id
$array_alias_concernarea = [];
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_concernarea";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    if (preg_match('/^[\-]+$/', $row['title'])) {
        $alias = "";
    } else {
        $alias = nv_strtolower(change_alias($row['title']));
    }
    $array_alias_concernarea[$alias] = $row['id'];
}

$xtpl = new XTemplate('importexcel.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);

if ($nv_Request->isset_request('submit', 'post')) {
    $array_setting['group_id'] = $nv_Request->get_int('group_id', 'post', 0);
    $array_setting['invalidmode'] = $nv_Request->get_int('invalidmode', 'post', 0);
    $array_setting['existsmode'] = $nv_Request->get_int('existsmode', 'post', 0);

    if (empty($array_setting['group_id']) or !isset($array_group_managers[$array_setting['group_id']])) {
        $error[] = $lang_module['importexcel_error_file'];
    }

    $upload = new NukeViet\Files\Upload(['documents'], $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, NV_MAX_WIDTH, NV_MAX_HEIGHT);
    $upload->setLanguage($lang_global);
    $upload_info = $upload->save_file($_FILES['fileexcel'], NV_ROOTDIR . '/' . NV_TEMP_DIR, false, $global_config['nv_auto_resize']);
    $file_ready = false;

    // Kiểm tra file tải lên
    if (!empty($upload_info['error'])) {
        $error[] = $upload_info['error'];
    } elseif (!in_array($upload_info['ext'], [
        'xls',
        'xlsx',
        'ods'
    ])) {
        $error[] = $lang_module['importexcel_error_filetype'];
    } else {
        $file_ready = true;
    }

    // Thử đọc excel của file
    if ($file_ready) {
        if ($sys_info['allowed_set_time_limit']) {
            set_time_limit(0);
        }
        if ($sys_info['ini_set_support']) {
            $memoryLimitMB = (integer) ini_get('memory_limit');
            if ($memoryLimitMB < 1024) {
                ini_set("memory_limit", "1024M");
            }
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($upload_info['name']);
            $sheet = $spreadsheet->getActiveSheet();
        } catch (Exception $e) {
            $error[] = $lang_module['importexcel_error_file_minetype'];
        }
    }

    // Bắt đầu đọc và xử lý dữ liệu
    if (empty($error)) {
        // Cột cao nhất và ô cao nhất
        $highestRow = $sheet->getHighestRow();
        $highestColumn = strtoupper($sheet->getHighestColumn());

        // Đọc hết dữ liệu
        $stt = 0;
        $break_row = false;
        $array_data_read = [];

        for ($row = 3; $row <= $highestRow; ++$row) {
            $array_data_read[$stt] = [];
            $check_field = true;

            // Họ tên
            $cell = $sheet->getCellByColumnAndRow(2, $row);
            $array_data_read[$stt]['full_name'] = trim($cell->getCalculatedValue());
            $array_data_read[$stt]['first_name'] = '';
            $array_data_read[$stt]['last_name'] = '';
            if (!empty($array_data_read[$stt]['full_name'])) {
                $full_name = array_values(array_filter(array_map('trim', explode(' ', $array_data_read[$stt]['full_name']))));
                $full_name_last = sizeof($full_name) - 1;
                $array_data_read[$stt]['first_name'] = $full_name[$full_name_last];
                unset($full_name[$full_name_last]);
                if (!empty($full_name)) {
                    $array_data_read[$stt]['last_name'] = implode(' ', $full_name);
                }
            }

            $cell = $sheet->getCellByColumnAndRow(3, $row);
            if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                $value = $cell->getCalculatedValue();
                if (strpos($value, '/') === false) {
                    $unixTimeStamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                    $value = date('d/m/Y', $unixTimeStamp);
                }
                $array_data_read[$stt]['birthday'] = $value;
            } else {
                $array_data_read[$stt]['birthday'] = trim($cell->getCalculatedValue());
            }

            // Nơi làm việc hiện tại
            $cell = $sheet->getCellByColumnAndRow(4, $row);
            $array_data_read[$stt]['workplace'] = trim($cell->getCalculatedValue());

            // Đọc và xử lý hợp chuẩn email
            $cell = $sheet->getCellByColumnAndRow(5, $row);
            $array_data_read[$stt]['email'] = trim($cell->getCalculatedValue());
            $array_data_read[$stt]['email'] = nv_strtolower($array_data_read[$stt]['email']);
            $array_data_read[$stt]['email'] = str_replace(';', ',', $array_data_read[$stt]['email']);
            $array_data_read[$stt]['email'] = array_unique(array_filter(array_map('trim', explode(',', $array_data_read[$stt]['email']))));
            if (empty($array_data_read[$stt]['email'])) {
                $array_data_read[$stt]['email'] = '';
            } else {
                $array_data_read[$stt]['email'] = $array_data_read[$stt]['email'][0];
            }

            $cell = $sheet->getCellByColumnAndRow(6, $row);
            $array_data_read[$stt]['phone'] = trim($cell->getCalculatedValue());
            // Trường đã học tại Bỉ
            $cell = $sheet->getCellByColumnAndRow(7, $row);
            $array_data_read[$stt]['belgiumschool'] = trim($cell->getCalculatedValue());
            // Trường đã học tại Việt Nam
            $cell = $sheet->getCellByColumnAndRow(8, $row);
            $array_data_read[$stt]['vnschool'] = trim($cell->getCalculatedValue());
            // Khóa học
            $cell = $sheet->getCellByColumnAndRow(9, $row);
            $array_data_read[$stt]['course'] = trim($cell->getCalculatedValue());
            // Thời gian học XXXX - YYYY
            $cell = $sheet->getCellByColumnAndRow(10, $row);
            $array_data_read[$stt]['studytime'] = trim($cell->getCalculatedValue());
            $array_data_read[$stt]['studytime_from'] = 0;
            $array_data_read[$stt]['studytime_to'] = 0;
            // Loại hình đào tạo
            $cell = $sheet->getCellByColumnAndRow(11, $row);
            $array_data_read[$stt]['edutype'] = trim($cell->getCalculatedValue());
            // Ngành học
            $cell = $sheet->getCellByColumnAndRow(12, $row);
            $array_data_read[$stt]['branch'] = trim($cell->getCalculatedValue());
            // Lĩnh vực quan tâm
            $cell = $sheet->getCellByColumnAndRow(13, $row);
            $array_data_read[$stt]['concernarea'] = trim($cell->getCalculatedValue());
            // Ghi chú
            $cell = $sheet->getCellByColumnAndRow(14, $row);
            $array_data_read[$stt]['othernote'] = trim($cell->getCalculatedValue());

            // Tới dòng trống là kết thúc
            $filter_check = array_filter($array_data_read[$stt]);
            if ($filter_check == []) {
                $check_field = false;
                $break_row = true;
            }

            // Xóa nếu dữ liệu không hợp lệ
            if (!$check_field) {
                unset($array_data_read[$stt]);
            }

            $stt++;

            if ($break_row) {
                break;
            }
        }

        // Kiểm tra dữ liệu
        foreach ($array_data_read as $key => $row) {
            $line = number_format($key + 3, 0, ',', '.');
            $line_is_error = false;

            // Kiểm tra ngày sinh
            if (preg_match('/^([0-9]+)\/([0-9]+)\/([0-9]{4})$/', $row['birthday'], $m)) {
                $array_data_read[$key]['birthday'] = mktime(0, 0, 0, intval($m[2]), intval($m[1]), intval($m[3]));
            } else {
                $array_data_read[$key]['birthday'] = 0;
            }

            // Kiểm tra email
            if (empty($row['email'])) {
                $error[] = sprintf($lang_module['importexcel_error_email_empty'], $line);
                $line_is_error = true;
            } elseif (($error_xemail = nv_check_valid_email($row['email'])) != '') {
                $error[] = sprintf($lang_module['importexcel_error_email'], $line, $error_xemail);
                $line_is_error = true;
            }

            // Kiểm tra thời gian học
            if (!empty($row['studytime'])) {
                if (!preg_match('/^([0-9]+)[\s]*\-[\s]*([0-9]+)$/', $row['studytime'], $m) or $m[1] > $m[2]) {
                    $error[] = sprintf($lang_module['importexcel_error_studytime'], $line, $row['studytime']);
                    $line_is_error = true;
                } else {
                    $array_data_read[$key]['studytime'] = $m[1] . ' - ' . $m[2];
                    $array_data_read[$key]['studytime_from'] = $m[1];
                    $array_data_read[$key]['studytime_to'] = $m[2];
                }
            }

            $array_data_read[$key]['error'] = $line_is_error;
        }

        if (empty($error) or $array_setting['invalidmode'] == 0) {
            $error = [];
            $num_exists = 0; // Số hội viên tồn tại
            $num_added = 0; // Số hội viên thêm mới
            $num_updated = 0; // Số hội viên cập nhật lại thông tin
            $num_email_exists = 0; // Trùng email ở OPENID => Cái này bỏ qua với mọi tùy chọn

            foreach ($array_data_read as $key => $row) {
                if ($row['error']) {
                    continue;
                }

                $row['userid'] = 0;
                $row['group_id'] = $array_setting['group_id'];
                $row['old_group_id'] = 0;
                $row['old_in_groups'] = '';
                $row['regdate'] = NV_CURRENTTIME;
                $is_exists = false;

                // Thực hiện câu truy vấn để kiểm tra email đã tồn tại trong nv3_users_openid chưa.
                // Nếu trùng trong trường hợp này thì bỏ qua hoàn toàn
                $stmt = $db->prepare('SELECT userid FROM ' . NV_USERS_GLOBALTABLE . '_openid WHERE email= :email');
                $stmt->bindParam(':email', $row['email'], PDO::PARAM_STR);
                $stmt->execute();
                $query_error_email_openid = $stmt->fetchColumn();
                if ($query_error_email_openid) {
                    $num_email_exists++;
                    continue;
                }

                // Thực hiện câu truy vấn để kiểm tra email đã tồn tại chưa.
                $stmt = $db->prepare('SELECT * FROM ' . NV_USERS_GLOBALTABLE . ' WHERE email= :email');
                $stmt->bindParam(':email', $row['email'], PDO::PARAM_STR);
                $stmt->execute();
                $user = $stmt->fetch();

                if (!empty($user)) {
                    $row['userid'] = $user['userid'];
                    $row['old_group_id'] = $user['group_id'];
                    $row['old_in_groups'] = $user['in_groups'];
                    $row['regdate'] = $user['regdate'];
                    $is_exists = true;
                }

                // Thực hiện câu truy vấn để kiểm tra email đã tồn tại trong nv4_users_reg chưa.
                $stmt = $db->prepare('SELECT userid FROM ' . NV_USERS_GLOBALTABLE . '_reg WHERE email= :email');
                $stmt->bindParam(':email', $row['email'], PDO::PARAM_STR);
                $stmt->execute();
                $query_error_email_reg = $stmt->fetchColumn();
                if ($query_error_email_reg) {
                    $row['userid'] = -1;
                    $is_exists = true;
                }

                if ($is_exists) {
                    $num_exists++;
                }

                // Xóa trong bảng REG
                if ($row['userid'] == -1) {
                    $stmt = $db->prepare('DELETE FROM ' . NV_USERS_GLOBALTABLE . '_reg WHERE email= :email');
                    $stmt->bindParam(':email', $row['email'], PDO::PARAM_STR);
                    $stmt->execute();
                }

                // Xác định ngành học hiện có, thêm ngành học chưa có
                $row['branch_id'] = '';
                $branch_alias = empty($row['branch']) ? '' : nv_strtolower(change_alias($row['branch']));
                if (isset($array_alias_branch[$branch_alias])) {
                    $row['branch_id'] = $array_alias_branch[$branch_alias];
                } elseif (!empty($branch_alias)) {
                    // Thêm mới ngành học vào CSDL
                    $weight = $db->query("SELECT MAX(weight) FROM " . NV_CMNG_TABLE . "_branch")->fetchColumn();
                    $weight = $weight + 1;
                    $sql = "INSERT INTO " . NV_CMNG_TABLE . "_branch (title, description, status, weight) VALUES (" . $db->quote($row['branch']) . ", '', 1, " . $weight . ")";
                    $branch_id = $db->insert_id($sql, 'id');
                    if ($branch_id) {
                        $row['branch_id'] = $branch_id;
                        $array_alias_branch[$branch_alias] = $branch_id;
                    }
                }

                // Xác định trường đã học tại Bỉ hiện có, thêm trường chưa có
                $row['belgiumschool_id'] = '';
                $belgiumschool_alias = empty($row['belgiumschool']) ? '' : nv_strtolower(change_alias($row['belgiumschool']));
                if (isset($array_alias_belgiumschool[$belgiumschool_alias])) {
                    $row['belgiumschool_id'] = $array_alias_belgiumschool[$belgiumschool_alias];
                } elseif (!empty($belgiumschool_alias)) {
                    // Thêm mới trường đã học tại Bỉ vào CSDL
                    $weight = $db->query("SELECT MAX(weight) FROM " . NV_CMNG_TABLE . "_belgiumschool")->fetchColumn();
                    $weight = $weight + 1;
                    $sql = "INSERT INTO " . NV_CMNG_TABLE . "_belgiumschool (title, description, status, weight) VALUES (" . $db->quote($row['belgiumschool']) . ", '', 1, " . $weight . ")";
                    $belgiumschool_id = $db->insert_id($sql, 'id');
                    if ($belgiumschool_id) {
                        $row['belgiumschool_id'] = $belgiumschool_id;
                        $array_alias_belgiumschool[$belgiumschool_alias] = $belgiumschool_id;
                    }
                }

                // Xác định trường đã học tại Việt Nam hiện có, thêm trường chưa có
                $row['vnschool_id'] = '';
                $vnschool_alias = empty($row['vnschool']) ? '' : nv_strtolower(change_alias($row['vnschool']));
                if (isset($array_alias_vnschool[$vnschool_alias])) {
                    $row['vnschool_id'] = $array_alias_vnschool[$vnschool_alias];
                } elseif (!empty($vnschool_alias)) {
                    // Thêm mới trường đã học tại Việt Nam vào CSDL
                    $weight = $db->query("SELECT MAX(weight) FROM " . NV_CMNG_TABLE . "_vnschool")->fetchColumn();
                    $weight = $weight + 1;
                    $sql = "INSERT INTO " . NV_CMNG_TABLE . "_vnschool (title, description, status, weight) VALUES (" . $db->quote($row['vnschool']) . ", '', 1, " . $weight . ")";
                    $vnschool_id = $db->insert_id($sql, 'id');
                    if ($vnschool_id) {
                        $row['vnschool_id'] = $vnschool_id;
                        $array_alias_vnschool[$vnschool_alias] = $vnschool_id;
                    }
                }

                // Xác định loại hình đào tạo hiện có, thêm loại hình chưa có
                $row['edutype_id'] = '';
                $edutype_alias = empty($row['edutype']) ? '' : nv_strtolower(change_alias($row['edutype']));
                if (isset($array_alias_edutype[$edutype_alias])) {
                    $row['edutype_id'] = $array_alias_edutype[$edutype_alias];
                } elseif (!empty($edutype_alias)) {
                    // Thêm mới loại hình đào tạo vào CSDL
                    $weight = $db->query("SELECT MAX(weight) FROM " . NV_CMNG_TABLE . "_edutype")->fetchColumn();
                    $weight = $weight + 1;
                    $sql = "INSERT INTO " . NV_CMNG_TABLE . "_edutype (title, description, status, weight) VALUES (" . $db->quote($row['edutype']) . ", '', 1, " . $weight . ")";
                    $edutype_id = $db->insert_id($sql, 'id');
                    if ($edutype_id) {
                        $row['edutype_id'] = $edutype_id;
                        $array_alias_edutype[$edutype_alias] = $edutype_id;
                    }
                }

                // Xác định lĩnh vực quan tâm hiện có, thêm loại hình chưa có
                $row['concernarea_id'] = '';
                $concernarea_alias = empty($row['concernarea']) ? '' : nv_strtolower(change_alias($row['concernarea']));
                if (isset($array_alias_concernarea[$concernarea_alias])) {
                    $row['concernarea_id'] = $array_alias_concernarea[$concernarea_alias];
                } elseif (!empty($concernarea_alias)) {
                    // Thêm mới lĩnh vực quan tâm vào CSDL
                    $weight = $db->query("SELECT MAX(weight) FROM " . NV_CMNG_TABLE . "_concernarea")->fetchColumn();
                    $weight = $weight + 1;
                    $sql = "INSERT INTO " . NV_CMNG_TABLE . "_concernarea (title, description, status, weight) VALUES (" . $db->quote($row['concernarea']) . ", '', 1, " . $weight . ")";
                    $concernarea_id = $db->insert_id($sql, 'id');
                    if ($concernarea_id) {
                        $row['concernarea_id'] = $concernarea_id;
                        $array_alias_concernarea[$concernarea_alias] = $concernarea_id;
                    }
                }

                // Bổ sung một số trường
                $row['old_in_groups'] = empty($row['old_in_groups']) ? [] : array_filter(array_unique(explode(',', $row['old_in_groups'])));
                $row['in_groups'] = $row['old_in_groups'];
                $row['in_groups'][] = $row['group_id']; // Chi hội của hội viên
                $row['in_groups'][] = 4; // Thành viên chính thức
                $row['in_groups'] = array_filter(array_unique($row['in_groups']));

                if ($row['userid'] > 0) {
                    // Cập nhật tài khoản
                    if ($array_setting['existsmode'] == 1) {
                        $num_updated++;

                        // Cập nhật thông tin cơ bản
                        $sql = "UPDATE " . NV_USERS_GLOBALTABLE . " SET
                            first_name=" . $db->quote($row['first_name']) . ",
                            last_name=" . $db->quote($row['last_name']) . ",
                            birthday=" . $row['birthday'] . "
                        WHERE userid=" . $row['userid'];
                        $db->query($sql);

                        // Cập nhật dữ liệu tùy chỉnh
                        $sql = "UPDATE " . NV_USERS_GLOBALTABLE . "_info SET
                            workplace=" . $db->quote($row['workplace']) . ",
                            phone=" . $db->quote($row['phone']) . ",
                            belgiumschool=" . $db->quote($row['belgiumschool_id']) . ",
                            vnschool=" . $db->quote($row['vnschool_id']) . ",
                            course=" . $db->quote($row['course']) . ",
                            studytime_from=" . $row['studytime_from'] . ",
                            studytime_to=" . $row['studytime_to'] . ",
                            edutype=" . $db->quote($row['edutype_id']) . ",
                            concernarea=" . $db->quote($row['concernarea_id']) . ",
                            othernote=" . $db->quote($row['othernote']) . ",
                            branch=" . $db->quote($row['branch_id']) . "
                        WHERE userid=" . $row['userid'];
                        $db->query($sql);

                        // Thêm mới vào nhóm nếu chưa có
                        if (!in_array($row['group_id'], $row['old_in_groups'])) {
                            nv_groups_add_user($row['group_id'], $userid);
                        }
                    }
                } else {
                    // Thêm mới tài khoản
                    $num_added++;
                    $group_code = $array_group_managers[$array_setting['group_id']]['group_code'];

                    // Xác định được mã hội viên, tương ứng là username của tài khoản luôn
                    $sql = "SELECT username FROM " . NV_USERS_GLOBALTABLE . " WHERE username REGEXP '^" . nv_strtolower($group_code) . "[0-9]{5}$' ORDER BY username DESC LIMIT 1";
                    $username_last = $db->query($sql)->fetchColumn();
                    if (!preg_match('/([0-9]+)$/', $username_last, $m)) {
                        $row['username'] = nv_strtolower($group_code) . '00001';
                    } else {
                        $row['username'] = nv_strtolower($group_code) . str_pad(intval($m[1]) + 1, 5, '0', STR_PAD_LEFT);
                    }

                    $sql = "INSERT INTO " . NV_USERS_GLOBALTABLE . " (
                        group_id, username, md5username, password, email, first_name, last_name, gender, birthday, sig, regdate,
                        question, answer, passlostkey, view_mail,
                        remember, in_groups, active, checknum, last_login, last_ip, last_agent, last_openid, idsite)
                    VALUES (
                        " . $row['group_id'] . ",
                        :username,
                        :md5_username,
                        :password,
                        :email,
                        :first_name,
                        :last_name,
                        '',
                        " . $row['birthday'] . ",
                        '',
                        " . NV_CURRENTTIME . ",
                        '',
                        '',
                        '',
                         0,
                         1,
                         '" . implode(',', $row['in_groups']) . "', 1, '', 0, '', '', '', " . $global_config['idsite'] . "
                    )";

                    $data_insert = [];
                    $data_insert['username'] = $row['username'];
                    $data_insert['md5_username'] = nv_md5safe($row['username']);
                    $data_insert['password'] = $crypt->hash_password($row['username'], $global_config['hashprefix']);
                    $data_insert['email'] = $row['email'];
                    $data_insert['first_name'] = $row['first_name'];
                    $data_insert['last_name'] = $row['last_name'];

                    $userid = $db->insert_id($sql, 'userid', $data_insert);

                    if ($userid) {
                        // Thêm vào bảng info
                        $sql = "INSERT INTO " . NV_USERS_GLOBALTABLE . "_info (
                            userid, workplace, phone, belgiumschool, vnschool, course, studytime_from, studytime_to, edutype, othernote,
                            address, fb_twitter, contactsocial, branch, learningtasks, concernarea, contactinfo
                        ) VALUES (
                            " . $userid . ", " . $db->quote($row['workplace']) . ", " . $db->quote($row['phone']) . ", " . $db->quote($row['belgiumschool_id']) . ", " . $db->quote($row['vnschool_id']) . ",
                            " . $db->quote($row['course']) . ", " . $row['studytime_from'] . ", " . $row['studytime_to'] . ",
                            " . $db->quote($row['edutype_id']) . ", " . $db->quote($row['othernote']) . ", '', '', '',
                            " . $db->quote($row['branch_id']) . ", '', " . $db->quote($row['concernarea_id']) . ", ''
                        )";
                        $db->query($sql);

                        // Thêm vào nhóm
                        nv_groups_add_user($row['group_id'], $userid);

                        // Cập nhật số thành viên của site tăng lên
                        $db->query('UPDATE ' . NV_USERS_GLOBALTABLE . '_groups SET numbers=numbers+1 WHERE group_id=4');
                    }
                }
            }

            $nv_Cache->delMod($module_name);
            $nv_Cache->delMod('users');
            $nv_Cache->delMod(NV_CMNG_MODULE);

            $xtpl->assign('NUM_READ', number_format(sizeof($array_data_read), 0, ',', '.'));
            $xtpl->assign('NUM_ADDED', number_format($num_added, 0, ',', '.'));
            $xtpl->assign('NUM_UPDATED', number_format($num_updated, 0, ',', '.'));
            $xtpl->assign('NUM_EXISTS', number_format($num_exists, 0, ',', '.'));
            $xtpl->assign('NUM_EMAIL_EXISTS', number_format($num_email_exists, 0, ',', '.'));

            $xtpl->parse('success');
            $contents = $xtpl->text('success');

            include NV_ROOTDIR . '/includes/header.php';
            echo nv_admin_theme($contents);
            include NV_ROOTDIR . '/includes/footer.php';
        }
    }
}

$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('LINK_DOWNLOAD', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;template');

if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

foreach ($array_group_managers as $group) {
    $group['selected'] = $group['group_id'] == $array_setting['group_id'] ? ' selected="selected"' : '';
    $xtpl->assign('GROUP', $group);
    $xtpl->parse('main.group');
}

foreach ($array_invalidmode as $key => $value) {
    $xtpl->assign('INVALIDMODE', [
        'key' => $key,
        'title' => $value,
        'selected' => $key == $array_setting['invalidmode'] ? ' selected="selected"' : ''
    ]);
    $xtpl->parse('main.invalidmode');
}

foreach ($array_existsmode as $key => $value) {
    $xtpl->assign('EXISTSMODE', [
        'key' => $key,
        'title' => $value,
        'selected' => $key == $array_setting['existsmode'] ? ' selected="selected"' : ''
    ]);
    $xtpl->parse('main.existsmode');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
