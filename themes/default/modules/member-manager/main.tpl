<!-- BEGIN: main -->
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>

<h1 class="margin-bottom-lg">{LANG.main_title}</h1>
<!-- BEGIN: note -->
<p>{LANG.main_note}.</p>
<!-- END: note -->

<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->

<form method="post" action="{FORM_ACTION}" autocomplete="off">
    <div class="panel panel-default panel-edit-member">
        <div class="panel-body">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.email} <span class="text-danger">(*)</span></label>
                    <div class="col-sm-14 col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control" name="email" value="{DATA.email}" id="memberEmailVal">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="button" data-toggle="sendEmail"><span class="load hidden"><i class="fa fa-spinner fa-spin"></i> </span>{LANG.send_email}</button>
                            </div>
                        </div>
                        <span class="text-muted form-text">{LANG.email_help}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.last_name}</label>
                    <div class="col-sm-14 col-md-12">
                        <input type="text" class="form-control" name="last_name" value="{DATA.last_name}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.first_name}</label>
                    <div class="col-sm-14 col-md-12">
                        <input type="text" class="form-control" name="first_name" value="{DATA.first_name}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.birthday}</label>
                    <div class="col-sm-14 col-md-12 form-inline">
                        <input type="text" class="form-control mxw150" name="birthday" value="{DATA.birthday}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.new_email}</label>
                    <div class="col-sm-14 col-md-12">
                        <input type="text" class="form-control" name="new_email" value="{DATA.new_email}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.workplace}</label>
                    <div class="col-sm-14 col-md-12">
                        <input type="text" class="form-control" name="workplace" value="{DATA.workplace}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.phone}</label>
                    <div class="col-sm-14 col-md-12">
                        <input type="text" class="form-control" name="phone" value="{DATA.phone}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.belgiumschool}</label>
                    <div class="col-sm-14 col-md-12 form-inline">
                        <select class="form-control" name="belgiumschool">
                            <!-- BEGIN: belgiumschool -->
                            <option value="{BELGIUMSCHOOL.id}"{BELGIUMSCHOOL.selected}>{BELGIUMSCHOOL.title}</option>
                            <!-- END: belgiumschool -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.vnschool}</label>
                    <div class="col-sm-14 col-md-12 form-inline">
                        <select class="form-control" name="vnschool">
                            <!-- BEGIN: vnschool -->
                            <option value="{VNSCHOOL.id}"{VNSCHOOL.selected}>{VNSCHOOL.title}</option>
                            <!-- END: vnschool -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.course}</label>
                    <div class="col-sm-6 col-md-5">
                        <input type="text" class="form-control" name="course" value="{DATA.course}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.studytime}</label>
                    <div class="col-sm-14 col-md-12">
                        <input type="text" class="form-control mxw150" name="studytime" value="{DATA.studytime}">
                        <span class="text-muted form-text">{LANG.studytime_help}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.learningtasks}</label>
                    <div class="col-sm-14 col-md-12 form-inline">
                        <select class="form-control" name="learningtasks">
                            <!-- BEGIN: learningtasks -->
                            <option value="{LEARNINGTASKS.id}"{LEARNINGTASKS.selected}>{LEARNINGTASKS.title}</option>
                            <!-- END: learningtasks -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.othernote}</label>
                    <div class="col-sm-14 col-md-12">
                        <textarea class="form-control" name="othernote" rows="3">{DATA.othernote}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.edutype}</label>
                    <div class="col-sm-14 col-md-12 form-inline">
                        <select class="form-control" name="edutype">
                            <!-- BEGIN: edutype -->
                            <option value="{EDUTYPE.id}"{EDUTYPE.selected}>{EDUTYPE.title}</option>
                            <!-- END: edutype -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.address}</label>
                    <div class="col-sm-14 col-md-12">
                        <input type="text" class="form-control" name="address" value="{DATA.address}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.fb_twitter}</label>
                    <div class="col-sm-14 col-md-12">
                        <input type="text" class="form-control" name="fb_twitter" value="{DATA.fb_twitter}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.contactsocial}</label>
                    <div class="col-sm-14 col-md-12">
                        <input type="text" class="form-control" name="contactsocial" value="{DATA.contactsocial}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.branch}</label>
                    <div class="col-sm-14 col-md-12 form-inline">
                        <select class="form-control" name="branch">
                            <!-- BEGIN: branch -->
                            <option value="{BRANCH.id}"{BRANCH.selected}>{BRANCH.title}</option>
                            <!-- END: branch -->
                        </select>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.concernarea}</label>
                    <div class="col-sm-14 col-md-12 form-inline">
                        <select class="form-control" name="concernarea">
                            <!-- BEGIN: concernarea -->
                            <option value="{CONCERNAREA.id}"{CONCERNAREA.selected}>{CONCERNAREA.title}</option>
                            <!-- END: concernarea -->
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-24">
                        <hr>
                        <label>{LANG.contactinfo}</label>
                        {DATA.contactinfo}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{LANG.editreason}</label>
                    <div class="col-sm-14 col-md-12">
                        <textarea class="form-control" name="editreason" rows="2">{DATA.editreason}</textarea>
                    </div>
                </div>
                <!-- BEGIN: captcha -->
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{N_CAPTCHA}</label>
                    <div class="col-sm-14 col-md-17 form-inline">
                        <input id="commentseccode_iavim" type="text" class="form-control mxw150" maxlength="{GFX_NUM}" name="code">
                        <img class="captchaImg" alt="{N_CAPTCHA}" src="{SRC_CAPTCHA}" height="30">
                        &nbsp;<em class="fa fa-pointer fa-refresh" onclick="change_captcha('#commentseccode_iavim');">&nbsp;</em>
                    </div>
                </div>
                <!-- END: captcha -->
                <!-- BEGIN: recaptcha -->
                <div class="form-group">
                    <label class="col-sm-10 col-md-7 control-label">{N_CAPTCHA}</label>
                    <div class="col-sm-14 col-md-17 form-inline">
                        <div id="{RECAPTCHA_ELEMENT}"></div>
                        <script type="text/javascript">
                        nv_recaptcha_elements.push({
                            id: "{RECAPTCHA_ELEMENT}",
                            btn: $("[name='submit']", $('#{RECAPTCHA_ELEMENT}').parent().parent().parent())
                        });
                        </script>
                    </div>
                </div>
                <!-- END: recaptcha -->
                <div class="row">
                    <div class="col-sm-14 col-md-12 col-sm-offset-10 col-md-offset-7">
                        <button type="submit" name="submit" class="btn btn-primary">{GLANG.submit}</button>
                    </div>
                </div>
            </div>
        </div>
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

<!-- END: main -->
