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

$page_title = $lang_module['setgroupcode'];
$groups_list = nv_groups_list();
$error = [];
$array = [];

// Submit dữ liệu
if ($nv_Request->isset_request('submit', 'post')) {
    $array = $nv_Request->get_array('groupcode', 'post', []);

    foreach ($array as $gid => $code) {
        if (!isset($groups_list[$gid])) {
            $error[] = sprintf($lang_module['setgroupcode_error_isset'], $gid);
        } elseif (!empty($code) and !preg_match('/^[a-zA-Z]{3}$/i', $code)) {
            $error[] = sprintf($lang_module['setgroupcode_error_rule'], $code);
        }
    }

    if (empty($error)) {
        foreach ($array as $gid => $code) {
            if (isset($groups_list[$gid]) and !empty($code)) {
                $code = nv_strtoupper($code);
                try {
                    if (!isset($global_group_additions[$gid])) {
                        // Thêm mới
                        $sql = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_group_addition (group_id, group_code, last_update) VALUES (
                            " . $gid . ",
                            " . $db->quote($code) . ",
                            " . NV_CURRENTTIME . "
                        )";
                        $db->query($sql);
                    } elseif (defined('NV_IS_GODADMIN')) {
                        // Sửa
                        $sql = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_group_addition SET
                            group_code = " . $db->quote($code) . ",
                            last_update = " . NV_CURRENTTIME . "
                        WHERE group_id=" . $gid;
                        $db->query($sql);
                    }
                } catch (PDOException $e) {
                    trigger_error(print_r($e, true));
                }
            }
        }

        $nv_Cache->delMod($module_name);
        nv_insert_logs(NV_LANG_DATA, $module_name, 'Change Group Code', '', $admin_info['userid']);
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
}

$xtpl = new XTemplate('setgroupcode.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

foreach ($groups_list as $gid => $gtitle) {
    if ($gid > 9) {
        $xtpl->assign('ROW', [
            'key' => $gid,
            'title' => $gtitle,
            'value' => isset($array[$gid]) ? nv_htmlspecialchars($array[$gid]) : (isset($global_group_additions[$gid]) ? $global_group_additions[$gid]['group_code'] : '')
        ]);
        if (defined('NV_IS_GODADMIN') or !isset($global_group_additions[$gid])) {
            $xtpl->parse('main.loop.edit');
        } else {
            $xtpl->parse('main.loop.text');
        }
        $xtpl->parse('main.loop');
    }
}

if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
} else {
    $xtpl->parse('main.info');
    $xtpl->parse('main.info2');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
