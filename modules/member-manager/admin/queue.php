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

$page_title = $lang_module['queue_page_title'];

$xtpl = new XTemplate('queue.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
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
    // Duyệt
    if ($nv_Request->isset_request('queueaccept', 'post')) {
        if (!defined('NV_IS_AJAX')) {
            nv_htmlOutput('Wrong URL');
        }

        $userid = $nv_Request->get_int('userid', 'post', 0);
        if (empty($userid)) {
            nv_htmlOutput('NO');
        }

        // Lấy được hội viên dựa trên $userid đảm bảo phải thuộc chi hội được quyền quản lý
        $sql = "SELECT tb1.*, tb2.username FROM " . $db_config['prefix'] . "_" . $module_data . "_queue tb1, " . NV_USERS_GLOBALTABLE . " tb2
        WHERE (tb2.group_id IN(" . implode(',', array_keys($array_group_managers)) . ")";
        foreach ($array_group_managers as $group) {
            $sql .= " OR FIND_IN_SET(" . $group['group_id'] . ", tb2.in_groups)";
        }
        $sql .= ") AND tb1.user_id=tb2.userid AND tb1.user_id=" . $userid;
        $user = $db->query($sql)->fetch();

        if (!empty($user)){
            //
            // TODO

            // Ghi nhật ký
            nv_insert_logs(NV_LANG_DATA, $module_data, 'Accept U Queue', $user['username'], $admin_info['userid']);
        }

        nv_htmlOutput('OK');
    }

    // Từ chối
    if ($nv_Request->isset_request('queuerefuse', 'post')) {
        if (!defined('NV_IS_AJAX')) {
            nv_htmlOutput('Wrong URL');
        }

        $userid = $nv_Request->get_int('userid', 'post', 0);
        if (empty($userid)) {
            nv_htmlOutput('NO');
        }

        // Lấy được hội viên dựa trên $userid đảm bảo phải thuộc chi hội được quyền quản lý
        $sql = "SELECT * FROM " . NV_USERS_GLOBALTABLE . " WHERE (group_id IN(" . implode(',', array_keys($array_group_managers)) . ")";
        foreach ($array_group_managers as $group) {
            $sql .= " OR FIND_IN_SET(" . $group['group_id'] . ", in_groups)";
        }
        $sql .= ") AND userid=" . $userid;
        $user = $db->query($sql)->fetch();

        if (!empty($user)){
            // Xóa bảng yêu cầu duyệt
            $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_queue WHERE user_id=" . $userid);

            // Ghi nhật ký
            nv_insert_logs(NV_LANG_DATA, $module_data, 'Delete U Queue', $user['username'], $admin_info['userid']);
        }

        nv_htmlOutput('OK');
    }

    // Xem chi tiết
    if ($nv_Request->isset_request('userid', 'get')) {
        $userid = $nv_Request->get_int('userid', 'get', 0);
        if (empty($userid)) {
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        }

        // Lấy được hội viên dựa trên $userid đảm bảo phải thuộc chi hội được quyền quản lý
        $sql = "SELECT tb1.*, tb2.username, tb2.first_name old_first_name, tb2.last_name old_last_name, tb2.birthday old_birthday, tb2.email old_email
        FROM " . $db_config['prefix'] . "_" . $module_data . "_queue tb1, " . NV_USERS_GLOBALTABLE . " tb2
        WHERE (tb2.group_id IN(" . implode(',', array_keys($array_group_managers)) . ")";
        foreach ($array_group_managers as $group) {
            $sql .= " OR FIND_IN_SET(" . $group['group_id'] . ", tb2.in_groups)";
        }
        $sql .= ") AND tb1.user_id=tb2.userid AND tb1.user_id=" . $userid;
        $user = $db->query($sql)->fetch();
        if (empty($user)) {
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        }
        $old_info = $db->query("SELECT * FROM " . NV_USERS_GLOBALTABLE . "_info WHERE userid=" . $userid)->fetch();
        if (empty($old_info)) {
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
        }

        $array_change = [];
        $user['full_name'] = nv_show_name_user($user['old_first_name'], $user['old_last_name'], $user['username']);

        // Xác định các trường có thay đổi hay không
        $array_change['first_name'] = empty($user['first_name']) ? '' : ' checked="checked"';
        $array_change['last_name'] = empty($user['last_name']) ? '' : ' checked="checked"';
        $array_change['birthday'] = empty($user['birthday']) ? '' : ' checked="checked"';
        $array_change['email'] = empty($user['email']) ? '' : ' checked="checked"';
        $array_change['workplace'] = empty($user['workplace']) ? '' : ' checked="checked"';
        $array_change['phone'] = empty($user['phone']) ? '' : ' checked="checked"';
        $array_change['belgiumschool'] = $user['belgiumschool'] == $old_info['belgiumschool'] ? '' : ' checked="checked"';

        $old_info['first_name'] = $user['old_first_name'];
        $old_info['last_name'] = $user['old_last_name'];
        $old_info['birthday'] = $user['old_birthday'];
        $old_info['email'] = $user['old_email'];
        $branch = isset($array_branch[$old_info['branch']]) ? $array_branch[$old_info['branch']]['title'] : '';
        if (preg_match('/^[\-]+$/', $branch)) {
            $branch = '';
        }
        $old_info['branch'] = $branch;
        $belgiumschool = isset($array_belgiumschool[$old_info['belgiumschool']]) ? $array_belgiumschool[$old_info['belgiumschool']]['title'] : '';
        if (preg_match('/^[\-]+$/', $belgiumschool)) {
            $belgiumschool = '';
        }
        $old_info['belgiumschool'] = $belgiumschool;
        $vnschool = isset($array_vnschool[$old_info['vnschool']]) ? $array_vnschool[$old_info['vnschool']]['title'] : '';
        if (preg_match('/^[\-]+$/', $vnschool)) {
            $vnschool = '';
        }
        $old_info['vnschool'] = $vnschool;
        $edutype = isset($array_edutype[$old_info['edutype']]) ? $array_edutype[$old_info['edutype']]['title'] : '';
        if (preg_match('/^[\-]+$/', $edutype)) {
            $edutype = '';
        }
        $old_info['edutype'] = $edutype;
        $concernarea = isset($array_concernarea[$old_info['concernarea']]) ? $array_concernarea[$old_info['concernarea']]['title'] : '';
        if (preg_match('/^[\-]+$/', $concernarea)) {
            $concernarea = '';
        }
        $old_info['concernarea'] = $concernarea;
        $old_info['birthday'] = $old_info['birthday'] ? nv_date('d/m/Y', $old_info['birthday']) : '';

        $user['birthday'] = $user['birthday'] ? nv_date('d/m/Y', $user['birthday']) : '';

        $xtpl->assign('DATA', $user);
        $xtpl->assign('OLDDATA', $old_info);
        $xtpl->assign('CHANGE', $array_change);

        foreach ($array_belgiumschool as $belgiumschool) {
            $belgiumschool['selected'] = $user['belgiumschool'] == $belgiumschool['id'] ? ' selected="selected"' : '';
            if (preg_match('/^[\-]+$/', $belgiumschool['title'])) {
                $belgiumschool['title'] = $lang_module['othervalues'];
            }
            $xtpl->assign('BELGIUMSCHOOL', $belgiumschool);
            $xtpl->parse('detail.belgiumschool');
        }

        $xtpl->parse('detail');
        $contents = $xtpl->text('detail');

        include NV_ROOTDIR . '/includes/header.php';
        echo nv_admin_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }

    // Danh sách các hội viên (tùy theo quyền mà được xem)
    $array_search = [];
    $array_search['q'] = $nv_Request->get_title('q', 'get', '');
    $array_search['group'] = $nv_Request->get_int('g', 'get', 0);

    $per_page = 20;
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
    $page = $nv_Request->get_int('page', 'get', 1);
    if ($page < 1 or $page > 9999999) {
        $page = 1;
    }

    $where = [];
    $where[] = "tb1.userid=tb2.userid";
    $where[] = "tb1.userid=tb3.user_id";

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
    // Tìm theo chi hội
    if (!empty($array_search['group']) and isset($array_group_managers[$array_search['group']])) {
        $where[] = "(tb1.group_id=" . $array_search['group'] . " OR FIND_IN_SET(" . $array_search['group'] . ", tb1.in_groups))";
        $base_url .= '&amp;g=' . $array_search['group'];
    }

    $db->sqlreset()->select('COUNT(tb1.userid)')->from(NV_USERS_GLOBALTABLE . " tb1, " . NV_USERS_GLOBALTABLE . "_info tb2, " . $db_config['prefix'] . "_" . $module_data . "_queue tb3");
    if (!empty($where)) {
        $db->where(implode(' AND ', $where));
    }

    $num_items = $db->query($db->sql())->fetchColumn();
    // Hội viên đã xác nhận email và cũ nhất lên trên nhằm đảm bảo ai yêu cầu trước thì sửa trước
    $db->order('tb3.status DESC, tb3.verificationtime ASC');
    $db->select('tb1.username, tb1.first_name, tb1.first_name, tb1.last_name, tb1.birthday, tb1.email, tb1.group_id, tb1.in_groups, tb2.*, tb3.status cstatus, tb3.verificationtime')
       ->limit($per_page)->offset(($page - 1) * $per_page);
    $result = $db->query($db->sql());
    $sttStart = ($page - 1) * $per_page;
    while ($row = $result->fetch()) {
        $sttStart++;
        $row['stt'] = $sttStart;
        $row['full_name'] = nv_show_name_user($row['first_name'], $row['last_name']);
        $row['cstatus'] = $lang_module['queue_status' . $row['cstatus']];
        $row['verificationtime'] = $row['verificationtime'] ? nv_date('H:i d/m/Y', $row['verificationtime']) : '';

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

        $row['link_view'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;userid=' . $row['userid'];

        $xtpl->assign('ROW', $row);
        $xtpl->parse('main.data.loop');
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
