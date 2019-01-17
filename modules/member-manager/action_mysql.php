<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Wed, 21 Nov 2018 01:58:33 GMT
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
 email varchar(250) NULL COMMENT 'Email sẽ gửi đi',
 email_id int(11) unsigned NOT NULL COMMENT 'ID của email trong bảng email',
 sentmail int(11) NOT NULL DEFAULT '0' COMMENT 'Thời gian gửi mail, nếu 0 thì là chưa gửi',
 isbusy tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Đang bận gửi',
 PRIMARY KEY (id),
 KEY user_id (user_id),
 KEY email_id (email_id),
 KEY isbusy (isbusy)
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
