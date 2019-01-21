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

$page_title = $lang_module['sendmail'];

// Kích hoạt, đình chỉ
if ($nv_Request->isset_request('changestatus', 'post')) {
    if (!defined('NV_IS_AJAX')) {
        nv_htmlOutput('Wrong URL');
    }

    $id = $nv_Request->get_int('id', 'post', 0);
    if (empty($id)) {
        nv_htmlOutput('NO');
    }

    $sql = "SELECT status FROM " . $db_config['prefix'] . "_" . $module_data . "_emails WHERE id=" . $id;
    // Không phải quản trị tối cao, điều hành chung thì chỉ thao tác email của mình
    if (!defined('NV_IS_GODADMIN') and !defined('NV_IS_SPADMIN')) {
        $sql .= ' AND send_id=' . $admin_info['admin_id'];
    }
    $result = $db->query($sql);
    $numrows = $result->rowCount();

    if ($numrows != 1) {
        nv_htmlOutput('NO');
    }

    $status = $result->fetchColumn();
    $status = $status ? 0 : 1;
    $sql = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_emails SET status=" . $status . " WHERE id=" . $id;
    $db->query($sql);

    nv_htmlOutput('OK');
}

// Xóa tin nhắn
if ($nv_Request->isset_request('delete', 'post')) {
    if (!defined('NV_IS_AJAX')) {
        nv_htmlOutput('Wrong URL');
    }

    $id = $nv_Request->get_int('id', 'post', 0);
    if (!$id) {
        nv_htmlOutput('NO');
    }

    $sql = "SELECT title FROM " . $db_config['prefix'] . "_" . $module_data . "_emails WHERE id=" . $id;
    // Không phải quản trị tối cao, điều hành chung thì chỉ thao tác email của mình
    if (!defined('NV_IS_GODADMIN') and !defined('NV_IS_SPADMIN')) {
        $sql .= ' AND send_id=' . $admin_info['admin_id'];
    }
    $result = $db->query($sql);
    $numrows = $result->rowCount();
    if ($numrows != 1) {
        nv_htmlOutput('NO');
    }

    $row = $result->fetch();

    // Xóa bảng email
    $sql = "DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_emails WHERE id=" . $id;
    $db->query($sql);

    // Xóa bảng gửi đi
    $sql = "DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_emails_status WHERE email_id=" . $id;
    $db->query($sql);

    $nv_Cache->delMod($module_name);
    nv_insert_logs(NV_LANG_DATA, $module_data, 'Delete Email Send', $row['title'], $admin_info['userid']);
    nv_htmlOutput('OK');
}

$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;

$db->sqlreset()->select('COUNT(id)')->from($db_config['prefix'] . "_" . $module_data . "_emails");

$where = [];
// Không phải quản trị tối cao, điều hành chung thì chỉ hiển thị email của mình
if (!defined('NV_IS_GODADMIN') and !defined('NV_IS_SPADMIN')) {
    $where[] = 'send_id=' . $admin_info['admin_id'];
}

if (!empty($where)) {
    $db->where(implode(' AND ', $where));
}

$num_items = $db->query($db->sql())->fetchColumn();
$db->select('*')->order('send_time DESC')->limit($per_page)->offset(($page - 1) * $per_page);

$array = [];
$array_userid = [];
$result = $db->query($db->sql());
while ($row = $result->fetch()) {
    $array[$row['id']] = $row;

    if (!empty($row['send_id'])) {
        $array_userid[] = $row['send_id'];
    }

    if (!empty($row['receiver_users'])) {
        $receiver_users = explode(',', $row['receiver_users']);
        $array_userid =  array_merge_recursive($array_userid, $receiver_users);
    }
}

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
$array_users = [];
$array_userid = array_unique($array_userid);
if (!empty($array_userid)) {
    $sql = "SELECT userid, username, first_name, last_name FROM " . NV_USERS_GLOBALTABLE . " WHERE userid IN(" . implode(',', $array_userid) . ")";
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['full_name'] = nv_show_name_user($row['first_name'], $row['last_name']);
        $array_users[$row['userid']] = $row;
    }
}

$xtpl = new XTemplate('sendmail.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('URL_SEND_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=sendmail-content');

// Xuất các email gửi
foreach ($array as $row) {
    $row['send_name'] = isset($array_users[$row['send_id']]) ? $array_users[$row['send_id']]['username'] : 'N/A';
    $row['send_time'] = nv_date('d/m/Y H:i', $row['send_time']);
    $row['status'] = $row['status'] ? ' checked="checked"' : '';
    $row['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=sendmail-content&amp;id=' . $row['id'];
    $row['link_detail'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=sendmail-detail&amp;id=' . $row['id'];

    // Xử lý trạng thái gửi
    $sql_count_all = "SELECT COUNT(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_emails_status WHERE email_id=" . $row['id'];
    $result_count_all = $db->query($sql_count_all);
    $count_all = $result_count_all->fetchColumn();
    $sql_count_all = "SELECT COUNT(*) FROM " . $db_config['prefix'] . "_" . $module_data . "_emails_status WHERE email_id=" . $row['id'] . " AND sentmail!=0";
    $result_count_all = $db->query($sql_count_all);
    $count_send = $result_count_all->fetchColumn();

    $row['email_send'] = number_format($count_send, 0, ',', '.') . " / " . number_format($count_all, 0, ',', '.');

    $xtpl->assign('ROW', $row);

    // Xử lý hội viên nhận
    $receiver_users = [];
    $row['receiver_users'] = empty($row['receiver_users']) ? [] : explode(',', $row['receiver_users']);
    foreach ($row['receiver_users'] as $userid) {
        if (isset($array_users[$userid])) {
            $receiver_users[] = nv_strtoupper($array_users[$userid]['username']);
        }
    }
    if (!empty($receiver_users)) {
        $xtpl->assign('RECEIVER_USERS', implode(', ', $receiver_users));
        $xtpl->parse('main.loop.receiver_users');
    }

    // Xử lý chi hội nhận
    $receiver_groups = [];
    $row['receiver_groups'] = empty($row['receiver_groups']) ? [] : explode(',', $row['receiver_groups']);
    foreach ($row['receiver_groups'] as $group_id) {
        if (isset($global_group_additions[$group_id])) {
            $receiver_groups[] = $global_group_additions[$group_id]['group_code'];
        }
    }
    if (!empty($receiver_groups)) {
        $xtpl->assign('RECEIVER_GROUPS', implode(', ', $receiver_groups));
        $xtpl->parse('main.loop.receiver_groups');
    }


    $xtpl->parse('main.loop');
}

// Phân trang
if (!empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
