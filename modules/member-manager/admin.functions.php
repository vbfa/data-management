<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN'))
    die('Stop!!!');

define('NV_IS_FILE_ADMIN', true);
define('NV_CMNG_TABLE', $db_config['prefix'] . '_category_manager');
define('NV_CMNG_MODULE', 'category-manager');

// Xác định phân quyền
$global_permissions = [];
$sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $module_data . "_permissions";
$list = $nv_Cache->db($sql, 'admin_id', $module_name);
foreach ($list as $row) {
    $global_permissions[$row['admin_id']] = empty($row['permission']) ? [] : json_decode($row['permission'], true);
}
unset($list, $row);

// Xác định mã của nhóm và các thông tin mở rộng khác
$global_group_additions = [];
$sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $module_data . "_group_addition";
$list = $nv_Cache->db($sql, 'group_id', $module_name);
foreach ($list as $row) {
    $global_group_additions[$row['group_id']] = $row;
}
unset($list, $row);

// Xác định các ngành học
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_branch ORDER BY weight ASC";
$array_branch = $nv_Cache->db($sql, 'id', NV_CMNG_MODULE);

// Xác định các nhiệm vụ học tập
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_learning_tasks ORDER BY weight ASC";
$array_learningtasks = $nv_Cache->db($sql, 'id', NV_CMNG_MODULE);

// Xác định các trường học tại Bỉ
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_belgiumschool ORDER BY weight ASC";
$array_belgiumschool = $nv_Cache->db($sql, 'id', NV_CMNG_MODULE);

// Xác định các loại hình đào tạo
$array_edutype = [];
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_edutype ORDER BY weight ASC";
$array_edutype = $nv_Cache->db($sql, 'id', NV_CMNG_MODULE);
