<?php

/**
 * @Project VBFA MEMBER-MANAGER
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: GNU/GPL version 2 or any later version
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_IS_MOD_MEMBER_MANAGER')) {
    die('Stop!!!');
}

$page_title = $lang_module['main_title'];
$key_words = $module_info['keywords'];

// Xác nhận thông tin chỉnh sửa
if (isset($array_op[0]) and $array_op[0] == 'active') {
    $userid = $nv_Request->get_int('userid', 'get', 0, 1);
    $checknum = $nv_Request->get_title('checknum', 'get', '', 1);

    if (empty($userid) or empty($checknum)) {
        nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
    }

    $sql = "SELECT tb2.userid, tb2.group_id, tb2.username, tb2.email, tb2.first_name, tb2.last_name, tb2.in_groups, tb1.*
    FROM " . $db_config['prefix'] . "_" . $module_data . "_queue tb1, " . NV_USERS_GLOBALTABLE . " tb2 WHERE tb1.user_id=" . $userid . " AND tb1.user_id=tb2.userid";
    $row = $db->query($sql)->fetch();

    if (empty($row) or !empty($row['status'])) {
        nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
    }

    if ($checknum == $row['checknum']) {
        // Cập nhật trạng thái
        $sql = "UPDATE " . $db_config['prefix'] . "_" . $module_data . "_queue SET status=1, verificationtime=" . NV_CURRENTTIME . " WHERE user_id=" . $userid;
        $db->query($sql);

        // Xác định chi hội
        // Xác định từ username là chính xác nhất
        $group_id = 0;
        if (preg_match('/^([a-zA-Z]{3})[0-9]{5}$/i', $row['username'], $m)) {
            $group_code = strtoupper($m[1]);
            foreach ($global_group_additions as $group) {
                if ($group['group_code'] == $group_code) {
                    $group_id = $group['group_id'];
                    break;
                }
            }
        }
        // Xác định từ group_id, in_groups
        if (empty($group_id)) {
            $_group_ids = explode(',', $row['in_groups']);
            $_group_ids[] = $row['group_id'];
            foreach ($_group_ids as $_group_id) {
                if (isset($global_group_additions[$_group_id])) {
                    $group_id = $_group_id;
                    break;
                }
            }
        }

        if (!empty($group_id)) {
            // Xác định danh sách admin của chi hội
            $array_group_admins = [];
            if (!empty($sys_mods[$module_name]['admins'])) {
                $sql = "SELECT tb1.admin_id, tb2.username, tb2.first_name, tb2.last_name, tb2.email FROM " . NV_AUTHORS_GLOBALTABLE . " tb1, " . NV_USERS_GLOBALTABLE . " tb2
                WHERE tb1.admin_id=tb2.userid AND tb1.lev=3 AND tb1.admin_id IN(" . $sys_mods[$module_name]['admins'] . ")";
                $result = $db->query($sql);
                while ($_row = $result->fetch()) {
                    if (isset($global_permissions[$_row['admin_id']]) and in_array($group_id, $global_permissions[$_row['admin_id']])) {
                        $_row['full_name'] = nv_show_name_user($_row['first_name'], $_row['last_name'], $_row['username']);
                        $array_group_admins[$_row['admin_id']] = $_row;
                    }
                }
            }

            // Gửi email cho các admin
            foreach ($array_group_admins as $admin) {
                $full_name = nv_show_name_user($row['first_name'], $row['last_name'], $row['username']);
                $subject = $lang_module['admin_email_subject'];
                $link = NV_MY_DOMAIN . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=queue&amp;userid=' . $row['userid'];
                $message = sprintf($lang_module['admin_email_content'], $admin['full_name'], $full_name, $global_config['site_name'], $link);
                nv_sendmail($global_config['site_email'], $admin['email'], $subject, $message);
            }

            // Thông báo kích hoạt thành công
            $url_back = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA, true);
            $contents = nv_theme_alert('', $lang_module['active_success'], 'info', $url_back, $lang_module['back_to_home'], 10);

            include NV_ROOTDIR . '/includes/header.php';
            echo nv_site_theme($contents);
            include NV_ROOTDIR . '/includes/footer.php';
        }
    }

    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

if (!defined('NV_EDITOR')) {
    define('NV_EDITOR', 'ckeditor');
}
require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';

$array = [];
$error = '';
$isSubmit = false;

if ($nv_Request->isset_request('submit', 'post')) {
    $isSubmit = true;
    $array['email'] = nv_substr($nv_Request->get_title('email', 'post', ''), 0, 250);
    $array['first_name'] = nv_substr($nv_Request->get_title('first_name', 'post', ''), 0, 100);
    $array['last_name'] = nv_substr($nv_Request->get_title('last_name', 'post', ''), 0, 100);
    $array['birthday'] = $nv_Request->get_string('birthday', 'post', '');
    $array['new_email'] = nv_substr($nv_Request->get_title('new_email', 'post', ''), 0, 100);
    $array['workplace'] = nv_substr($nv_Request->get_title('workplace', 'post', ''), 0, 250);
    $array['phone'] = nv_substr($nv_Request->get_title('phone', 'post', ''), 0, 250);
    $array['belgiumschool'] = $nv_Request->get_int('belgiumschool', 'post', 0);
    $array['vnschool'] = $nv_Request->get_int('vnschool', 'post', 0);
    $array['course'] = nv_substr($nv_Request->get_title('course', 'post', ''), 0, 250);
    $array['studytime'] = nv_substr($nv_Request->get_title('studytime', 'post', ''), 0, 250);
    $array['learningtasks'] = $nv_Request->get_int('learningtasks', 'post', 0);
    $array['othernote'] = $nv_Request->get_textarea('othernote', '', NV_ALLOWED_HTML_TAGS);
    $array['edutype'] = $nv_Request->get_int('edutype', 'post', 0);
    $array['address'] = nv_substr($nv_Request->get_title('address', 'post', ''), 0, 250);
    $array['fb_twitter'] = nv_substr($nv_Request->get_title('fb_twitter', 'post', ''), 0, 250);
    $array['contactsocial'] = nv_substr($nv_Request->get_title('contactsocial', 'post', ''), 0, 250);
    $array['branch'] = $nv_Request->get_int('branch', 'post', 0);
    $array['concernarea'] = $nv_Request->get_int('concernarea', 'post', 0);
    $array['contactinfo'] = $nv_Request->get_editor('contactinfo', '', NV_ALLOWED_HTML_TAGS);
    $array['editreason'] = $nv_Request->get_textarea('editreason', '', NV_ALLOWED_HTML_TAGS);

    $array['studytime_from'] = 0;
    $array['studytime_to'] = 0;

    // Chuẩn hóa ngày tháng
    if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $array['birthday'], $m)) {
        $array['birthday'] = mktime(0, 0, 0, intval($m[2]), intval($m[1]), intval($m[3]));
    } else {
        $array['birthday'] = 0;
    }

    // Tìm kiếm email
    if (empty($array['email'])) {
        $error = $lang_module['error_email'];
    } elseif (($check = nv_check_valid_email($array['email'])) != '') {
        $error = $check;
    } else {
        $sql = "SELECT userid, group_id, username, email, first_name, last_name, in_groups FROM " . NV_USERS_GLOBALTABLE . " WHERE email=:email AND active=1";
        $sth = $db->prepare($sql);
        $sth->bindParam(':email', $array['email'], PDO::PARAM_STR);
        $sth->execute();
        $user = $sth->fetch();
        if (empty($user)) {
            $error = $lang_module['error_email_match_user'];
        }
    }

    // Kiểm tra chuẩn hóa dữ liệu
    if (empty($error)) {
        if (!empty($array['studytime'])) {
            if (!preg_match('/^([0-9]+)[\s]*\-[\s]*([0-9]+)$/', $array['studytime'], $m) or $m[1] > $m[2]) {
                $error = $lang_module['error_studytime'];
            } else {
                $array['studytime'] = $m[1] . ' - ' . $m[2];
                $array['studytime_from'] = $m[1];
                $array['studytime_to'] = $m[2];
            }
        }
    }

    // Kiểm tra nếu nhập email mới thì phải chuẩn
    if (empty($error) and !empty($array['new_email']) and ($check = nv_check_valid_email($array['new_email'])) != '') {
        $error = $check;
    }

    // Kiểm tra captcha
    if (empty($error) and !nv_capcha_txt(($global_config['captcha_type'] == 2 ? $nv_Request->get_title('g-recaptcha-response', 'post', '') : $nv_Request->get_title('code', 'post', '')))) {
        $error = ($global_config['captcha_type'] == 2 ? $lang_global['securitycodeincorrect1'] : $lang_global['securitycodeincorrect']);
    }

    if (empty($error)) {
        if (!isset($array_belgiumschool[$array['belgiumschool']])) {
            $array['belgiumschool'] = 0;
        }
        if (!isset($array_vnschool[$array['vnschool']])) {
            $array['vnschool'] = 0;
        }
        if (!isset($array_learningtasks[$array['learningtasks']])) {
            $array['learningtasks'] = 0;
        }
        if (!isset($array_edutype[$array['edutype']])) {
            $array['edutype'] = 0;
        }
        if (!isset($array_branch[$array['branch']])) {
            $array['branch'] = 0;
        }
        if (!isset($array_concernarea[$array['concernarea']])) {
            $array['concernarea'] = 0;
        }

        // Xóa trong CSDL nếu có
        $sql = "DELETE FROM " . $db_config['prefix'] . "_" . $module_data . "_queue WHERE user_id=" . $user['userid'];
        $db->query($sql);

        // Thêm vào CSDL
        $checknum = sha1(nv_genpass(20));
        $sql = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_queue (
            user_id, checknum, first_name, last_name, birthday, email, workplace, phone, belgiumschool, vnschool, course, studytime_from, studytime_to,
            learningtasks, othernote, edutype, address, fb_twitter, contactsocial, branch, concernarea, contactinfo,
            editreason, lastupdate, status
        ) VALUES (
            " . $user['userid'] . ", '" . $checknum . "', :first_name, :last_name, " . $array['birthday'] . ", :email, :workplace, :phone, " . $array['belgiumschool'] . ",
            " . $array['vnschool'] . ", :course, " . $array['studytime_from'] . ", " . $array['studytime_to'] . ",
            " . $array['learningtasks'] . ", :othernote, " . $array['edutype'] . ", :address, :fb_twitter, :contactsocial,
            " . $array['branch'] . ", " . $array['branch'] . ", :contactinfo, :editreason, " . NV_CURRENTTIME . ", 0
        )";
        $sth = $db->prepare($sql);
        $sth->bindParam(':first_name', $array['first_name'], PDO::PARAM_STR);
        $sth->bindParam(':last_name', $array['last_name'], PDO::PARAM_STR);
        $sth->bindParam(':email', $array['new_email'], PDO::PARAM_STR);
        $sth->bindParam(':workplace', $array['workplace'], PDO::PARAM_STR);
        $sth->bindParam(':phone', $array['phone'], PDO::PARAM_STR);
        $sth->bindParam(':course', $array['course'], PDO::PARAM_STR);
        $sth->bindParam(':othernote', $array['othernote'], PDO::PARAM_STR);
        $sth->bindParam(':address', $array['address'], PDO::PARAM_STR);
        $sth->bindParam(':fb_twitter', $array['fb_twitter'], PDO::PARAM_STR);
        $sth->bindParam(':contactsocial', $array['contactsocial'], PDO::PARAM_STR);
        $sth->bindParam(':contactinfo', $array['contactinfo'], PDO::PARAM_STR);
        $sth->bindParam(':editreason', $array['editreason'], PDO::PARAM_STR);
        $sth->execute();

        // Gửi email xác nhận
        $full_name = nv_show_name_user($user['first_name'], $user['last_name'], $user['username']);
        $subject = $lang_module['email_subject'];
        $message = sprintf($lang_module['email_content'], $full_name, $global_config['site_name'], NV_MY_DOMAIN . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=active&amp;userid=' . $user['userid'] . '&checknum=' . $checknum, true));
        $send = nv_sendmail($global_config['site_email'], $user['email'], $subject, $message);

        if ($send) {
            $info = $lang_module['email_success'];
        } else {
            $info = $lang_module['email_error'];
        }

        $url_back = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA, true);
        $contents = nv_theme_alert('', $info, 'info', $url_back, $lang_module['back_to_home'], 10);

        include NV_ROOTDIR . '/includes/header.php';
        echo nv_site_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }
} else {
    $array['email'] = '';
    $array['first_name'] = '';
    $array['last_name'] = '';
    $array['birthday'] = 0;
    $array['new_email'] = '';
    $array['workplace'] = '';
    $array['phone'] = '';
    $array['belgiumschool'] = 0;
    $array['vnschool'] = 0;
    $array['course'] = '';
    $array['studytime'] = '';
    $array['learningtasks'] = 0;
    $array['othernote'] = '';
    $array['edutype'] = 0;
    $array['address'] = '';
    $array['fb_twitter'] = '';
    $array['contactsocial'] = '';
    $array['branch'] = 0;
    $array['concernarea'] = 0;
    $array['contactinfo'] = '';
    $array['editreason'] = '';
}

$array['editreason'] = nv_htmlspecialchars($array['editreason']);
$array['othernote'] = nv_htmlspecialchars($array['othernote']);
$array['contactinfo'] = nv_aleditor('contactinfo', '100%', '150px', nv_htmlspecialchars($array['contactinfo']), 'Basic');

$contents = nv_theme_member_edit($array, $error, $isSubmit);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
