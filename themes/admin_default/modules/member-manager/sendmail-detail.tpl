<!-- BEGIN: main -->
<div class="form-group text-right">
    <a href="{URL_BACK}">{LANG.back}</a>
</div>
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <caption>{LANG.sendmaildl_title}: {DATA.title}</caption>
            <thead>
                <tr>
                    <th class="text-nowrap">{GLANG.full_name}</th>
                    <th class="text-nowrap">{LANG.sendmaildl_ucode}</th>
                    <th class="text-nowrap">{GLANG.email}</th>
                    <th class="text-nowrap">{LANG.sendmaildl_status}</th>
                    <th class="w150 text-nowrap">{LANG.sendmail_send_time}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td class="mw200">{ROW.full_name}</td>
                    <td class="text-nowrap">{ROW.username}</td>
                    <td class="text-nowrap">{ROW.email}</td>
                    <td class="text-nowrap">{ROW.status}</td>
                    <td class="text-nowrap">{ROW.sentmail}</td>
                </tr>
                <!-- END: loop -->
            </tbody>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td colspan="5">
                        {GENERATE_PAGE}
                    </td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
        </table>
    </div>
</form>
<!-- END: main -->
