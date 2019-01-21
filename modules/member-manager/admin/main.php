<?php

/**
 * @Project VBFA MEMBER-MANAGER
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: GNU/GPL version 2 or any later version
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['main'];

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php');
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

// Xác định và thông báo nhóm chưa thiết lập mã
$groups_list = nv_groups_list();
$group_missing_info = [];
foreach ($groups_list as $gid => $gtitle) {
    if ($gid > 9 and !isset($global_group_additions[$gid])) {
        $group_missing_info[] = $gtitle;
    }
}
if (!empty($group_missing_info) and (defined('NV_IS_SPADMIN') or defined('NV_IS_GODADMIN'))) {
    $xtpl->assign('GROUP_NO_INFO', sprintf($lang_module['main_have_noginfo'], implode(', ', $group_missing_info)));
    $xtpl->assign('GROUP_LINK_INFO', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=setgroupcode');
    $xtpl->parse('main.group_no_info');
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

if (empty($array_group_managers)) {
    $xtpl->parse('main.nogroup');
} else {
    // Danh sách các hội viên (tùy theo quyền mà được xem)
    $array_search = [];
    $array_search['q'] = $nv_Request->get_title('q', 'get', '');
    $array_search['branch'] = $nv_Request->get_int('b', 'get', 0);
    $array_search['group'] = $nv_Request->get_int('g', 'get', 0);
    $array_search['workplace'] = $nv_Request->get_title('wo', 'get', '');
    $array_search['studytime_from'] = $nv_Request->get_int('stf', 'get', 0);
    $array_search['studytime_to'] = $nv_Request->get_int('stt', 'get', 0);
    $array_search['edutype'] = $nv_Request->get_int('ed', 'get', 0);
    $array_search['concernarea'] = $nv_Request->get_int('co', 'get', 0);

    $per_page = 20;
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
    $page = $nv_Request->get_int('page', 'get', 1);
    if ($page < 1 or $page > 9999999) {
        $page = 1;
    }
    $isSearchAdv = false;

    $where = [];
    $where[] = "tb1.userid=tb2.userid";

    // Giới hạn thành viên trong các chi hội
    $where_or = [];
    $where_or[] = "tb1.group_id IN(" . implode(',', array_keys($array_group_managers)) . ")";
    foreach ($array_group_managers as $group) {
        $where_or[] = "FIND_IN_SET(" . $group['group_id'] . ", tb1.in_groups)";
    }
    $where[] = "(" . implode(' OR ', $where_or) . ")";

    // Tìm theo họ tên hội viên
    if (!empty($array_search['q'])) {
        $where[] = ($global_config['name_show'] == 0 ? "CONCAT(tb1.last_name,' ',tb1.first_name)" : "CONCAT(tb1.first_name,' ',tb1.last_name)") . " LIKE '%" . $db->dblikeescape($array_search['q']) . "%'";
        $base_url .= '&amp;q=' . urlencode($array_search['q']);
    }
    // Tìm theo ngành học
    if (!empty($array_search['branch']) and isset($array_branch[$array_search['branch']])) {
        $where[] = 'tb2.branch=' . $db->quote($array_search['branch']);
        $base_url .= '&amp;b=' . $array_search['branch'];
    }
    // Tìm theo chi hội
    if (!empty($array_search['group']) and isset($array_group_managers[$array_search['group']])) {
        $where[] = "(tb1.group_id=" . $array_search['group'] . " OR FIND_IN_SET(" . $array_search['group'] . ", tb1.in_groups))";
        $base_url .= '&amp;g=' . $array_search['group'];
    }
    // Tìm theo địa điểm làm việc hiện tại
    if (!empty($array_search['workplace'])) {
        $isSearchAdv = true;
        $where[] = "tb2.workplace LIKE '%" . $db->dblikeescape($array_search['workplace']) . "%'";
        $base_url .= '&amp;wo=' . urlencode($array_search['workplace']);
    }
    // Đào tạo từ
    if ($array_search['studytime_from'] > 0 and $array_search['studytime_from'] < 9999) {
        $isSearchAdv = true;
        $where[] = 'tb2.studytime_from>=' . $array_search['studytime_from'];
        $base_url .= '&amp;stf=' . $array_search['studytime_from'];
    }
    // Đào tạo đến
    if ($array_search['studytime_to'] > 0 and $array_search['studytime_to'] < 9999) {
        $isSearchAdv = true;
        $where[] = 'tb2.studytime_to<=' . $array_search['studytime_to'];
        $base_url .= '&amp;stt=' . $array_search['studytime_to'];
    }
    // Tìm theo loại hình đào tạo
    if (!empty($array_search['edutype']) and isset($array_edutype[$array_search['edutype']])) {
        $isSearchAdv = true;
        $where[] = 'tb2.edutype=' . $db->quote($array_search['edutype']);
        $base_url .= '&amp;ed=' . $array_search['edutype'];
    }
    // Tìm theo lĩnh vực quan tâm
    if (!empty($array_search['concernarea']) and isset($array_concernarea[$array_search['concernarea']])) {
        $isSearchAdv = true;
        $where[] = 'tb2.concernarea=' . $db->quote($array_search['concernarea']);
        $base_url .= '&amp;co=' . $array_search['concernarea'];
    }

    $db->sqlreset()->select('COUNT(tb1.userid)')->from(NV_USERS_GLOBALTABLE . " tb1, " . NV_USERS_GLOBALTABLE . "_info tb2");
    if (!empty($where)) {
        $db->where(implode(' AND ', $where));
    }

    $num_items = $db->query($db->sql())->fetchColumn();
    $db->order('tb1.userid DESC');
    $db->select('tb1.first_name, tb1.first_name, tb1.last_name, tb1.birthday, tb1.email, tb1.group_id, tb1.in_groups, tb2.*')->limit($per_page)->offset(($page - 1) * $per_page);
    $result = $db->query($db->sql());
    $sttStart = ($page - 1) * $per_page;
    while ($row = $result->fetch()) {
        $sttStart++;
        $row['stt'] = $sttStart;
        $row['full_name'] = nv_show_name_user($row['first_name'], $row['last_name']);

        if (isset($array_group_managers[$row['group_id']])) {
            $row['group'] = $array_group_managers[$row['group_id']]['group_title'];
        } else {
            $row['group'] = '';
            $in_groups = explode(',', $row['in_groups']);
            foreach ($in_groups as $group_id) {
                if (isset($array_group_managers[$group_id])) {
                    $row['group'] = $array_group_managers[$group_id]['group_title'];
                    break;
                }
            }
        }

        $branch = isset($array_branch[$row['branch']]) ? $array_branch[$row['branch']]['title'] : '';
        if (preg_match('/^[\-]+$/', $branch)) {
            $branch = '';
        }
        $row['branch'] = $branch;
        $belgiumschool = isset($array_belgiumschool[$row['belgiumschool']]) ? $array_belgiumschool[$row['belgiumschool']]['title'] : '';
        if (preg_match('/^[\-]+$/', $belgiumschool)) {
            $belgiumschool = '';
        }
        $row['belgiumschool'] = $belgiumschool;
        $vnschool = isset($array_vnschool[$row['vnschool']]) ? $array_vnschool[$row['vnschool']]['title'] : '';
        if (preg_match('/^[\-]+$/', $vnschool)) {
            $vnschool = '';
        }
        $row['vnschool'] = $vnschool;
        $edutype = isset($array_edutype[$row['edutype']]) ? $array_edutype[$row['edutype']]['title'] : '';
        if (preg_match('/^[\-]+$/', $edutype)) {
            $edutype = '';
        }
        $row['edutype'] = $edutype;
        $concernarea = isset($array_concernarea[$row['concernarea']]) ? $array_concernarea[$row['concernarea']]['title'] : '';
        if (preg_match('/^[\-]+$/', $concernarea)) {
            $concernarea = '';
        }
        $row['concernarea'] = $concernarea;

        $studytime = [];
        if (!empty($row['studytime_from'])) {
            $studytime[] = $row['studytime_from'];
        }
        if (!empty($row['studytime_to'])) {
            $studytime[] = $row['studytime_to'];
        }
        $row['studytime'] = implode(' - ', $studytime);
        $row['birthday'] = empty($row['birthday']) ? '' : nv_date('d/m/Y', $row['birthday']);
        $row['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=edit&amp;userid=' . $row['userid'];

        $xtpl->assign('ROW', $row);
        $xtpl->parse('main.data.loop');
    }

    $array_search['studytime_from'] = empty($array_search['studytime_from']) ? '' : $array_search['studytime_from'];
    $array_search['studytime_to'] = empty($array_search['studytime_to']) ? '' : $array_search['studytime_to'];

    $xtpl->assign('SEARCH', $array_search);
    $xtpl->assign('SEARCH_ADV', $isSearchAdv ? ' in' : '');

    foreach ($array_branch as $branch) {
        $branch['selected'] = $array_search['branch'] == $branch['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $branch['title'])) {
            $branch['title'] = $lang_module['search_other'];
        }
        $xtpl->assign('BRANCH', $branch);
        $xtpl->parse('main.data.branch');
    }

    foreach ($array_edutype as $edutype) {
        $edutype['selected'] = $array_search['edutype'] == $edutype['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $edutype['title'])) {
            $edutype['title'] = $lang_module['search_other'];
        }
        $xtpl->assign('EDUTYPE', $edutype);
        $xtpl->parse('main.data.edutype');
    }

    foreach ($array_concernarea as $concernarea) {
        $concernarea['selected'] = $array_search['concernarea'] == $concernarea['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $concernarea['title'])) {
            $concernarea['title'] = $lang_module['search_other'];
        }
        $xtpl->assign('CONCERNAREA', $concernarea);
        $xtpl->parse('main.data.concernarea');
    }

    foreach ($array_group_managers as $group) {
        $group['selected'] = $array_search['group'] == $group['group_id'] ? ' selected="selected"' : '';
        $xtpl->assign('GROUP', $group);
        $xtpl->parse('main.data.group');
    }

    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
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
