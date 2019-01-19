<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

$page_title = $lang_module['report'];
// Kiểm tra thư viện Excel
if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
    trigger_error('No class PhpSpreadsheet', 256);
}

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

$array_fields = [
    'stt' => $lang_module['report_field_stt'],
    'full_name' => $lang_module['report_field_full_name'],
    'birthday' => $lang_module['report_field_birthday'],
    'workplace' => $lang_module['report_field_workplace'],
    'email' => $lang_module['report_field_email'],
    'phone' => $lang_module['report_field_phone'],
    'belgiumschool' => $lang_module['report_field_belgiumschool'],
    'course' => $lang_module['report_field_course'],
    'studytime' => $lang_module['report_field_studytime'],
    'edutype' => $lang_module['report_field_edutype'],
    'branch' => $lang_module['report_field_branch'],
    'othernote' => $lang_module['report_field_othernote']
];
$request = [
    'fields' => $nv_Request->get_typed_array('fields', 'get', 'title', []),
    'belgiumschool' => $nv_Request->get_typed_array('belgiumschool', 'get', 'int', []),
    'studytime_from' => $nv_Request->get_string('studytime_from', 'post', ''),
    'studytime_to' => $nv_Request->get_string('studytime_to', 'post', ''),
    'edutype' => $nv_Request->get_typed_array('edutype', 'get', 'int', []),
    'branch' => $nv_Request->get_typed_array('branch', 'get', 'int', []),
    'group_id' => $nv_Request->get_typed_array('group_id', 'get', 'int', [])
];
$is_submit = ($nv_Request->isset_request('submit_view', 'get') or $nv_Request->isset_request('submit_excel', 'get'));
$error = [];
$array = [];
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;
$query_data = [
    NV_LANG_VARIABLE => NV_LANG_DATA,
    NV_NAME_VARIABLE => $module_name,
    NV_OP_VARIABLE => $op,
];
$query_data = array_merge($query_data, $request);
$query_data['submit_view'] = 1;

if ($is_submit) {
    if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $request['studytime_from'], $m)) {
        $request['studytime_from'] = mktime(0, 0, 0, intval($m[2]), intval($m[1]), intval($m[3]));
    } else {
        $request['studytime_from'] = 0;
    }
    if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $request['studytime_to'], $m)) {
        $request['studytime_to'] = mktime(23, 59, 59, intval($m[2]), intval($m[1]), intval($m[3]));
    } else {
        $request['studytime_to'] = 0;
    }

    if (empty($request['fields'])) {
        $error[] = $lang_module['report_error_field'];
    } elseif (empty($request['group_id'])) {
        $error[] = $lang_module['report_error_group_id'];
    }

    // Truy vấn CSDL theo các điều kiện
    if (empty($error)) {
        $where = [];
        $where[] = "tb1.userid=tb2.userid";

        // Chi hội
        $where_or = [];
        $where_or[] = "tb1.group_id IN(" . implode(',', $request['group_id']) . ")";
        foreach ($request['group_id'] as $group_id) {
            $where_or[] = "FIND_IN_SET(" . $group_id . ", tb1.in_groups)";
        }
        $where[] = "(" . implode(' OR ', $where_or) . ")";

        // Trường đã học tại Bỉ
        $request['belgiumschool'] = array_intersect($request['belgiumschool'], array_keys($array_belgiumschool));
        if (!empty($request['belgiumschool']) and sizeof($request['belgiumschool']) < sizeof($array_belgiumschool)) {
            $where_or = [];
            foreach ($request['belgiumschool'] as $value) {
                $where_or[] = 'tb2.belgiumschool=' . $db->quote($value);
            }
            $where[] = "(" . implode(' OR ', $where_or) . ")";
        }
        // Thời gian học từ
        if (!empty($request['studytime_from'])) {
            $where[] = "tb2.studytime_from>=" . $request['studytime_from'];
        }
        // Thời gian học đến
        if (!empty($request['studytime_to'])) {
            $where[] = "tb2.studytime_to<=" . $request['studytime_to'];
        }
        // Loại hình đào tạo
        $request['edutype'] = array_intersect($request['edutype'], array_keys($array_edutype));
        if (!empty($request['edutype']) and sizeof($request['edutype']) < sizeof($array_edutype)) {
            $where_or = [];
            foreach ($request['edutype'] as $value) {
                $where_or[] = 'tb2.edutype=' . $db->quote($value);
            }
            $where[] = "(" . implode(' OR ', $where_or) . ")";
        }
        // Ngành học
        $request['branch'] = array_intersect($request['branch'], array_keys($array_branch));
        if (!empty($request['branch']) and sizeof($request['branch']) < sizeof($array_branch)) {
            $where_or = [];
            foreach ($request['branch'] as $value) {
                $where_or[] = 'tb2.branch=' . $db->quote($value);
            }
            $where[] = "(" . implode(' OR ', $where_or) . ")";
        }

        // Xuất excel
        $db->sqlreset()->select('COUNT(tb1.userid)')->from(NV_USERS_GLOBALTABLE . " tb1, " . NV_USERS_GLOBALTABLE . "_info tb2");
        if (!empty($where)) {
            $db->where(implode(' AND ', $where));
        }

        if ($nv_Request->isset_request('submit_excel', 'get')) {
            if ($sys_info['allowed_set_time_limit']) {
                set_time_limit(0);
            }
            if ($sys_info['ini_set_support']) {
                $memoryLimitMB = (integer) ini_get('memory_limit');
                if ($memoryLimitMB < 1024) {
                    ini_set("memory_limit", "1024M");
                }
            }

            $fileName = NV_ROOTDIR . '/modules/' . $module_file . '/excel-report-template.xlsx';
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileName);
            $sheet = $spreadsheet->getActiveSheet();
            $line = 2;

            // Ghi phần tiêu đề ra excel
            $colIndex = 0;
            $maxColString = '';
            foreach ($array_fields as $key => $value) {
                if (in_array($key, $request['fields'])) {
                    $colIndex++;
                    $maxColString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->setCellValue($maxColString . '2', $value);
                }
            }

            $db->select('tb1.first_name, tb1.last_name, tb1.birthday, tb1.email, tb2.*');
            $result = $db->query($db->sql());
            $sttStart = 0;
            while ($row = $result->fetch()) {
                $sttStart++;
                $line++;
                $row['stt'] = $sttStart;

                $colIndex = 0;
                foreach ($array_fields as $key => $value) {
                    if (in_array($key, $request['fields'])) {
                        $colIndex++;
                        $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);

                        $cellValue = '';
                        if (in_array($key, ['stt', 'workplace', 'email', 'phone', 'course', 'othernote'])) {
                            $cellValue = $row[$key];
                        } elseif ($key == 'full_name') {
                            $cellValue = nv_show_name_user($row['first_name'], $row['last_name']);
                        } elseif ($key == 'birthday') {
                            $cellValue = empty($row['birthday']) ? '' : nv_date('d/m/Y', $row['birthday']);
                        } elseif ($key == 'belgiumschool') {
                            $belgiumschool = isset($array_belgiumschool[$row['belgiumschool']]) ? $array_belgiumschool[$row['belgiumschool']]['title'] : '';
                            if (preg_match('/^[\-]+$/', $belgiumschool)) {
                                $belgiumschool = '';
                            }
                            $cellValue = $belgiumschool;
                        } elseif ($key == 'studytime') {
                            $studytime = [];
                            if (!empty($row['studytime_from'])) {
                                $studytime[] = $row['studytime_from'];
                            }
                            if (!empty($row['studytime_to'])) {
                                $studytime[] = $row['studytime_to'];
                            }
                            $cellValue = implode(' - ', $studytime);
                        } elseif ($key == 'edutype') {
                            $edutype = isset($array_edutype[$row['edutype']]) ? $array_edutype[$row['edutype']]['title'] : '';
                            if (preg_match('/^[\-]+$/', $edutype)) {
                                $edutype = '';
                            }
                            $cellValue = $edutype;
                        } elseif ($key == 'branch') {
                            $branch = isset($array_branch[$row['branch']]) ? $array_branch[$row['branch']]['title'] : '';
                            if (preg_match('/^[\-]+$/', $branch)) {
                                $branch = '';
                            }
                            $cellValue = $branch;
                        }

                        $sheet->setCellValue($colString . $line, $cellValue);
                    }
                }
            }

            // Kẻ viền bao vùng dữ liệu
            $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:' . $maxColString . '' . $line)->applyFromArray($styleArray);

            // Ghi ra file
            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $file_src = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/dshv.' . NV_CHECK_SESSION . '.xlsx';
            $objWriter->save($file_src);

            // Kết thúc
            $download = new NukeViet\Files\Download($file_src, NV_ROOTDIR . '/' . NV_TEMP_DIR, change_alias($lang_module['exportexcel_filename']) . '.xlsx');
            $download->download_file();
            exit();
        }

        // Xem trên site
        $num_items = $db->query($db->sql())->fetchColumn();
        $db->select('tb1.first_name, tb1.last_name, tb1.birthday, tb1.email, tb2.*')->limit($per_page)->offset(($page - 1) * $per_page);

        $result = $db->query($db->sql());
        $sttStart = ($page - 1) * $per_page;
        while ($row = $result->fetch()) {
            $sttStart++;
            $row['stt'] = $sttStart;
            $array[$row['userid']] = $row;
        }
    }
}

$base_url = NV_BASE_ADMINURL . 'index.php?' . http_build_query($query_data, '', '&amp;');

$xtpl = new XTemplate('report.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('FORM_CSS', ($is_submit and empty($error)) ? ' hidden' : '');

$request['studytime_from'] = $request['studytime_from'] ? nv_date('d/m/Y', $request['studytime_from']) : '';
$request['studytime_to'] = $request['studytime_to'] ? nv_date('d/m/Y', $request['studytime_to']) : '';

$xtpl->assign('DATA', $request);

if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

foreach ($array_fields as $key => $value) {
    $xtpl->assign('FIELD', [
        'key' => $key,
        'title' => $value,
        'checked' => (!$is_submit or in_array($key, $request['fields'])) ? ' checked="checked"' : ''
    ]);
    $xtpl->parse('main.field');
}

foreach ($array_belgiumschool as $belgiumschool) {
    $belgiumschool['selected'] = (!$is_submit or in_array($belgiumschool['id'], $request['belgiumschool'])) ? ' selected="selected"' : '';
    $xtpl->assign('BELGIUMSCHOOL', $belgiumschool);
    $xtpl->parse('main.belgiumschool');
}

foreach ($array_branch as $branch) {
    $branch['selected'] = (!$is_submit or in_array($branch['id'], $request['branch'])) ? ' selected="selected"' : '';
    $xtpl->assign('BRANCH', $branch);
    $xtpl->parse('main.branch');
}

foreach ($array_edutype as $edutype) {
    $edutype['selected'] = (!$is_submit or in_array($edutype['id'], $request['edutype'])) ? ' selected="selected"' : '';
    $xtpl->assign('EDUTYPE', $edutype);
    $xtpl->parse('main.edutype');
}

foreach ($array_group_managers as $group) {
    $group['checked'] = (!$is_submit or in_array($group['group_id'], $request['group_id'])) ? ' checked="checked"' : '';
    $xtpl->assign('GROUP', $group);
    $xtpl->parse('main.group');
}

if ($is_submit and empty($error)) {
    foreach ($array_fields as $key => $value) {
        if (in_array($key, $request['fields'])) {
            $xtpl->assign('THEAD_TITLE', $value);
            if (in_array($key, ['stt'])) {
                $xtpl->assign('THEAD_CSS', 'w50');
            } elseif (in_array($key, ['birthday', 'phone', 'course', 'studytime'])) {
                $xtpl->assign('THEAD_CSS', 'mw120');
            } else {
                $xtpl->assign('THEAD_CSS', 'mw200');
            }
            $xtpl->parse('main.data.thead');
        }
    }

    // Xuất các hội viên
    foreach ($array as $row) {
        foreach ($array_fields as $key => $value) {
            if (in_array($key, $request['fields'])) {
                if (in_array($key, ['stt', 'workplace', 'email', 'phone', 'course', 'othernote'])) {
                    $xtpl->assign('VALUE', $row[$key]);
                } elseif ($key == 'full_name') {
                    $xtpl->assign('VALUE', nv_show_name_user($row['first_name'], $row['last_name']));
                } elseif ($key == 'birthday') {
                    $xtpl->assign('VALUE', empty($row['birthday']) ? '' : nv_date('d/m/Y', $row['birthday']));
                } elseif ($key == 'belgiumschool') {
                    $belgiumschool = isset($array_belgiumschool[$row['belgiumschool']]) ? $array_belgiumschool[$row['belgiumschool']]['title'] : '';
                    if (preg_match('/^[\-]+$/', $belgiumschool)) {
                        $belgiumschool = '';
                    }
                    $xtpl->assign('VALUE', $belgiumschool);
                } elseif ($key == 'studytime') {
                    $studytime = [];
                    if (!empty($row['studytime_from'])) {
                        $studytime[] = $row['studytime_from'];
                    }
                    if (!empty($row['studytime_to'])) {
                        $studytime[] = $row['studytime_to'];
                    }
                    $xtpl->assign('VALUE', implode(' - ', $studytime));
                } elseif ($key == 'edutype') {
                    $edutype = isset($array_edutype[$row['edutype']]) ? $array_edutype[$row['edutype']]['title'] : '';
                    if (preg_match('/^[\-]+$/', $edutype)) {
                        $edutype = '';
                    }
                    $xtpl->assign('VALUE', $edutype);
                } elseif ($key == 'branch') {
                    $branch = isset($array_branch[$row['branch']]) ? $array_branch[$row['branch']]['title'] : '';
                    if (preg_match('/^[\-]+$/', $branch)) {
                        $branch = '';
                    }
                    $xtpl->assign('VALUE', $branch);
                }
                $xtpl->parse('main.data.loop.field');
            }
        }
        $xtpl->parse('main.data.loop');
    }

    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    // Phân trang
    if (!empty($generate_page)) {
        $xtpl->assign('GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.data.generate_page');
    }

    $xtpl->parse('main.data');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
