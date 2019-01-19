<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

$page_title = $lang_module['exportexcel'];

// Kiểm tra thư viện Excel
if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
    trigger_error('No class PhpSpreadsheet', 256);
}

// Xác định các nhóm được quyền quản lý
$array_group_managers = [];
$groups_list = nv_groups_list();
foreach ($groups_list as $gid => $gtitle) {
    if ($gid > 9 and isset($global_group_additions[$gid]) and (defined('NV_IS_GODADMIN') or defined('NV_IS_SPADMIN') or (
        isset($global_permissions[$admin_info['admin_id']]) and in_array($gid, $global_permissions[$admin_info['admin_id']])))
        ) {
            $array_group_managers[$gid] = [
                'group_id' => $gid,
                'group_title' => $gtitle,
                'group_code' => $global_group_additions[$gid]['group_code']
            ];
        }
}
$array_setting = [
    'group_id' => [],
    'from' => 0,
    'to' => 0,
    'branch' => [],
    'learningtasks' => []
];

// Submit form
if ($nv_Request->isset_request('submit', 'post')) {
    $array_setting['group_id'] = $nv_Request->get_typed_array('group_id', 'post', 'int', []);
    $array_setting['branch'] = $nv_Request->get_typed_array('branch', 'post', 'int', []);
    $array_setting['learningtasks'] = $nv_Request->get_typed_array('learningtasks', 'post', 'int', []);

    $array_setting['from'] = $nv_Request->get_string('from', 'post', '');
    if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $array_setting['from'], $m)) {
        $array_setting['from'] = mktime(0, 0, 0, intval($m[2]), intval($m[1]), intval($m[3]));
    } else {
        $array_setting['from'] = 0;
    }
    $array_setting['to'] = $nv_Request->get_string('to', 'post', '');
    if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $array_setting['to'], $m)) {
        $array_setting['to'] = mktime(23, 59, 59, intval($m[2]), intval($m[1]), intval($m[3]));
    } else {
        $array_setting['to'] = 0;
    }

    $fileName = NV_ROOTDIR . '/modules/' . $module_file . '/excel-export-template.xlsx';
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileName);
    $sheet = $spreadsheet->getActiveSheet();
    $line = 2;

    // Ghi dữ liệu
    $array_setting['group_id'] = array_intersect($array_setting['group_id'], array_keys($array_group_managers));
    if (!empty($array_setting['group_id'])) {
        if ($sys_info['allowed_set_time_limit']) {
            set_time_limit(0);
        }
        if ($sys_info['ini_set_support']) {
            $memoryLimitMB = (integer) ini_get('memory_limit');
            if ($memoryLimitMB < 1024) {
                ini_set("memory_limit", "1024M");
            }
        }

        $where = [];
        $where[] = "tb1.userid=tb2.userid";
        $where_or = [];
        $where_or[] = "tb1.group_id IN(" . implode(',', $array_setting['group_id']) . ")";
        foreach ($array_setting['group_id'] as $group_id) {
            $where_or[] = "FIND_IN_SET(" . $group_id . ", tb1.in_groups)";
        }
        $where[] = "(" . implode(' OR ', $where_or) . ")";

        if (!empty($array_setting['from'])) {
            $where[] = "tb1.regdate>=" . $array_setting['from'];
        }
        if (!empty($array_setting['to'])) {
            $where[] = "tb1.regdate<=" . $array_setting['to'];
        }

        $array_setting['branch'] = array_intersect($array_setting['branch'], array_keys($array_branch));
        if (!empty($array_setting['branch']) and sizeof($array_setting['branch']) < sizeof($array_branch)) {
            $where_or = [];
            foreach ($array_setting['branch'] as $value) {
                $where_or[] = 'tb2.branch=' . $db->quote($value);
            }
            $where[] = "(" . implode(' OR ', $where_or) . ")";
        }

        $array_setting['learningtasks'] = array_intersect($array_setting['learningtasks'], array_keys($array_learningtasks));
        if (!empty($array_setting['learningtasks']) and sizeof($array_setting['learningtasks']) < sizeof($array_learningtasks)) {
            $where_or = [];
            foreach ($array_setting['learningtasks'] as $value) {
                $where_or[] = 'tb2.learningtasks=' . $db->quote($value);
            }
            $where[] = "(" . implode(' OR ', $where_or) . ")";
        }

        $sql = "SELECT tb1.first_name, tb1.last_name, tb1.birthday, tb1.email,
        tb2.* FROM " . NV_USERS_GLOBALTABLE . " tb1, " . NV_USERS_GLOBALTABLE . "_info tb2 WHERE " . implode(' AND ', $where);
        $result = $db->query($sql);

        $stt = 0;
        while ($row = $result->fetch()) {
            $stt++;
            $line++;
            $row['full_name'] = nv_show_name_user($row['first_name'], $row['last_name']);
            $row['birthday'] = empty($row['birthday']) ? '' : nv_date('d/m/Y', $row['birthday']);

            $sheet->setCellValue('A' . $line, $stt);
            $sheet->setCellValue('B' . $line, nv_unhtmlspecialchars($row['full_name']));
            $sheet->setCellValue('C' . $line, nv_unhtmlspecialchars($row['birthday']));
            $sheet->setCellValue('D' . $line, nv_unhtmlspecialchars($row['workplace']));
            $sheet->setCellValue('E' . $line, nv_unhtmlspecialchars($row['email']));
            $sheet->setCellValue('F' . $line, nv_unhtmlspecialchars($row['phone']));

            // Trường đã học tại Bỉ
            $belgiumschool = isset($array_belgiumschool[$row['belgiumschool']]) ? $array_belgiumschool[$row['belgiumschool']]['title'] : '';
            if (preg_match('/^[\-]+$/', $belgiumschool)) {
                $belgiumschool = '';
            }
            $sheet->setCellValue('G' . $line, nv_unhtmlspecialchars($belgiumschool));

            $sheet->setCellValue('H' . $line, nv_unhtmlspecialchars($row['course']));

            // Thời gian học XXXX - YYYY
            $studytime = [];
            if (!empty($row['studytime_from'])) {
                $studytime[] = $row['studytime_from'];
            }
            if (!empty($row['studytime_to'])) {
                $studytime[] = $row['studytime_to'];
            }
            $sheet->setCellValue('I' . $line, nv_unhtmlspecialchars(implode(' - ', $studytime)));

            // Loại hình đào tạo
            $edutype = isset($array_edutype[$row['edutype']]) ? $array_edutype[$row['edutype']]['title'] : '';
            if (preg_match('/^[\-]+$/', $edutype)) {
                $edutype = '';
            }
            $sheet->setCellValue('J' . $line, nv_unhtmlspecialchars($edutype));

            // Ngành học
            $branch = isset($array_branch[$row['branch']]) ? $array_branch[$row['branch']]['title'] : '';
            if (preg_match('/^[\-]+$/', $branch)) {
                $branch = '';
            }
            $sheet->setCellValue('K' . $line, nv_unhtmlspecialchars($branch));

            $sheet->setCellValue('L' . $line, nv_unhtmlspecialchars($row['othernote']));
        }
    }

    // Kẻ viền bao vùng dữ liệu
    $styleArray = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ];
    $sheet->getStyle('A1:L' . $line)->applyFromArray($styleArray);

    // Ghi ra file
    $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $file_src = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/dshv.' . NV_CHECK_SESSION . '.xlsx';
    $objWriter->save($file_src);

    // Kết thúc
    $download = new NukeViet\Files\Download($file_src, NV_ROOTDIR . '/' . NV_TEMP_DIR, change_alias($lang_module['exportexcel_filename']) . '.xlsx');
    $download->download_file();
    exit();
}

$xtpl = new XTemplate('exportexcel.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

$array_setting['from'] = empty($array_setting['from']) ? '' : nv_date('d/m/Y', $array_setting['from']);
$array_setting['to'] = empty($array_setting['to']) ? '' : nv_date('d/m/Y', $array_setting['to']);
$xtpl->assign('DATA', $array_setting);

foreach ($array_group_managers as $group) {
    $group['checked'] = in_array($group['group_id'], $array_setting['group_id']) ? ' checked="checked"' : '';
    $xtpl->assign('GROUP', $group);
    $xtpl->parse('main.group');
}

foreach ($array_branch as $branch) {
    $branch['checked'] = in_array($branch['id'], $array_setting['branch']) ? ' checked="checked"' : '';
    $xtpl->assign('BRANCH', $branch);
    $xtpl->parse('main.branch');
}

foreach ($array_learningtasks as $learningtasks) {
    $learningtasks['checked'] = in_array($learningtasks['id'], $array_setting['learningtasks']) ? ' checked="checked"' : '';
    $xtpl->assign('LEARNINGTASKS', $learningtasks);
    $xtpl->parse('main.learningtasks');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
