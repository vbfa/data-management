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
            // Kiểm tra trước email trùng
            if (!empty($user['email'])) {
                $check = $db->query("SELECT userid FROM " . NV_USERS_GLOBALTABLE . " WHERE email=" . $db->quote($user['email']) . " AND userid!=" . $userid)->fetchColumn();
                if ($check) {
                    nv_htmlOutput(sprintf($lang_module['queue_error_exemail1'], $user['email']));
                }
            }

            // Cập nhật thông tin cơ bản
            $sql = "UPDATE " . NV_USERS_GLOBALTABLE . " SET
                first_name=" . $db->quote($user['first_name']) . ",
                last_name=" . $db->quote($user['last_name']) . ",
                birthday=" . $user['birthday'] . (!empty($user['email']) ? (', email=' . $db->quote($user['email'])) : '') . "
            WHERE userid=" . $userid;
            $db->query($sql);

            // Cập nhật thông tin tùy biến
            $sql = "UPDATE " . NV_USERS_GLOBALTABLE . "_info SET
                workplace=" . $db->quote($user['workplace']) . ",
                phone=" . $db->quote($user['phone']) . ",
                course=" . $db->quote($user['course']) . ",
                othernote=" . $db->quote($user['othernote']) . ",
                address=" . $db->quote($user['address']) . ",
                fb_twitter=" . $db->quote($user['fb_twitter']) . ",
                contactsocial=" . $db->quote($user['contactsocial']) . ",
                branch=" . $db->quote($user['branch']) . ",
                learningtasks=" . $db->quote($user['learningtasks']) . ",
                belgiumschool=" . $db->quote($user['belgiumschool']) . ",
                edutype=" . $db->quote($user['edutype']) . ",
                studytime_from=" . $user['studytime_from'] . ",
                studytime_to=" . $user['studytime_to'] . ",
                vnschool=" . $db->quote($user['vnschool']) . ",
                concernarea=" . $db->quote($user['concernarea']) . ",
                contactinfo=" . $db->quote($user['contactinfo']) . "
            WHERE userid=" . $userid;
            $db->query($sql);

            // Xóa bảng yêu cầu duyệt
            $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_queue WHERE user_id=" . $userid);

            // Xóa cache
            $nv_Cache->delMod($module_name);
            $nv_Cache->delMod('users');

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

        if (!defined('NV_EDITOR')) {
            define('NV_EDITOR', 'ckeditor');
        }
        require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';

        $error = '';
        $array_change = [];

        // Submit dữ liệu
        if ($nv_Request->isset_request('submit', 'post')) {
            $user['email'] = nv_substr($nv_Request->get_title('email', 'post', ''), 0, 250);
            $user['first_name'] = nv_substr($nv_Request->get_title('first_name', 'post', ''), 0, 100);
            $user['last_name'] = nv_substr($nv_Request->get_title('last_name', 'post', ''), 0, 100);
            $user['birthday'] = $nv_Request->get_string('birthday', 'post', '');
            $user['workplace'] = nv_substr($nv_Request->get_title('workplace', 'post', ''), 0, 250);
            $user['phone'] = nv_substr($nv_Request->get_title('phone', 'post', ''), 0, 250);
            $user['belgiumschool'] = $nv_Request->get_int('belgiumschool', 'post', 0);
            $user['vnschool'] = $nv_Request->get_int('vnschool', 'post', 0);
            $user['course'] = nv_substr($nv_Request->get_title('course', 'post', ''), 0, 250);
            $user['studytime'] = nv_substr($nv_Request->get_title('studytime', 'post', ''), 0, 250);
            $user['learningtasks'] = $nv_Request->get_int('learningtasks', 'post', 0);
            $user['othernote'] = $nv_Request->get_textarea('othernote', '', NV_ALLOWED_HTML_TAGS);
            $user['edutype'] = $nv_Request->get_int('edutype', 'post', 0);
            $user['address'] = nv_substr($nv_Request->get_title('address', 'post', ''), 0, 250);
            $user['fb_twitter'] = nv_substr($nv_Request->get_title('fb_twitter', 'post', ''), 0, 250);
            $user['contactsocial'] = nv_substr($nv_Request->get_title('contactsocial', 'post', ''), 0, 250);
            $user['branch'] = $nv_Request->get_int('branch', 'post', 0);
            $user['concernarea'] = $nv_Request->get_int('concernarea', 'post', 0);
            $user['contactinfo'] = $nv_Request->get_editor('contactinfo', '', NV_ALLOWED_HTML_TAGS);

            $user['studytime_from'] = 0;
            $user['studytime_to'] = 0;

            $array_change = $nv_Request->get_typed_array('change', 'post', 'int', []);

            // Chuẩn hóa ngày tháng
            if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $user['birthday'], $m)) {
                $user['birthday'] = mktime(0, 0, 0, intval($m[2]), intval($m[1]), intval($m[3]));
            } else {
                $user['birthday'] = 0;
            }

            // Kiểm tra chuẩn hóa dữ liệu
            if (empty($error)) {
                if (!empty($user['studytime'])) {
                    if (!preg_match('/^([0-9]+)[\s]*\-[\s]*([0-9]+)$/', $user['studytime'], $m) or $m[1] > $m[2]) {
                        $error = $lang_module['queue_error_studytime'];
                    } else {
                        $user['studytime'] = $m[1] . ' - ' . $m[2];
                        $user['studytime_from'] = $m[1];
                        $user['studytime_to'] = $m[2];
                    }
                }
            }

            // Nếu thay đổi email thì email bắt buộc và không trùng với email nào khác
            if (!empty($array_change['email']) and empty($error)) {
                if (empty($user['email'])) {
                    $error = $lang_module['queue_error_noemail'];
                } elseif (($check = nv_check_valid_email($user['email'])) != '') {
                    $error = $check;
                } else {
                    $check = $db->query("SELECT userid FROM " . NV_USERS_GLOBALTABLE . " WHERE email=" . $db->quote($user['email']) . " AND userid!=" . $userid)->fetchColumn();
                    if ($check) {
                        $error = sprintf($lang_module['queue_error_exemail'], $user['email']);
                    }
                }
            }

            if (empty($error)) {
                // Cập nhật các thông tin cơ bản
                $sql = [];
                if (!empty($array_change['last_name'])) {
                    $sql['last_name'] = $db->quote($user['last_name']);
                }
                if (!empty($array_change['first_name'])) {
                    $sql['first_name'] = $db->quote($user['first_name']);
                }
                if (!empty($array_change['birthday'])) {
                    $sql['birthday'] = $user['birthday'];
                }
                if (!empty($array_change['email'])) {
                    $sql['email'] = $db->quote($user['email']);
                }
                if (!empty($sql)) {
                    $ressql = [];
                    foreach ($sql as $__k => $__v) {
                        $ressql[] = $__k . '=' . $__v;
                    }
                    $ressql = "UPDATE " . NV_USERS_GLOBALTABLE . " SET " . implode(', ', $ressql) . " WHERE userid=" . $userid;
                    $db->query($ressql);
                }

                // Cập nhật các thông tin tùy chỉnh
                $sql = [];
                if (!empty($array_change['workplace'])) {
                    $sql['workplace'] = $db->quote($user['workplace']);
                }
                if (!empty($array_change['phone'])) {
                    $sql['phone'] = $db->quote($user['phone']);
                }
                if (!empty($array_change['course'])) {
                    $sql['course'] = $db->quote($user['course']);
                }
                if (!empty($array_change['othernote'])) {
                    $sql['othernote'] = $db->quote(nv_nl2br($user['othernote']));
                }
                if (!empty($array_change['address'])) {
                    $sql['address'] = $db->quote($user['address']);
                }
                if (!empty($array_change['fb_twitter'])) {
                    $sql['fb_twitter'] = $db->quote($user['fb_twitter']);
                }
                if (!empty($array_change['contactsocial'])) {
                    $sql['contactsocial'] = $db->quote($user['contactsocial']);
                }
                if (!empty($array_change['branch'])) {
                    $sql['branch'] = $db->quote($user['branch']);
                }
                if (!empty($array_change['learningtasks'])) {
                    $sql['learningtasks'] = $db->quote($user['learningtasks']);
                }
                if (!empty($array_change['belgiumschool'])) {
                    $sql['belgiumschool'] = $db->quote($user['belgiumschool']);
                }
                if (!empty($array_change['edutype'])) {
                    $sql['edutype'] = $db->quote($user['edutype']);
                }
                if (!empty($array_change['studytime'])) {
                    $sql['studytime_from'] = $user['studytime_from'];
                    $sql['studytime_to'] = $user['studytime_to'];
                }
                if (!empty($array_change['vnschool'])) {
                    $sql['vnschool'] = $db->quote($user['vnschool']);
                }
                if (!empty($array_change['concernarea'])) {
                    $sql['concernarea'] = $db->quote($user['concernarea']);
                }
                if (!empty($array_change['contactinfo'])) {
                    $sql['contactinfo'] = $db->quote(nv_editor_nl2br($user['contactinfo']));
                }
                if (!empty($sql)) {
                    $ressql = [];
                    foreach ($sql as $__k => $__v) {
                        $ressql[] = $__k . '=' . $__v;
                    }
                    $ressql = "UPDATE " . NV_USERS_GLOBALTABLE . "_info SET " . implode(', ', $ressql) . " WHERE userid=" . $userid;
                    $db->query($ressql);
                }

                // Xóa đi thông tin chờ duyệt
                $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_queue WHERE user_id=" . $userid);

                // Ghi nhật ký
                nv_insert_logs(NV_LANG_DATA, $module_data, 'Accept U Queue Detail', $user['username'], $admin_info['userid']);

                // Xóa cache
                $nv_Cache->delMod($module_name);
                $nv_Cache->delMod('users');

                // Duyển về danh sách chờ duyệt
                nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
            }
        } else {
            $user['studytime'] = [];
            if ($user['studytime_from'] > 0) {
                $user['studytime'][] = $user['studytime_from'];
            }
            if ($user['studytime_to'] > 0) {
                $user['studytime'][] = $user['studytime_to'];
            }
            $user['studytime'] = implode(' - ', $user['studytime']);

            // Xác định các trường có thay đổi hay không
            $array_change['first_name'] = empty($user['first_name']) ? 0 : 1;
            $array_change['last_name'] = empty($user['last_name']) ? 0 : 1;
            $array_change['birthday'] = empty($user['birthday']) ? 0 : 1;
            $array_change['email'] = empty($user['email']) ? 0 : 1;
            $array_change['workplace'] = empty($user['workplace']) ? 0 : 1;
            $array_change['phone'] = empty($user['phone']) ? 0 : 1;
            $array_change['belgiumschool'] = $user['belgiumschool'] == $old_info['belgiumschool'] ? 0 : 1;
            $array_change['vnschool'] = $user['vnschool'] == $old_info['vnschool'] ? 0 : 1;
            $array_change['course'] = empty($user['course']) ? 0 : 1;
            $array_change['studytime'] = empty($user['studytime']) ? 0 : 1;
            $array_change['learningtasks'] = $user['learningtasks'] == $old_info['learningtasks'] ? 0 : 1;
            $array_change['othernote'] = empty($user['othernote']) ? 0 : 1;
            $array_change['edutype'] = $user['edutype'] == $old_info['edutype'] ? 0 : 1;
            $array_change['address'] = empty($user['address']) ? 0 : 1;
            $array_change['fb_twitter'] = empty($user['fb_twitter']) ? 0 : 1;
            $array_change['contactsocial'] = empty($user['contactsocial']) ? 0 : 1;
            $array_change['branch'] = $user['branch'] == $old_info['branch'] ? 0 : 1;
            $array_change['concernarea'] = $user['concernarea'] == $old_info['concernarea'] ? 0 : 1;
            $array_change['contactinfo'] = empty($user['contactinfo']) ? 0 : 1;
        }

        $user['full_name'] = nv_show_name_user($user['old_first_name'], $user['old_last_name'], $user['username']);

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
        $learningtasks = isset($array_learningtasks[$old_info['learningtasks']]) ? $array_learningtasks[$old_info['learningtasks']]['title'] : '';
        if (preg_match('/^[\-]+$/', $learningtasks)) {
            $learningtasks = '';
        }
        $old_info['learningtasks'] = $learningtasks;
        $old_info['birthday'] = $old_info['birthday'] ? nv_date('d/m/Y', $old_info['birthday']) : '';
        $old_info['studytime'] = [];
        if ($old_info['studytime_from'] > 0) {
            $old_info['studytime'][] = $old_info['studytime_from'];
        }
        if ($old_info['studytime_to'] > 0) {
            $old_info['studytime'][] = $old_info['studytime_to'];
        }
        $old_info['studytime'] = implode(' - ', $old_info['studytime']);

        $user['birthday'] = $user['birthday'] ? nv_date('d/m/Y', $user['birthday']) : '';
        $user['editreason'] = nv_htmlspecialchars($user['editreason']);
        $user['othernote'] = nv_htmlspecialchars($user['othernote']);
        $user['contactinfo'] = nv_aleditor('contactinfo', '100%', '150px', nv_htmlspecialchars($user['contactinfo']), 'Basic');

        $array_change['first_name'] = empty($array_change['first_name']) ? '' : ' checked="checked"';
        $array_change['last_name'] = empty($array_change['last_name']) ? '' : ' checked="checked"';
        $array_change['birthday'] = empty($array_change['birthday']) ? '' : ' checked="checked"';
        $array_change['email'] = empty($array_change['email']) ? '' : ' checked="checked"';
        $array_change['workplace'] = empty($array_change['workplace']) ? '' : ' checked="checked"';
        $array_change['phone'] = empty($array_change['phone']) ? '' : ' checked="checked"';
        $array_change['belgiumschool'] = empty($array_change['belgiumschool']) ? '' : ' checked="checked"';
        $array_change['vnschool'] = empty($array_change['vnschool']) ? '' : ' checked="checked"';
        $array_change['course'] = empty($array_change['course']) ? '' : ' checked="checked"';
        $array_change['studytime'] = empty($array_change['studytime']) ? '' : ' checked="checked"';
        $array_change['learningtasks'] = empty($array_change['learningtasks']) ? '' : ' checked="checked"';
        $array_change['othernote'] = empty($array_change['othernote']) ? '' : ' checked="checked"';
        $array_change['edutype'] = empty($array_change['edutype']) ? '' : ' checked="checked"';
        $array_change['address'] = empty($array_change['address']) ? '' : ' checked="checked"';
        $array_change['fb_twitter'] = empty($array_change['fb_twitter']) ? '' : ' checked="checked"';
        $array_change['contactsocial'] = empty($array_change['contactsocial']) ? '' : ' checked="checked"';
        $array_change['branch'] = empty($array_change['branch']) ? '' : ' checked="checked"';
        $array_change['concernarea'] = empty($array_change['concernarea']) ? '' : ' checked="checked"';
        $array_change['contactinfo'] = empty($array_change['contactinfo']) ? '' : ' checked="checked"';

        $xtpl->assign('DATA', $user);
        $xtpl->assign('OLDDATA', $old_info);
        $xtpl->assign('CHANGE', $array_change);
        $xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;userid=' . $userid);

        foreach ($array_belgiumschool as $belgiumschool) {
            $belgiumschool['selected'] = $user['belgiumschool'] == $belgiumschool['id'] ? ' selected="selected"' : '';
            if (preg_match('/^[\-]+$/', $belgiumschool['title'])) {
                $belgiumschool['title'] = $lang_module['othervalues'];
            }
            $xtpl->assign('BELGIUMSCHOOL', $belgiumschool);
            $xtpl->parse('detail.belgiumschool');
        }

        foreach ($array_vnschool as $vnschool) {
            $vnschool['selected'] = $user['vnschool'] == $vnschool['id'] ? ' selected="selected"' : '';
            if (preg_match('/^[\-]+$/', $vnschool['title'])) {
                $vnschool['title'] = $lang_module['othervalues'];
            }
            $xtpl->assign('VNSCHOOL', $vnschool);
            $xtpl->parse('detail.vnschool');
        }

        foreach ($array_learningtasks as $learningtasks) {
            $learningtasks['selected'] = $user['learningtasks'] == $learningtasks['id'] ? ' selected="selected"' : '';
            if (preg_match('/^[\-]+$/', $learningtasks['title'])) {
                $learningtasks['title'] = $lang_module['othervalues'];
            }
            $xtpl->assign('LEARNINGTASKS', $learningtasks);
            $xtpl->parse('detail.learningtasks');
        }

        foreach ($array_edutype as $edutype) {
            $edutype['selected'] = $user['edutype'] == $edutype['id'] ? ' selected="selected"' : '';
            if (preg_match('/^[\-]+$/', $edutype['title'])) {
                $edutype['title'] = $lang_module['othervalues'];
            }
            $xtpl->assign('EDUTYPE', $edutype);
            $xtpl->parse('detail.edutype');
        }

        foreach ($array_branch as $branch) {
            $branch['selected'] = $user['branch'] == $branch['id'] ? ' selected="selected"' : '';
            if (preg_match('/^[\-]+$/', $branch['title'])) {
                $branch['title'] = $lang_module['othervalues'];
            }
            $xtpl->assign('BRANCH', $branch);
            $xtpl->parse('detail.branch');
        }

        foreach ($array_concernarea as $concernarea) {
            $concernarea['selected'] = $user['concernarea'] == $concernarea['id'] ? ' selected="selected"' : '';
            if (preg_match('/^[\-]+$/', $concernarea['title'])) {
                $concernarea['title'] = $lang_module['othervalues'];
            }
            $xtpl->assign('CONCERNAREA', $concernarea);
            $xtpl->parse('detail.concernarea');
        }

        if (!empty($error)) {
            $xtpl->assign('ERROR', $error);
            $xtpl->parse('detail.error');
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
