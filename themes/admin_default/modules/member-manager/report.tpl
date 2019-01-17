<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<form action="{NV_BASE_ADMINURL}index.php" method="get">
    <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
    <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
    <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-horizontal{FORM_CSS}" id="reportInputForm">
                <div class="form-group">
                    <label class="col-md-6 control-label"><a href="#" data-toggle="checkuncheck" data-css="checkfields">{LANG.report_sel_field}</a></label>
                    <div class="col-md-18 form-inline">
                        <!-- BEGIN: field -->
                        <div class="checkbox">
                            <label>
                                <input class="checkfields" type="checkbox" name="fields[]" value="{FIELD.key}"{FIELD.checked}> {FIELD.title} &nbsp; &nbsp;
                            </label>
                        </div>
                        <!-- END: field -->
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-6 control-label">{LANG.report_field_belgiumschool}</label>
                    <div class="col-md-10 col-lg-8">
                        <select class="form-control" name="belgiumschool[]" multiple="multiple" style="height: 120px;">
                            <!-- BEGIN: belgiumschool -->
                            <option value="{BELGIUMSCHOOL.id}"{BELGIUMSCHOOL.selected}>{BELGIUMSCHOOL.title}</option>
                            <!-- END: belgiumschool -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-6 control-label">{LANG.report_field_studytime1}</label>
                    <div class="col-md-10 col-lg-8 form-inline export-date">
                        <input type="text" class="form-control w100" name="studytime_from" value="{DATA.studytime_from}" placeholder="dd/mm/yyyy">
                        {LANG.to}
                        <input type="text" class="form-control w100" name="studytime_to" value="{DATA.studytime_to}" placeholder="dd/mm/yyyy">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-6 control-label">{LANG.report_field_edutype}</label>
                    <div class="col-md-10 col-lg-8">
                        <select class="form-control" name="edutype[]" multiple="multiple" style="height: 120px;">
                            <!-- BEGIN: edutype -->
                            <option value="{EDUTYPE.id}"{EDUTYPE.selected}>{EDUTYPE.title}</option>
                            <!-- END: edutype -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-6 control-label">{LANG.report_field_branch}</label>
                    <div class="col-md-10 col-lg-8">
                        <select class="form-control" name="branch[]" multiple="multiple" style="height: 120px;">
                            <!-- BEGIN: branch -->
                            <option value="{BRANCH.id}"{BRANCH.selected}>{BRANCH.title}</option>
                            <!-- END: branch -->
                        </select>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-10 col-md-6 control-label"><a href="#" data-toggle="checkuncheck" data-css="checkgroupid">{LANG.importexcel_select_group}</a></label>
                    <div class="col-sm-14 col-md-10 col-lg-8">
                        <div class="row pt-7">
                            <!-- BEGIN: group -->
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-xs-24">
                                        <label><input class="checkgroupid" type="checkbox" name="group_id[]" value="{GROUP.group_id}"{GROUP.checked}> {GROUP.group_title} ({GROUP.group_code})</label>
                                    </div>
                                </div>
                            </div>
                            <!-- END: group -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-horizontal">
                <div class="row">
                    <div class="col-md-18 col-md-offset-6">
                        <button type="submit" name="submit_view" value="1" class="btn btn-default"><i class="fa fa-table" aria-hidden="true"></i> {LANG.report_view}</button>
                        <button type="submit" name="submit_excel" value="1" class="btn btn-primary"><i class="fa fa-file-excel-o" aria-hidden="true"></i> {LANG.report_excel}</button>
                        <button type="button" class="btn btn-link" id="reportShowHide">{LANG.report_showhide}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script>
$(document).ready(function() {
    $('[name="studytime_from"],[name="studytime_to"]').datepicker({
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

<!-- BEGIN: data -->
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <!-- BEGIN: thead -->
                <th class="{THEAD_CSS}">{THEAD_TITLE}</th>
                <!-- END: thead -->
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN: loop -->
            <tr>
                <!-- BEGIN: field -->
                <td>{VALUE}</td>
                <!-- END: field -->
            </tr>
            <!-- END: loop -->
        </tbody>
    </table>
</div>
<!-- BEGIN: generate_page -->
<div class="text-center" style="margin-top: 20px;">
    {GENERATE_PAGE}
</div>
<!-- END: generate_page -->
<!-- END: data -->
<!-- END: main -->
