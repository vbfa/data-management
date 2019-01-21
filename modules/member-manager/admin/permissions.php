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

$page_title = $lang_module['permissions'];
$groups_list = nv_groups_list();

// Xác định admin của module
$array_modadmin = [];
if (!empty($sys_mods[$module_name]['admins'])) {
    $sql = "SELECT tb1.admin_id, tb2.username, tb2.first_name, tb2.last_name FROM " . NV_AUTHORS_GLOBALTABLE . " tb1, " . NV_USERS_GLOBALTABLE . " tb2
    WHERE tb1.admin_id=tb2.userid AND tb1.lev=3 AND tb1.admin_id IN(" . $sys_mods[$module_name]['admins'] . ")";
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['full_name'] = nv_show_name_user($row['first_name'], $row['last_name']);
        $array_modadmin[$row['admin_id']] = $row;
    }
}

// Submit dữ liệu
if ($nv_Request->isset_request('submit', 'post')) {
    $permissions = $nv_Request->get_array('admin', 'post', []);

    // Xóa thiết lập phân quyền
    $sql = "TRUNCATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_permissions";
    $db->query($sql);

    foreach ($permissions as $admin_id => $glists) {
        $sql = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_permissions (admin_id, permission, last_update) VALUES (
            " . $admin_id . ", " . $db->quote(json_encode($glists)) . ", " . NV_CURRENTTIME . "
        )";
        $db->query($sql);
    }

    $nv_Cache->delMod($module_name);
    nv_insert_logs(NV_LANG_DATA, $module_name, 'Change Permissions', '', $admin_info['userid']);
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
}

$xtpl = new XTemplate('permissions.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

if (empty($array_modadmin)) {
    $xtpl->parse('main.no_admin');
} else {
    foreach ($groups_list as $gid => $gname) {
        if ($gid > 9) {
            $xtpl->assign('GROUP', [
                'key' => $gid,
                'title' => $gname
            ]);
            $xtpl->parse('main.data.group');
        }
    }

    foreach ($array_modadmin as $madmin) {
        $xtpl->assign('ADMIN', $madmin);

        foreach ($groups_list as $gid => $gname) {
            if ($gid > 9) {
                $xtpl->assign('GROUP', [
                    'key' => $gid,
                    'title' => $gname,
                    'checked' => (!empty($global_permissions[$madmin['admin_id']]) and in_array($gid, $global_permissions[$madmin['admin_id']])) ? ' checked="checked"' : ''
                ]);
                $xtpl->parse('main.data.admin.group');
            }
        }

        $xtpl->parse('main.data.admin');
    }

    $xtpl->parse('main.data');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
