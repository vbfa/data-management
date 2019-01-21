<!-- BEGIN: main -->
<!-- BEGIN: group_no_info -->
<div class="alert alert-info">
    <a href="{GROUP_LINK_INFO}">{GROUP_NO_INFO}</a>
</div>
<!-- END: group_no_info -->
<!-- BEGIN: nogroup -->
<div class="alert alert-warning">{LANG.main_no_group}</div>
<!-- END: nogroup -->
<!-- BEGIN: data -->
<form method="get" action="{FORM_ACTION}">
    <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
    <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
    <div class="form-inline">
        <div class="form-group">
            <input type="text" name="q" class="form-control" value="{SEARCH.q}" placeholder="{GLANG.full_name}">
        </div>
        <div class="form-group">
            <select class="form-control" name="b">
                <option value="0">{LANG.search_all_branch}</option>
                <!-- BEGIN: branch -->
                <option value="{BRANCH.id}"{BRANCH.selected}>{BRANCH.title}</option>
                <!-- END: branch -->
            </select>
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
            <button type="button" class="btn btn-link" data-toggle="collapse" href="#advSearchForm">{LANG.search_adv}</button>
        </div>
    </div>
    <div class="collapse{SEARCH_ADV}" id="advSearchForm">
        <div style="margin-top: 15px;">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><strong>{LANG.report_field_workplace}:</strong></label>
                        <input type="text" class="form-control" name="wo" value="{SEARCH.workplace}">
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><strong>{LANG.report_field_studytime}:</strong></label>
                        <div class="row">
                            <div class="col-xs-12">
                                <input type="number" class="form-control" name="stf" value="{SEARCH.studytime_from}" placeholder="{LANG.search_from}" min="0">
                            </div>
                            <div class="col-xs-12">
                                <input type="number" class="form-control" name="stt" value="{SEARCH.studytime_to}" placeholder="{LANG.search_to}" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><strong>{LANG.report_field_edutype}:</strong></label>
                        <select class="form-control" name="ed">
                            <option value="0">{LANG.search_all_edutype}</option>
                            <!-- BEGIN: edutype -->
                            <option value="{EDUTYPE.id}"{EDUTYPE.selected}>{EDUTYPE.title}</option>
                            <!-- END: edutype -->
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><strong>{LANG.report_field_concernarea}:</strong></label>
                        <select class="form-control" name="co">
                            <option value="0">{LANG.search_all_concernarea}</option>
                            <!-- BEGIN: concernarea -->
                            <option value="{CONCERNAREA.id}"{CONCERNAREA.selected}>{CONCERNAREA.title}</option>
                            <!-- END: concernarea -->
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="table-responsive" style="margin-top: 15px;">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="w200">{LANG.report_field_full_name}</th>
                <th class="w150">{LANG.report_field_branch}</th>
                <th class="w150">{LANG.report_field_studytime}</th>
                <th>{LANG.report_field_workplace}</th>
                <th class="w150">{LANG.sendmail_to_group}</th>
                <th class="w150"></th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN: loop -->
            <tr>
                <td>
                    <strong>{ROW.full_name}</strong>
                    <div id="detailUser{ROW.userid}" class="hidden" title="{LANG.viewdetailuser} {ROW.full_name}">
                        <div class="clearfix itemDetailUser">
                            <dl class="dl-horizontal">
                                <dt>{LANG.report_field_full_name}</dt>
                                <dd>{ROW.full_name}</dd>
                                <dt>{LANG.report_field_birthday}</dt>
                                <dd>{ROW.birthday}</dd>
                                <dt>{LANG.report_field_workplace}</dt>
                                <dd>{ROW.workplace}</dd>
                                <dt>{LANG.report_field_email}</dt>
                                <dd>{ROW.email}</dd>
                                <dt>{LANG.report_field_phone}</dt>
                                <dd>{ROW.phone}</dd>
                                <dt>{LANG.report_field_belgiumschool}</dt>
                                <dd>{ROW.belgiumschool}</dd>
                                <dt>{LANG.report_field_vnschool}</dt>
                                <dd>{ROW.vnschool}</dd>
                                <dt>{LANG.report_field_course}</dt>
                                <dd>{ROW.course}</dd>
                                <dt>{LANG.report_field_studytime}</dt>
                                <dd>{ROW.studytime}</dd>
                                <dt>{LANG.report_field_edutype}</dt>
                                <dd>{ROW.edutype}</dd>
                                <dt>{LANG.report_field_branch}</dt>
                                <dd>{ROW.branch}</dd>
                                <dt>{LANG.report_field_concernarea}</dt>
                                <dd>{ROW.concernarea}</dd>
                                <dt>{LANG.report_field_contactinfo}</dt>
                                <dd>{ROW.contactinfo}</dd>
                                <dt>{LANG.report_field_othernote}</dt>
                                <dd>{ROW.othernote}</dd>
                            </dl>
                        </div>
                    </div>
                </td>
                <td>{ROW.branch}</td>
                <td>{ROW.studytime}</td>
                <td>{ROW.workplace}</td>
                <td>{ROW.group}</td>
                <td class="text-center">
                    <a class="btn btn-xs btn-info" href="#" data-toggle="tipUserByID" data-target="#detailUser{ROW.userid}"><i class="fa fa-eye"></i> {LANG.viewmore}</a>
                    <a class="btn btn-xs btn-default" href="{ROW.link_edit}"><i class="fa fa-edit"></i> {GLANG.edit}</a>
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
