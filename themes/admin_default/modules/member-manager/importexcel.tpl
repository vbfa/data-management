<!-- BEGIN: main -->
<h2>{LANG.importexcel_h2}</h2>
<ul>
  <li><a href="{LINK_DOWNLOAD}">{LANG.importexcel_step1}</a></li>
  <li>{LANG.importexcel_step2}</li>
  <li>{LANG.importexcel_step3}</li>
</ul>
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-10 col-md-6 control-label">{LANG.importexcel_select_group}</label>
                    <div class="col-sm-14 col-md-10 col-lg-8">
                        <select class="form-control" name="group_id">
                            <option value="0">----</option>
                            <!-- BEGIN: group -->
                            <option value="{GROUP.group_id}"{GROUP.selected}>{GROUP.group_title} ({GROUP.group_code})</option>
                            <!-- END: group -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-6 control-label">{LANG.importexcel_select_invalidmode}</label>
                    <div class="col-sm-14 col-md-10 col-lg-8">
                        <select class="form-control" name="invalidmode">
                            <!-- BEGIN: invalidmode -->
                            <option value="{INVALIDMODE.key}"{INVALIDMODE.selected}>{INVALIDMODE.title}</option>
                            <!-- END: invalidmode -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-6 control-label">{LANG.importexcel_select_existsmode}</label>
                    <div class="col-sm-14 col-md-10 col-lg-8">
                        <select class="form-control" name="existsmode">
                            <!-- BEGIN: existsmode -->
                            <option value="{EXISTSMODE.key}"{EXISTSMODE.selected}>{EXISTSMODE.title}</option>
                            <!-- END: existsmode -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-6 control-label">{LANG.importexcel_select_file}</label>
                    <div class="col-sm-14 col-md-10 col-lg-8">
                        <input class="form-control" name="fileexcel" type="file">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-14 col-md-10 col-lg-8 col-sm-push-10 col-md-push-6">
                        <input class="btn btn-primary" name="submit" type="submit" value="{GLANG.submit}" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<p>{LANG.importexcel_select_existsmode_help}</p>
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script>
$(document).ready(function() {
    $('[name="group_id"]').select2();
});
</script>
<!-- END: main -->

<!-- BEGIN: success -->
<div class="alert alert-success">
    <h2>Import thành công, dưới đây là thông tin:</h2>
    <ul>
        <li>Tổng số hội viên đọc được từ excel: <strong class="text-danger">{NUM_READ}</strong></li>
        <li>Số hội viên đã thêm mới: <strong class="text-danger">{NUM_ADDED}</strong></li>
        <li>Số hội viên trùng: <strong class="text-danger">{NUM_EXISTS}</strong></li>
        <li>Số hội viên đã cập nhật: <strong class="text-danger">{NUM_UPDATED}</strong></li>
        <li>Số hội viên đã bỏ qua do trùng email trong OpenID: <strong class="text-danger">{NUM_EMAIL_EXISTS}</strong></li>
    </ul>
</div>
<!-- END: success -->
