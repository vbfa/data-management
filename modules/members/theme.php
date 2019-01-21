<?php

/**
 * @Project VBFA MEMBER-MANAGER
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: GNU/GPL version 2 or any later version
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_IS_MOD_MEMBERS')) {
    die('Stop!!!');
}

/**
 * @param array $array
 * @param string $generate_page
 * @param array $array_search
 * @return string
 */
function nv_theme_members_list($array, $generate_page, $array_search)
{
    global $lang_module, $lang_global, $module_info, $array_branch, $array_groups, $module_name, $global_config;

    $xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('SEARCH', $array_search);

    if (!$global_config['rewrite_enable']) {
        $xtpl->assign('FORM_ACTION', NV_BASE_SITEURL . 'index.php');
        $xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
        $xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
        $xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
        $xtpl->assign('MODULE_NAME', $module_name);
        $xtpl->parse('main.no_rewrite');
    } else {
        $xtpl->assign('FORM_ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name);
    }

    foreach ($array_branch as $branch) {
        $branch['selected'] = $array_search['branch'] == $branch['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $branch['title'])) {
            $branch['title'] = $lang_module['search_other'];
        }
        $xtpl->assign('BRANCH', $branch);
        $xtpl->parse('main.branch');
    }

    foreach ($array_groups as $group) {
        $group['selected'] = $array_search['group'] == $group['group_id'] ? ' selected="selected"' : '';
        $xtpl->assign('GROUP', $group);
        $xtpl->parse('main.group');
    }

    foreach ($array as $row) {
        $xtpl->assign('ROW', $row);

        if (empty($row['contactinfo'])) {
            $xtpl->parse('main.loop.noinfo');
        }

        $xtpl->parse('main.loop');
    }

    // Phân trang
    if (!empty($generate_page)) {
        $xtpl->assign('GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.generate_page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}
