<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<div class="panel panel-default">
    <div class="panel-body">
        <form class="form-horizontal" action="{FORM_ACTION}" method="post">
            <div class="form-group">
                <label class="col-sm-10 col-md-6 control-label">{LANG.sendmail_sender_info}:</label>
                <div class="col-sm-14 col-md-18 col-lg-14">
                    <div class="form-inline">
                        <div class="form-group">
                            <div class="col-xs-24">
                                <label>{LANG.sendmail_sender_name}</label>
                                <input type="text" class="form-control" name="send_cname" value="{DATA.send_cname}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-24">
                                <label>{LANG.sendmail_sender_email}</label>
                                <input type="text" class="form-control" name="send_cemail" value="{DATA.send_cemail}">
                            </div>
                        </div>
                        <div class="text-muted form-text">{LANG.sendmail_sender_default}</div>
                    </div>
                </div>
            </div>
            <div class="form-group showsendnew{SHOW_SEND_TO}">
                <label class="col-sm-10 col-md-6 control-label">{LANG.sendmail_send_user}:</label>
                <div class="col-sm-14 col-md-18 col-lg-10">
                    <input type="text" class="form-control" id="username-search" value="" autocomplete="off">
                    <div class="text-muted form-text">{LANG.sendmail_send_user_help}</div>
                    <div class="mt-10" id="emailReceiver">
                        <!-- BEGIN: receiver -->
                        <div class="email-receiver">
                            <span class="n">{RECEIVER.full_name}</span>
                            <span class="f">{RECEIVER.username}</span>
                            <a href="#" class="email-receiver-remove"><i class="fa fa-times-circle-o" aria-hidden="true"></i></a>
                            <input type="hidden" name="receiver[]" value="{RECEIVER.username}" data-value="{RECEIVER.username}">
                        </div>
                        <!-- END: receiver -->
                    </div>
                </div>
            </div>
            <div class="row showsendnew{SHOW_SEND_TO}">
                <label class="col-sm-10 col-md-6 control-label">{LANG.sendmail_send_group}:</label>
                <div class="col-sm-14 col-md-18 col-lg-10">
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
            <!-- BEGIN: change_receiver -->
            <div class="form-group">
                <div class="col-sm-14 col-md-18 col-lg-10 col-sm-push-10 col-md-push-6">
                    <a href="#" data-toggle="crcv"><i class="fa fa-times-circle-o" aria-hidden="true"></i> {LANG.sendmail_change_receiver}</a>
                </div>
            </div>
            <!-- END: change_receiver -->
            <div class="form-group">
                <label class="col-sm-10 col-md-6 control-label">{LANG.sendmail_email_subject} <span class="text-danger">(*)</span>:</label>
                <div class="col-sm-14 col-md-18 col-lg-10">
                    <input type="text" class="form-control" value="{DATA.title}" name="title">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-24 col-md-20 col-lg-18 col-md-push-2 col-lg-push-3">
                    {DATA.emailcontent}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-14 col-md-18 col-lg-10 col-sm-push-10 col-md-push-6">
                    <input type="hidden" name="change_receiver" value="{DATA.change_receiver}">
                    <button type="submit" name="submit" class="btn btn-primary">{GLANG.submit}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script>
function addEmailReceiver(data) {
    if ($('[data-value="' + data.value + '"]').length) {
        return;
    }
    var html = '\
        <div class="email-receiver">\
            <span class="n">' + data.label + '</span>\
            <span class="f">' + data.value + '</span>\
            <a href="#" class="email-receiver-remove"><i class="fa fa-times-circle-o" aria-hidden="true"></i></a>\
            <input type="hidden" name="receiver[]" value="' + data.value + '" data-value="' + data.value + '">\
        </div>\
    ';
    $('#emailReceiver').append(html);
}
$(document).ready(function() {
    $("#username-search").autocomplete({
        source: function(request, response) {
            $.getJSON(script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=sendmail-content&nocache=' + new Date().getTime(), {
                term: request.term
            }, response);
        },
        search: function() {
            // Nhập trên 2 ký tự mới tìm
            if (this.value.length < 3) {
                return false;
            }
        },
        select: function(event, ui) {
            // Chọn hội viên
            addEmailReceiver(ui.item);
            $(this).val('');
            return false;
        }
    });
});
</script>
<!-- END: main -->
