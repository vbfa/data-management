<!-- BEGIN: main -->
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post" class="form-horizontal">
    <div class="panel panel-default">
        <div class="panel-body">
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
            <div class="form-group">
                <label class="col-sm-10 col-md-6 control-label">{LANG.exportexcel_fromto}</label>
                <div class="col-sm-14 col-md-10 col-lg-8 form-inline export-date">
                    <input type="text" class="form-control w100" name="from" value="{DATA.from}" placeholder="dd/mm/yyyy">
                    {LANG.to}
                    <input type="text" class="form-control w100" name="to" value="{DATA.to}" placeholder="dd/mm/yyyy">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-10 col-md-6 control-label"><a href="#" data-toggle="checkuncheck" data-css="checkbranch">{LANG.exportexcel_selbranch}</a></label>
                <div class="col-sm-14 col-md-10 col-lg-8">
                    <div class="row pt-7">
                        <!-- BEGIN: branch -->
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-xs-24">
                                    <label><input class="checkbranch" type="checkbox" name="branch[]" value="{BRANCH.id}"{BRANCH.checked}> {BRANCH.title}</label>
                                </div>
                            </div>
                        </div>
                        <!-- END: branch -->
                    </div>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-10 col-md-6 control-label"><a href="#" data-toggle="checkuncheck" data-css="checklearningtasks">{LANG.exportexcel_sellearningtasks}</a></label>
                <div class="col-sm-14 col-md-10 col-lg-8">
                    <div class="row pt-7">
                        <!-- BEGIN: learningtasks -->
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-xs-24">
                                    <label><input class="checklearningtasks" type="checkbox" name="learningtasks[]" value="{LEARNINGTASKS.id}"{LEARNINGTASKS.checked}> {LEARNINGTASKS.title}</label>
                                </div>
                            </div>
                        </div>
                        <!-- END: learningtasks -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-14 col-md-10 col-lg-8 col-sm-push-10 col-md-push-6">
                    <input class="btn btn-primary" name="submit" type="submit" value="{GLANG.submit}" />
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
    $('[name="from"],[name="to"]').datepicker({
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
<!-- END: main -->
