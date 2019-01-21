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

// Ajax tìm hội viên
if ($nv_Request->isset_request('term', 'get')) {
    $q = $nv_Request->get_title('term', 'get', '');

    $respon = [];

    // Query tìm kiếm hội viên
    if (nv_strlen($q) > 2 and nv_strlen($q) <= NV_MAX_SEARCH_LENGTH and !empty($array_group_managers)) {
        $q = $db->dblikeescape($q);
        $where = [];
        $where_or = [];
        $where_or[] = "username LIKE '%" . $q . "%'";
        $where_fullname = $global_config['name_show'] == 0 ? "CONCAT(last_name,' ',first_name)" : "CONCAT(first_name,' ',last_name)";
        $where_or[] =  $where_fullname ." LIKE '%" . $q . "%'";
        $where[] = "(" . implode(' OR ', $where_or) . ")";

        // Nếu không phải điều hành chung và quản trị tối cao thì chỉ tìm trong
        // Nhóm được quản lý
        if (!defined('NV_IS_GODADMIN') and !defined('NV_IS_SPADMIN')) {
            $where_or = [];
            $where_or[] = "group_id IN(" . implode(',', array_keys($array_group_managers)) . ")";
            foreach ($array_group_managers as $group_id => $group_val) {
                $where_or[] = "FIND_IN_SET(" . $group_id . ", in_groups)";
            }
            $where[] = "(" . implode(' OR ', $where_or) . ")";
        }

        $sql = "SELECT username, first_name, last_name FROM " . NV_USERS_GLOBALTABLE . " WHERE " . implode(' AND ', $where) . " LIMIT 100";
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $respon[] = [
                'label' => nv_show_name_user($row['first_name'], $row['last_name']),
                'value' => nv_strtoupper($row['username'])
            ];
        }
    }

    nv_jsonOutput($respon);
}

$id = $nv_Request->get_int('id', 'get', 0);
$error = '';

if (!empty($id)) {
    $sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $module_data . "_emails WHERE id=" . $id;
    if (!defined('NV_IS_GODADMIN') and !defined('NV_IS_SPADMIN')) {
        $sql .= " AND send_id=" . $admin_info['admin_id'];
    }
    $row = $db->query($sql)->fetch();
    if (empty($row)) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=sendmail');
    }

    $array = [
        'send_cname' => $row['send_cname'],
        'send_cemail' => $row['send_cemail'],
        'receiver' => [],
        'receiver_data' => [],
        'group_id' => empty($row['receiver_groups']) ? [] : explode(',', $row['receiver_groups']), // Mảng các nhóm nhận
        'title' => $row['title'],
        'emailcontent' => nv_editor_br2nl($row['emailcontent']),
        'change_receiver' => 0
    ];

    // Xác định đúng thành viên nhận
    if (!empty($row['receiver_users'])) {
        $sql = "SELECT userid, username, first_name, last_name, email FROM " . NV_USERS_GLOBALTABLE . " WHERE userid IN(" . $row['receiver_users'] . ")";
        $result = $db->query($sql);
        while ($_row = $result->fetch()) {
            $array['receiver_data'][$_row['userid']] = [
                'full_name' => nv_show_name_user($_row['first_name'], $_row['last_name']),
                'username' => nv_strtoupper($_row['username']),
                'userid' => $_row['userid'],
                'email' => $_row['email']
            ];
        }
    }

    $page_title = $lang_module['sendmail_edit'];
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;id=' . $row['id'];
} else {
    $array = [
        'send_cname' => '',
        'send_cemail' => '',
        'receiver' => [], // Mảng username nhận
        'receiver_data' => [], // Mảng user nhận gồm tên và username
        'group_id' => [], // Mảng các nhóm nhận
        'title' => '',
        'emailcontent' => '',
        'change_receiver' => 0
    ];

    $page_title = $lang_module['sendmail_new'];
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
}

if ($nv_Request->isset_request('submit', 'post')) {
    $array['send_cname'] = nv_substr($nv_Request->get_title('send_cname', 'post', ''), 0, 150);
    $array['send_cemail'] = nv_substr($nv_Request->get_title('send_cemail', 'post', ''), 0, 150);
    $array['title'] = nv_substr($nv_Request->get_title('title', 'post', ''), 0, 250);
    $array['emailcontent'] = $nv_Request->get_editor('emailcontent', '', NV_ALLOWED_HTML_TAGS);

    if (!empty($id)) {
        // Thay đổi thông tin người nhận, gửi lại
        $array['change_receiver'] = $nv_Request->get_int('change_receiver', 'post', 0);
    }

    // Lấy người nhận mới khi thay đổi người nhận hoặc là gửi mới
    if ($array['change_receiver'] or empty($id)) {
        $array['receiver_data'] = [];
        $array['receiver'] = $nv_Request->get_typed_array('receiver', 'post', 'title', []);
        $array['group_id'] = $nv_Request->get_typed_array('group_id', 'post', 'int', []);
    }

    // Xác định đúng thành viên nhận
    if (!empty($array['receiver'])) {
        $receiver = [];
        foreach ($array['receiver'] as $_receiver) {
            $receiver[] = $db->quote(nv_strtolower($_receiver));
        }
        $sql = "SELECT userid, username, first_name, last_name, email FROM " . NV_USERS_GLOBALTABLE . " WHERE username IN(" . implode(',', $receiver) . ")";
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $array['receiver_data'][$row['userid']] = [
                'full_name' => nv_show_name_user($row['first_name'], $row['last_name']),
                'username' => nv_strtoupper($row['username']),
                'userid' => $row['userid'],
                'email' => $row['email']
            ];
        }
    }

    $array['group_id'] = array_intersect($array['group_id'], array_keys($array_group_managers));

    if (!empty($array['send_cemail']) and ($checkemail = nv_check_valid_email($array['send_cemail'])) != '') {
        $error = $checkemail;
    } elseif (empty($array['group_id']) and empty($array['receiver_data'])) {
        $error = $lang_module['sendmail_error_receiver'];
    } elseif (empty($array['title'])) {
        $error = $lang_module['sendmail_error_title'];
    } elseif (empty($array['emailcontent'])) {
        $error = $lang_module['sendmail_error_content'];
    } else {
        if (!$id) {
            // Gửi mới
            $array['sendagain'] = 1;

            $sql = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_emails (
                title, emailcontent, send_id, send_cname, send_cemail, send_time, receiver_users, receiver_groups, status, sendstatus, sended
            ) VALUES (
                " . $db->quote($array['title']) . ",
                " . $db->quote(nv_editor_nl2br($array['emailcontent'])) . ",
                " . $admin_info['admin_id'] . ",
                " . $db->quote($array['send_cname']) . ",
                " . $db->quote($array['send_cemail']) . ",
                " . NV_CURRENTTIME . ",
                " . $db->quote(implode(',', array_keys($array['receiver_data']))) . ",
                " . $db->quote(implode(',', $array['group_id'])) . ",
                1,
                '',
                0
            )";

            $db_id = $db->insert_id($sql, 'id');
            if (!$db_id) {
                $error = 'Unknow Error while save new data';
            }
        } else {
            // Cập nhật thông tin
            $array['sendagain'] = $array['change_receiver'];
            $db_id = $id;

            $sql = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_emails SET
                title=" . $db->quote($array['title']) . ",
                emailcontent=" . $db->quote(nv_editor_nl2br($array['emailcontent'])) . ",
                send_cname=" . $db->quote($array['send_cname']) . ",
                send_cemail=" . $db->quote($array['send_cemail']) . ",
                edit_time=" . NV_CURRENTTIME . ",
                receiver_users=" . $db->quote(implode(',', array_keys($array['receiver_data']))) . ",
                receiver_groups=" . $db->quote(implode(',', $array['group_id'])) . ",
                sendstatus='',
                sended=0
            WHERE id=" . $id;
            $db->query($sql);
        }

        // Xử lý sau khi chỉnh sửa hoặc tạo mới CSDL
        if (empty($error)) {
            if ($array['sendagain']) {
                // Xóa thông tin gửi cũ
                $sql = "DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_emails_status WHERE email_id = " . $db_id;
                $db->query($sql);

                // Dữ liệu gửi cho các hội viên
                $array_send = [];
                foreach ($array['receiver_data'] as $row) {
                    $array_send[$row['userid']] = $row['email'];
                }

                // Dữ liệu gửi cho chi hội
                if (!empty($array['group_id'])) {
                    $where = [];
                    $where[] = "group_id IN(" . implode(',', $array['group_id']) . ")";
                    foreach ($array['group_id'] as $group_id) {
                        $where[] = "FIND_IN_SET(" . $group_id . ", in_groups)";
                    }
                    $sql = "SELECT userid, email FROM " . NV_USERS_GLOBALTABLE . " WHERE " . implode(' OR ', $where);
                    $result = $db->query($sql);
                    while ($row = $result->fetch()) {
                        $array_send[$row['userid']] = $row['email'];
                    }
                }

                // Lưu vào thông tin gửi email
                foreach ($array_send as $userid => $email) {
                    $sql = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_emails_status (
                        user_id, email, email_id, sentmail
                    ) VALUES (
                        " . $userid . ",'" . $email . "', " . $db_id . ", 0
                    )";
                    $db->insert_id($sql);
                }
            }

            if ($id) {
                nv_insert_logs(NV_LANG_DATA, $module_name, 'Edit email send', $db_id . ': ' . $array['title'], $admin_info['userid']);
            } else {
                nv_insert_logs(NV_LANG_DATA, $module_name, 'Send new email', $db_id . ': ' . $array['title'], $admin_info['userid']);
            }

            $nv_Cache->delMod($module_name);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=sendmail');
        }
    }
}

$xtpl = new XTemplate('sendmail-content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('FORM_ACTION', $form_action);
$xtpl->assign('SHOW_SEND_TO', (empty($id) or $array['change_receiver']) ? '' : ' hidden');

if (defined('NV_EDITOR')) {
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

$array['emailcontent'] = htmlspecialchars(nv_editor_br2nl($array['emailcontent']));

if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
    $array['emailcontent'] = nv_aleditor('emailcontent', '100%', '300px', $array['emailcontent']);
} else {
    $array['emailcontent'] = '<textarea class="form-control" rows="10" name="emailcontent">' . $array['emailcontent'] . '</textarea>';
}

$xtpl->assign('DATA', $array);

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

// Xuất thành viên nhận
foreach ($array['receiver_data'] as $receiver) {
    $xtpl->assign('RECEIVER', $receiver);
    $xtpl->parse('main.receiver');
}

// Xuất các nhóm quản lý
foreach ($array_group_managers as $group) {
    $group['checked'] = in_array($group['group_id'], $array['group_id']) ? ' checked="checked"' : '';
    $xtpl->assign('GROUP', $group);
    $xtpl->parse('main.group');
}

if ($id and !$array['change_receiver']) {
    $xtpl->parse('main.change_receiver');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
