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
