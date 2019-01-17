<!-- BEGIN: main -->
<a href="{URL_SEND_NEW}" class="btn btn-success"><i class="fa fa-paper-plane" aria-hidden="true"></i> {LANG.sendmail_new}</a>
<hr>
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <caption>{LANG.sendmail_list}</caption>
            <thead>
                <tr>
                    <th style="width: 30%;" class="text-nowrap">{LANG.sendmail_email_subject}</th>
                    <th style="width: 20%;" class="text-nowrap">{LANG.sendmail_sender}</th>
                    <th style="width: 30%;" class="text-nowrap">{LANG.sendmail_receiver}</th>
                    <th class="w150 text-nowrap">{LANG.sendmail_send_time}</th>
                    <th class="w150 text-right text-nowrap">{LANG.sendmail_send_status}</th>
                    <th class="w100 text-center text-nowrap">{LANG.status}</th>
                    <th class="w150 text-center text-nowrap">{LANG.func}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td class="mw200">{ROW.title}</td>
                    <td class="text-nowrap">{ROW.send_name}</td>
                    <td class="mw200">
                        <!-- BEGIN: receiver_users -->
                        <div>{LANG.sendmail_to_single}: {RECEIVER_USERS}</div>
                        <!-- END: receiver_users -->
                        <!-- BEGIN: receiver_groups -->
                        <div>{LANG.sendmail_to_group}: {RECEIVER_GROUPS}</div>
                        <!-- END: receiver_groups -->
                    </td>
                    <td class="text-nowrap">{ROW.send_time}</td>
                    <td class="text-nowrap">
                        <a href="{ROW.link_detail}"><strong>{ROW.email_send}</strong></a>
                    </td>
                    <td class="text-nowrap text-center">
                        <input name="status" id="change_status{ROW.id}" value="1" type="checkbox"{ROW.status} onclick="nv_change_email_status({ROW.id});">
                    </td>
                    <td class="text-nowrap">
                        <a href="{ROW.link_edit}" class="btn btn-sm btn-default"><i class="fa fa-edit"></i> {GLANG.edit}</a>
                        <a href="javascript:void(0);" onclick="nv_delete_emailsend({ROW.id});" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> {GLANG.delete}</a>
                    </td>
                </tr>
                <!-- END: loop -->
            </tbody>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td colspan="7">
                        {GENERATE_PAGE}
                    </td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
        </table>
    </div>
</form>
<!-- END: main -->
