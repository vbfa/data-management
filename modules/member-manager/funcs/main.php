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
// TODO

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
            user_id, checknum, workplace, phone, belgiumschool, vnschool, course, studytime_from, studytime_to,
            learningtasks, othernote, edutype, address, fb_twitter, contactsocial, branch, concernarea, contactinfo,
            editreason, lastupdate, status
        ) VALUES (
            " . $user['userid'] . ", '" . $checknum . "', :workplace, :phone, " . $array['belgiumschool'] . ",
            " . $array['vnschool'] . ", :course, " . $array['studytime_from'] . ", " . $array['studytime_to'] . ",
            " . $array['learningtasks'] . ", :othernote, " . $array['edutype'] . ", :address, :fb_twitter, :contactsocial,
            " . $array['branch'] . ", " . $array['branch'] . ", :contactinfo, :editreason, " . NV_CURRENTTIME . ", 0
        )";
        $sth = $db->prepare($sql);
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

        $contents = nv_theme_alert('', $info);

        include NV_ROOTDIR . '/includes/header.php';
        echo nv_site_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }
} else {
    $array['email'] = '';
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
