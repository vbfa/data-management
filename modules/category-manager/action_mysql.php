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

$sql_drop_module = array();

// Bỏ comment đoạn sau để xóa dữ liệu module
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_branch";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_learning_tasks";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_edutype";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_belgiumschool";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_vnschool";
//$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_concernarea";

$sql_create_module = $sql_drop_module;

// Ngành học
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_branch (
 id SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
 title VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'Tiêu đề',
 description TEXT NOT NULL COMMENT 'Mô tả',
 status TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Hiển thị, ẩn',
 weight SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Thứ tự',
 PRIMARY KEY (id), UNIQUE (title(190))
) ENGINE=MyISAM;";

// Nhiệm vụ học tập
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_learning_tasks (
 id SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
 title VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'Tiêu đề',
 description TEXT NOT NULL COMMENT 'Mô tả',
 status TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Hiển thị, ẩn',
 weight SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Thứ tự',
 PRIMARY KEY (id), UNIQUE (title(190))
) ENGINE=MyISAM;";

// Loại hình đào tạo
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_edutype (
 id SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
 title VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'Tiêu đề',
 description TEXT NOT NULL COMMENT 'Mô tả',
 status TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Hiển thị, ẩn',
 weight SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Thứ tự',
 PRIMARY KEY (id), UNIQUE (title(190))
) ENGINE=MyISAM;";

// Trường đã học tại Bỉ
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_belgiumschool (
 id SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
 title VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'Tiêu đề',
 description TEXT NOT NULL COMMENT 'Mô tả',
 status TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Hiển thị, ẩn',
 weight SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Thứ tự',
 PRIMARY KEY (id), UNIQUE (title(190))
) ENGINE=MyISAM;";

// Trường đã học tại Việt Nam
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_vnschool (
 id SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
 title VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'Tiêu đề',
 description TEXT NOT NULL COMMENT 'Mô tả',
 status TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Hiển thị, ẩn',
 weight SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Thứ tự',
 PRIMARY KEY (id), UNIQUE (title(190))
) ENGINE=MyISAM;";

// Lĩnh vực quan tâm
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_concernarea (
 id SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
 title VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'Tiêu đề',
 description TEXT NOT NULL COMMENT 'Mô tả',
 status TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Hiển thị, ẩn',
 weight SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Thứ tự',
 PRIMARY KEY (id), UNIQUE (title(190))
) ENGINE=MyISAM;";
