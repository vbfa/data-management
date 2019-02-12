<!-- BEGIN: main -->
<!-- BEGIN: nogroup -->
<div class="alert alert-warning">{LANG.queue_no_group}</div>
<!-- END: nogroup -->
<!-- BEGIN: data -->
<form method="get" action="{FORM_ACTION}">
    <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
    <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
    <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}">
    <div class="form-inline">
        <div class="form-group">
            <input type="text" name="q" class="form-control" value="{SEARCH.q}" placeholder="{GLANG.full_name}">
        </div>
        <div class="form-group">
            <select class="form-control" name="g">
                <option value="0">{LANG.search_all_group}</option>
                <!-- BEGIN: group -->
                <option value="{GROUP.group_id}"{GROUP.selected}>{GROUP.group_title}</option>
                <!-- END: group -->
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> {GLANG.search}</button>
        </div>
    </div>
</form>
<div class="table-responsive" style="margin-top: 15px;">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="w100">{LANG.sendmaildl_ucode}</th>
                <th>{LANG.report_field_full_name}</th>
                <th class="w100">{LANG.sendmail_to_group}</th>
                <th class="w150">{LANG.queue_status}</th>
                <th class="w150">{LANG.queue_verificationtime}</th>
                <th class="w250"></th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN: loop -->
            <tr>
                <td>{ROW.username}</td>
                <td>{ROW.full_name}</td>
                <td>{ROW.group}</td>
                <td>{ROW.cstatus}</td>
                <td>{ROW.verificationtime}</td>
                <td class="text-center">
                    <a class="btn btn-xs btn-info" href="{ROW.link_view}"><i class="fa fa-eye"></i> {LANG.viewmore}</a>
                    <a class="btn btn-xs btn-success" href="#" data-toggle="queueAccept" data-userid="{ROW.userid}" data-msg="{LANG.queue_accept_confirm}"><i class="fa fa-check"></i> {LANG.queue_accept}</a>
                    <a class="btn btn-xs btn-danger" href="#" data-toggle="queueRefuse" data-userid="{ROW.userid}" data-msg="{LANG.queue_refuse_confirm}"><i class="fa fa-times"></i> {LANG.queue_refuse}</a>
                </td>
            </tr>
            <!-- END: loop -->
        </tbody>
    </table>
</div>
<!-- BEGIN: generate_page -->
<div class="text-center" style="margin-top: 15px;">
    {GENERATE_PAGE}
</div>
<!-- END: generate_page -->
<!-- END: data -->
<!-- END: main -->

<!-- BEGIN: detail -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<form action="{FORM_ACTION}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <caption>{LANG.queue_caption}: {DATA.full_name}</caption>
            <thead>
                <tr>
                    <th class="w200">{LANG.queue_field}</th>
                    <th class="w200">{LANG.queue_old_value}</th>
                    <th class="text-center w100">{LANG.queue_change}</th>
                    <th>{LANG.queue_new_value}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{LANG.report_last_name}</td>
                    <td>{OLDDATA.last_name}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[last_name]" value="1"{CHANGE.last_name}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="last_name" value="{DATA.last_name}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_first_name}</td>
                    <td>{OLDDATA.first_name}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[first_name]" value="1"{CHANGE.first_name}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="first_name" value="{DATA.first_name}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_birthday}</td>
                    <td>{OLDDATA.birthday}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[birthday]" value="1"{CHANGE.birthday}>
                    </td>
                    <td class="form-inline">
                        <input type="text" class="form-control input-sm" name="birthday" value="{DATA.birthday}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_email}</td>
                    <td>{OLDDATA.email}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[email]" value="1"{CHANGE.email}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="email" value="{DATA.email}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_workplace}</td>
                    <td>{OLDDATA.workplace}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[workplace]" value="1"{CHANGE.workplace}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="workplace" value="{DATA.workplace}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_phone}</td>
                    <td>{OLDDATA.phone}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[phone]" value="1"{CHANGE.phone}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="phone" value="{DATA.phone}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_belgiumschool}</td>
                    <td>{OLDDATA.belgiumschool}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[belgiumschool]" value="1"{CHANGE.belgiumschool}>
                    </td>
                    <td class="form-inline">
                        <select class="form-control input-sm" name="belgiumschool">
                            <!-- BEGIN: belgiumschool -->
                            <option value="{BELGIUMSCHOOL.id}"{BELGIUMSCHOOL.selected}>{BELGIUMSCHOOL.title}</option>
                            <!-- END: belgiumschool -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_vnschool}</td>
                    <td>{OLDDATA.vnschool}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[vnschool]" value="1"{CHANGE.vnschool}>
                    </td>
                    <td class="form-inline">
                        <select class="form-control input-sm" name="vnschool">
                            <!-- BEGIN: vnschool -->
                            <option value="{VNSCHOOL.id}"{VNSCHOOL.selected}>{VNSCHOOL.title}</option>
                            <!-- END: vnschool -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_course}</td>
                    <td>{OLDDATA.course}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[course]" value="1"{CHANGE.course}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="course" value="{DATA.course}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_studytime}</td>
                    <td>{OLDDATA.studytime}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[studytime]" value="1"{CHANGE.studytime}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="studytime" value="{DATA.studytime}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_learningtasks}</td>
                    <td>{OLDDATA.learningtasks}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[learningtasks]" value="1"{CHANGE.learningtasks}>
                    </td>
                    <td class="form-inline">
                        <select class="form-control input-sm" name="learningtasks">
                            <!-- BEGIN: learningtasks -->
                            <option value="{LEARNINGTASKS.id}"{LEARNINGTASKS.selected}>{LEARNINGTASKS.title}</option>
                            <!-- END: learningtasks -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_othernote}</td>
                    <td>{OLDDATA.othernote}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[othernote]" value="1"{CHANGE.othernote}>
                    </td>
                    <td>
                        <textarea rows="3" class="form-control" name="othernote">{DATA.othernote}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_edutype}</td>
                    <td>{OLDDATA.edutype}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[edutype]" value="1"{CHANGE.edutype}>
                    </td>
                    <td class="form-inline">
                        <select class="form-control input-sm" name="edutype">
                            <!-- BEGIN: edutype -->
                            <option value="{EDUTYPE.id}"{EDUTYPE.selected}>{EDUTYPE.title}</option>
                            <!-- END: edutype -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_address}</td>
                    <td>{OLDDATA.address}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[address]" value="1"{CHANGE.address}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="address" value="{DATA.address}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_fb_twitter}</td>
                    <td>{OLDDATA.fb_twitter}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[fb_twitter]" value="1"{CHANGE.fb_twitter}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="fb_twitter" value="{DATA.fb_twitter}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_contactsocial}</td>
                    <td>{OLDDATA.contactsocial}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[contactsocial]" value="1"{CHANGE.contactsocial}>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="contactsocial" value="{DATA.contactsocial}">
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_branch}</td>
                    <td>{OLDDATA.branch}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[branch]" value="1"{CHANGE.branch}>
                    </td>
                    <td class="form-inline">
                        <select class="form-control input-sm" name="branch">
                            <!-- BEGIN: branch -->
                            <option value="{BRANCH.id}"{BRANCH.selected}>{BRANCH.title}</option>
                            <!-- END: branch -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_concernarea}</td>
                    <td>{OLDDATA.concernarea}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[concernarea]" value="1"{CHANGE.concernarea}>
                    </td>
                    <td class="form-inline">
                        <select class="form-control input-sm" name="concernarea">
                            <!-- BEGIN: concernarea -->
                            <option value="{CONCERNAREA.id}"{CONCERNAREA.selected}>{CONCERNAREA.title}</option>
                            <!-- END: concernarea -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>{LANG.report_field_contactinfo}</td>
                    <td>{OLDDATA.contactinfo}</td>
                    <td class="text-center">
                        <input type="checkbox" name="change[contactinfo]" value="1"{CHANGE.contactinfo}>
                    </td>
                    <td>
                        {DATA.contactinfo}
                    </td>
                </tr>
                <tr>
                    <td>{LANG.editreason}</td>
                    <td colspan="3">{DATA.editreason}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-center">
                        <button type="submit" name="submit" value="submit" class="btn btn-primary"><i class="fa fa-check"></i> {LANG.queue_accept}</button>
                        <a class="btn btn-danger" href="#" data-toggle="queueRefuse" data-userid="{DATA.user_id}" data-msg="{LANG.queue_refuse_confirm}"><i class="fa fa-times"></i> {LANG.queue_refuse}</a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</form>

<script>
$(document).ready(function() {
    $("[name='birthday']").datepicker({
        showOn : "both",
        dateFormat : "dd/mm/yy",
        changeMonth : true,
        changeYear : true,
        showOtherMonths : true,
        buttonImage : nv_base_siteurl + "assets/images/calendar.gif",
        buttonImageOnly : true
    });
});
</script>
<!-- END: detail -->
