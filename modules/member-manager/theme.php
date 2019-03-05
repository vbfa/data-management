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

function nv_theme_member_edit($array, $error, $isSubmit)
{
    global $lang_module, $lang_global, $module_info, $array_branch, $array_groups, $module_name, $global_config;
    global $array_belgiumschool, $array_vnschool, $array_learningtasks, $array_edutype, $array_concernarea;

    $xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('FORM_ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name);

    if ($array['birthday'] < 1) {
        $array['birthday'] = '';
    } else {
        $array['birthday'] = nv_date('d/m/Y', $array['birthday']);
    }

    $xtpl->assign('DATA', $array);

    if (!$isSubmit) {
        $xtpl->parse('main.note');
    }

    if (!empty($error)) {
        $xtpl->assign('ERROR', $error);
        $xtpl->parse('main.error');
    }

    /*
    foreach ($array_belgiumschool as $belgiumschool) {
        $belgiumschool['selected'] = $array['belgiumschool'] == $belgiumschool['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $belgiumschool['title'])) {
            $belgiumschool['title'] = $lang_module['othervalues'];
        }
        $xtpl->assign('BELGIUMSCHOOL', $belgiumschool);
        $xtpl->parse('main.belgiumschool');
    }
    */

    foreach ($array_vnschool as $vnschool) {
        $vnschool['selected'] = $array['vnschool'] == $vnschool['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $vnschool['title'])) {
            $vnschool['title'] = $lang_module['othervalues'];
        }
        $xtpl->assign('VNSCHOOL', $vnschool);
        $xtpl->parse('main.vnschool');
    }

    /*
    foreach ($array_learningtasks as $learningtasks) {
        $learningtasks['selected'] = $array['learningtasks'] == $learningtasks['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $learningtasks['title'])) {
            $learningtasks['title'] = $lang_module['othervalues'];
        }
        $xtpl->assign('LEARNINGTASKS', $learningtasks);
        $xtpl->parse('main.learningtasks');
    }
    */

    foreach ($array_edutype as $edutype) {
        $edutype['selected'] = $array['edutype'] == $edutype['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $edutype['title'])) {
            //$edutype['title'] = $lang_module['othervalues'];
            // Không hiển thị khác vào đây
            continue;
        }
        $xtpl->assign('EDUTYPE', $edutype);
        $xtpl->parse('main.edutype');
    }

    /*
    foreach ($array_branch as $branch) {
        $branch['selected'] = $array['branch'] == $branch['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $branch['title'])) {
            $branch['title'] = $lang_module['othervalues'];
        }
        $xtpl->assign('BRANCH', $branch);
        $xtpl->parse('main.branch');
    }

    foreach ($array_concernarea as $concernarea) {
        $concernarea['selected'] = $array['concernarea'] == $concernarea['id'] ? ' selected="selected"' : '';
        if (preg_match('/^[\-]+$/', $concernarea['title'])) {
            $concernarea['title'] = $lang_module['othervalues'];
        }
        $xtpl->assign('CONCERNAREA', $concernarea);
        $xtpl->parse('main.concernarea');
    }
    */

    if ($global_config['captcha_type'] == 2) {
        $xtpl->assign('N_CAPTCHA', $lang_global['securitycode1']);
        $xtpl->assign('RECAPTCHA_ELEMENT', 'recaptcha' . nv_genpass(8));
        $xtpl->assign('GFX_NUM', -1);
        $xtpl->parse('main.recaptcha');
    } else {
        $xtpl->assign('N_CAPTCHA', $lang_global['securitycode']);
        $xtpl->assign('CAPTCHA_REFRESH', $lang_global['captcharefresh']);
        $xtpl->assign('GFX_NUM', NV_GFX_NUM);
        $xtpl->assign('GFX_WIDTH', NV_GFX_WIDTH);
        $xtpl->assign('GFX_WIDTH', NV_GFX_WIDTH);
        $xtpl->assign('GFX_HEIGHT', NV_GFX_HEIGHT);
        $xtpl->assign('CAPTCHA_REFR_SRC', NV_BASE_SITEURL . NV_ASSETS_DIR . '/images/refresh.png');
        $xtpl->assign('SRC_CAPTCHA', NV_BASE_SITEURL . 'index.php?scaptcha=captcha&t=' . NV_CURRENTTIME);
        $xtpl->parse('main.captcha');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}
