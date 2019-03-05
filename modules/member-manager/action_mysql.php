<?php

/**
 * @Project VBFA MEMBER-MANAGER
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: GNU/GPL version 2 or any later version
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_MAINFILE'))
    die('Stop!!!');

$sql_drop_module = [];

// Tìm cron của module và xóa
$result = $db->query("SELECT id, params FROM " . NV_CRONJOBS_GLOBALTABLE . " ORDER BY id DESC");
$is_auto = 0;
while (list($id, $params) = $result->fetch(3)) {
    $params = (!empty($params)) ? array_map("trim", explode(",", $params)) : array("", 0);
    if ($params[0] == $module_data) {
        $is_auto = $id;
        break;
    }
}

if ($is_auto) {
    //$db->query("DELETE FROM " . NV_CRONJOBS_GLOBALTABLE . " WHERE id=" . $is_auto);
}

// Bỏ comment đoạn sau để xóa dữ liệu module
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permissions";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_group_addition";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_user_addition";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_emails";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_emails_status";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_queue";

$sql_create_module = $sql_drop_module;
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permissions (
 admin_id mediumint(8) UNSIGNED NOT NULL,
 permission TEXT NOT NULL COMMENT 'Quyền dưới dạng json_encode',
 last_update int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cập nhật lần cuối',
 PRIMARY KEY (admin_id)
) ENGINE=InnoDB;";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_group_addition (
 group_id mediumint(8) UNSIGNED NOT NULL,
 group_code varchar(10) NOT NULL,
 last_update int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cập nhật lần cuối',
 PRIMARY KEY (group_id),
 UNIQUE KEY group_code (group_code)
) ENGINE=InnoDB;";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_user_addition (
 user_id mediumint(8) UNSIGNED NOT NULL,
 user_code varchar(10) NOT NULL,
 last_update int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cập nhật lần cuối',
 PRIMARY KEY (user_id),
 UNIQUE KEY user_code (user_code)
) ENGINE=InnoDB;";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_emails (
 id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 title varchar(255) NOT NULL DEFAULT '' COMMENT 'Tiêu đề email',
 emailcontent text NOT NULL COMMENT 'Nội dung email',
 send_id int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ID tài khoản gửi',
 send_cname varchar(255) NOT NULL DEFAULT '' COMMENT 'Tên người gửi trong email',
 send_cemail varchar(255) NOT NULL DEFAULT '' COMMENT 'Email người gửi để gửi mail',
 send_time int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Thời gian gửi',
 edit_time int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Thời gian sửa lần cuối',
 receiver_users TEXT NOT NULL COMMENT 'Hội viên nhận',
 receiver_groups VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Chi hội nhận',
 status tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0: đình chỉ, 1: hoạt động',
 sendstatus varchar(255) NOT NULL DEFAULT '' COMMENT 'Trạng thái gửi',
 sended tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Đã gửi mail xong hay chưa',
 PRIMARY KEY (id),
 KEY send_id (send_id)
) ENGINE=InnoDB;";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_emails_status (
 id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 user_id mediumint(8) unsigned NOT NULL COMMENT 'ID hội viên nhận',
 email varchar(250) NOT NULL COMMENT 'Email sẽ gửi đi',
 email_id int(11) unsigned NOT NULL COMMENT 'ID của email trong bảng email',
 sentmail int(11) NOT NULL DEFAULT '0' COMMENT 'Thời gian gửi mail, nếu 0 thì là chưa gửi',
 isbusy tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Đang bận gửi',
 PRIMARY KEY (id),
 KEY user_id (user_id),
 KEY email_id (email_id),
 KEY isbusy (isbusy)
) ENGINE=InnoDB;";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_queue (
 user_id mediumint(8) unsigned NOT NULL COMMENT 'ID hội viên sửa',
 checknum varchar(250) NOT NULL COMMENT 'Mã kiểm tra',
 first_name varchar(100) NOT NULL DEFAULT '',
 last_name varchar(100) NOT NULL DEFAULT '',
 birthday int(11) unsigned NOT NULL DEFAULT '0',
 email varchar(100) NOT NULL DEFAULT '' COMMENT 'Thay đổi email mới',
 workplace varchar(250) NOT NULL DEFAULT '' COMMENT 'Cơ quan làm việc hiện tại',
 phone varchar(250) NOT NULL DEFAULT '' COMMENT 'Điện thoại',
 belgiumschool varchar(250) NOT NULL DEFAULT '' COMMENT 'Trường đã học tại Bỉ',
 vnschool smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Trường đã học tại Việt Nam',
 course varchar(250) NOT NULL DEFAULT '' COMMENT 'Khóa',
 studytime_from smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Thời gian bắt đầu học',
 studytime_to smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Thời gian kết thúc học',
 learningtasks varchar(250) NOT NULL DEFAULT '' COMMENT 'Nhiệm vụ học tập',
 othernote text NOT NULL COMMENT 'Ghi chú',
 edutype smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Loại hình đào tạo',
 address varchar(250) NOT NULL DEFAULT '' COMMENT 'Địa chỉ liên hệ',
 fb_twitter varchar(250) NOT NULL DEFAULT '' COMMENT 'FB/Twitter',
 contactsocial varchar(250) NOT NULL DEFAULT '' COMMENT 'Zalo/Messenger/Viber',
 branch varchar(250) NOT NULL DEFAULT '' COMMENT 'Ngành học',
 concernarea varchar(250) NOT NULL DEFAULT '' COMMENT 'Lĩnh vực quan tâm',
 contactinfo text NOT NULL COMMENT 'Thông tin liên hệ',
 editreason text NOT NULL COMMENT 'Lý do thay đổi',
 lastupdate int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Lần chỉnh sửa cuối',
 status tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0: Chưa xác nhận, 1: Đã xác nhận',
 verificationtime int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Thời gian xác nhận email',
 PRIMARY KEY (user_id),
 KEY checknum (checknum),
 KEY status (status),
 KEY verificationtime (verificationtime)
) ENGINE=InnoDB;";

// Xác định các trường dữ liệu theo ngôn ngữ trong bảng cron và thêm cron gửi email
$result = $db->query("SHOW COLUMNS FROM " . NV_CRONJOBS_GLOBALTABLE);
$list_field = array();
$list_value = array();
while ($row = $result->fetch()) {
    if (preg_match("/^([a-zA-Z0-9]{2})\_cron_name$/", $row['field'])) {
        $list_field[] = $row['field'];
        $list_value[] = "'Auto sendmail to members'";
    }
}

$list_field = ", " . implode(", ", $list_field);
$list_value = ", " . implode(", ", $list_value);

$sql_create_module[] = "INSERT IGNORE INTO " . NV_CRONJOBS_GLOBALTABLE . " (
    id, start_time, inter_val, run_file, run_func, params, del, is_sys, act, last_time, last_result " . $list_field . "
) VALUES (NULL, " . NV_CURRENTTIME . ", 5, 'nv_send_email_to_members.php', 'cron_send_email_to_members', '" . $module_data . ", 5', 0, 0, 1, " . NV_CURRENTTIME . ", 1 " . $list_value . " )";
