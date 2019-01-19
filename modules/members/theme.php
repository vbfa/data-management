<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Jul 11, 2010 8:43:46 PM
 */

if (!defined('NV_IS_MOD_MEMBERS')) {
    die('Stop!!!');
}

function nv_theme_members_list($array, $generate_page, $array_search)
{
    global $lang_module, $lang_global, $module_info, $array_branch, $array_groups;

    $xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('SEARCH', $array_search);

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
