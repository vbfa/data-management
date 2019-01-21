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

$page_title = $lang_module['sendmaildl'];

// Xác định email gửi
$id = $nv_Request->get_int('id', 'get', 0);
$sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $module_data . "_emails WHERE id=" . $id;
if (!defined('NV_IS_GODADMIN') and !defined('NV_IS_SPADMIN')) {
    $sql .= " AND send_id=" . $admin_info['admin_id'];
}
$array_info = $db->query($sql)->fetch();
if (empty($array_info)) {
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=sendmail');
}

$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;id=' . $id;

$db->sqlreset()->select('COUNT(tb1.id)')->from($db_config['prefix'] . "_" . $module_data . "_emails_status tb1, " . NV_USERS_GLOBALTABLE . " tb2");

$where = [];
$where[] = 'tb1.user_id=tb2.userid';
$where[] = 'tb1.email_id=' . $id;
if (!empty($where)) {
    $db->where(implode(' AND ', $where));
}

$num_items = $db->query($db->sql())->fetchColumn();
$db->select('tb1.id, tb1.sentmail, tb2.userid, tb2.username, tb2.first_name, tb2.last_name, tb1.email')->order('tb1.sentmail ASC')->limit($per_page)->offset(($page - 1) * $per_page);

$array = [];
$result = $db->query($db->sql());
while ($row = $result->fetch()) {
    $array[$row['id']] = $row;
}

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);

$xtpl = new XTemplate('sendmail-detail.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('DATA', $array_info);
$xtpl->assign('URL_BACK', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=sendmail');

// Xuất các email gửi
foreach ($array as $row) {
    $row['full_name'] = nv_show_name_user($row['first_name'], $row['last_name']);
    $row['username'] = nv_strtoupper($row['username']);
    $row['sentmail'] = $row['sentmail'] != 0 ? nv_date('d/m/Y H:i', abs($row['sentmail'])) : '';
    if ($row['sentmail'] < 0) {
        $row['status'] = $lang_module['sendmaildl_st2'];
    } elseif ($row['sentmail'] > 0) {
        $row['status'] = $lang_module['sendmaildl_st1'];
        } else {
        $row['status'] = $lang_module['sendmaildl_st0'];
    }
    $xtpl->assign('ROW', $row);

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
