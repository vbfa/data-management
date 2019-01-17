<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_ADMIN'))
    die('Stop!!!');

$allow_func = ['main', 'exportexcel', 'importexcel', 'sendmail', 'sendmail-content', 'sendmail-detail', 'report'];

if (defined('NV_IS_SPADMIN') or defined('NV_IS_GODADMIN')) {
    $submenu['permissions'] = $lang_module['permissions'];
    $allow_func[] = 'permissions';
    $submenu['setgroupcode'] = $lang_module['setgroupcode'];
    $allow_func[] = 'setgroupcode';
}

$submenu['exportexcel'] = $lang_module['exportexcel'];
$submenu['importexcel'] = $lang_module['importexcel'];
$submenu['sendmail'] = $lang_module['sendmail'];
$submenu['report'] = $lang_module['report'];
