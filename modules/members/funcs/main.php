<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Apr 20, 2010 10:47:41 AM
 */

if (!defined('NV_IS_MOD_MEMBERS')) {
    die('Stop!!!');
}

$page_title = $module_info['site_title'];
$key_words = $module_info['keywords'];

$array_search = [];
$array_search['q'] = $nv_Request->get_title('q', 'get', '');
$array_search['branch'] = $nv_Request->get_int('b', 'get', 0);
$array_search['group'] = $nv_Request->get_int('g', 'get', 0);

$per_page = 20;
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$page = $nv_Request->get_int('page', 'get', 1);
if ($page < 1 or $page > 9999999) {
    $page = 1;
}

$where = [];
$where[] = "tb1.userid=tb2.userid";

// Giới hạn thành viên trong các chi hội
if (!empty($array_groups)) {
    $where_or = [];
    $where_or[] = "tb1.group_id IN(" . implode(',', array_keys($array_groups)) . ")";
    foreach ($array_groups as $group) {
        $where_or[] = "FIND_IN_SET(" . $group['group_id'] . ", tb1.in_groups)";
    }
    $where[] = "(" . implode(' OR ', $where_or) . ")";
}

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
if (!empty($array_search['group']) and isset($array_groups[$array_search['group']])) {
    $where[] = "(tb1.group_id=" . $array_search['group'] . " OR FIND_IN_SET(" . $array_search['group'] . ", tb1.in_groups))";
    $base_url .= '&amp;g=' . $array_search['group'];
}

$db->sqlreset()->select('COUNT(tb1.userid)')->from(NV_USERS_GLOBALTABLE . " tb1, " . NV_USERS_GLOBALTABLE . "_info tb2");
if (!empty($where)) {
    $db->where(implode(' AND ', $where));
}

$array = [];
$num_items = $db->query($db->sql())->fetchColumn();
$db->select('tb1.first_name, tb1.first_name, tb1.last_name, tb1.birthday, tb1.email, tb1.group_id, tb1.in_groups, tb2.*')->limit($per_page)->offset(($page - 1) * $per_page);
$result = $db->query($db->sql());
$sttStart = ($page - 1) * $per_page;
while ($row = $result->fetch()) {
    $sttStart++;
    $row['stt'] = $sttStart;
    $row['full_name'] = nv_show_name_user($row['first_name'], $row['last_name']);

    if (isset($array_groups[$row['group_id']])) {
        $row['group'] = $array_groups[$row['group_id']]['group_title'];
    } else {
        $row['group'] = '';
        $in_groups = explode(',', $row['in_groups']);
        foreach ($in_groups as $group_id) {
            if (isset($array_groups[$group_id])) {
                $row['group'] = $array_groups[$group_id]['group_title'];
                break;
            }
        }
    }

    $branch = isset($array_branch[$row['branch']]) ? $array_branch[$row['branch']]['title'] : '';
    if (preg_match('/^[\-]+$/', $branch)) {
        $branch = '';
    }
    $row['branch'] = $branch;

    $studytime = [];
    if (!empty($row['studytime_from'])) {
        $studytime[] = $row['studytime_from'];
    }
    if (!empty($row['studytime_to'])) {
        $studytime[] = $row['studytime_to'];
    }
    $row['studytime'] = implode(' - ', $studytime);

    $array[$row['userid']] = $row;
}

if ($page > 1 and empty($array)) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name);
}

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
$contents = nv_theme_members_list($array, $generate_page, $array_search);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
