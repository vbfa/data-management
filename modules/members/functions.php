<?php

/**
 * @Project VBFA MEMBER-MANAGER
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: GNU/GPL version 2 or any later version
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_SYSTEM')) {
    die('Stop!!!');
}

define('NV_IS_MOD_MEMBERS', true);
define('NV_CMNG_TABLE', $db_config['prefix'] . '_category_manager');
define('NV_CMNG_MODULE', 'category-manager');
define('NV_MMNG_TABLE', $db_config['prefix'] . '_member_manager');
define('NV_MMNG_MODULE', 'member-manager');

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

// Xác định mã của nhóm và các thông tin mở rộng khác
$sql = "SELECT * FROM " . NV_MMNG_TABLE . "_group_addition";
$global_group_additions = $nv_Cache->db($sql, 'group_id', NV_MMNG_MODULE);

// Xác định các chi hội (Bỏ các nhóm hệ thống)
$cache_file = NV_LANG_DATA . '_groups_list_new_' . NV_CACHE_PREFIX . '.cache';
if (($cache = $nv_Cache->getItem('users', $cache_file)) != false) {
    $groups_list = unserialize($cache);
} else {
    $groups_list = [];
    $result = $db->query('SELECT group_id, title, idsite FROM ' . NV_USERS_GLOBALTABLE . '_groups WHERE (idsite = ' . $global_config['idsite'] . ' OR (idsite =0 AND siteus = 1)) ORDER BY idsite, weight');
    while ($row = $result->fetch()) {
        if ($row['group_id'] > 9) {
            $groups_list[$row['group_id']] = ($global_config['idsite'] > 0 and empty($row['idsite'])) ? '<strong>' . $row['title'] . '</strong>' : $row['title'];
        }
    }
    $nv_Cache->setItem('users', $cache_file, serialize($groups_list));
}

$array_groups = [];
foreach ($groups_list as $gid => $gtitle) {
    $array_groups[$gid] = [
        'group_id' => $gid,
        'group_title' => $gtitle,
        'group_code' => isset($global_group_additions[$gid]) ? $global_group_additions[$gid]['group_code'] : ''
    ];
}

unset($groups_list);
