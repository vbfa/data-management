<?php

/**
 * @Project VBFA MEMBER-MANAGER
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: GNU/GPL version 2 or any later version
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

define('NV_IS_FILE_ADMIN', true);
define('NV_CMNG_TABLE', $db_config['prefix'] . '_category_manager');
define('NV_CMNG_MODULE', 'category-manager');

/**
 * @param string $table
 * @param string $value
 * @return number
 */
function newCategoryManagerItems($table, $value)
{
    global $db, $nv_Cache, $admin_info, $module_name;

    $array_tables = [
        'belgiumschool' => 'belgiumschool',
        'learningtasks' => 'learning_tasks',
        'branch' => 'branch',
        'concernarea' => 'concernarea'
    ];

    if (!isset($array_tables[$table]) or empty($value)) {
        return 0;
    }

    // Tìm trong CSDL với $value trả về ID
    $sql = "SELECT id FROM " . NV_CMNG_TABLE . "_" . $array_tables[$table] . " WHERE title=" . $db->quote($value);
    $exists_id = $db->query($sql)->fetchColumn();
    if (!empty($exists_id)) {
        return $exists_id;
    }

    // Thêm mới và trả về ID
    try {
        $weight = $db->query('SELECT max(weight) FROM ' . NV_CMNG_TABLE . '_' . $array_tables[$table])->fetchColumn();
        $weight = intval($weight) + 1;

        $sql = "INSERT INTO " . NV_CMNG_TABLE . "_" . $array_tables[$table] . " (title, description, weight) VALUES (
            " . $db->quote($value) . ", '', " . $weight . "
        )";

        $new_id = $db->insert_id($sql, 'id');

        if ($new_id) {
            $nv_Cache->delMod(NV_CMNG_MODULE);
            nv_insert_logs(NV_LANG_DATA, $module_name, 'Quick add ' . $table, $value, $admin_info['userid']);
        }

        return intval($new_id);
    } catch (PDOException $e) {
        return 0;
    }
}

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

// Xác định các trường học tại Việt Nam
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_vnschool ORDER BY weight ASC";
$array_vnschool = $nv_Cache->db($sql, 'id', NV_CMNG_MODULE);

// Xác định các loại hình đào tạo
$array_edutype = [];
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_edutype ORDER BY weight ASC";
$array_edutype = $nv_Cache->db($sql, 'id', NV_CMNG_MODULE);

// Xác định các Lĩnh Vực quan tâm
$array_concernarea = [];
$sql = "SELECT id, title FROM " . NV_CMNG_TABLE . "_concernarea ORDER BY weight ASC";
$array_concernarea = $nv_Cache->db($sql, 'id', NV_CMNG_MODULE);
