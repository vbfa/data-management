<!-- BEGIN: main -->
<!-- BEGIN: no_admin -->
<div class="alert alert-info">{LANG.permissions_no_admin}</div>
<!-- END: no_admin -->
<!-- BEGIN: data -->
<p class="text-info">{LANG.permissions_help}</p>
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="w150">{GLANG.your_account}</th>
                    <th class="w150">{GLANG.full_name}</th>
                    <!-- BEGIN: group -->
                    <th class="mw100 text-center"><a href="#" data-toggle="selcolgp" data-id="{GROUP.key}">{GROUP.title}</a></th>
                    <!-- END: group -->
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: admin -->
                <tr>
                    <td><a href="#" data-toggle="selcolap" data-id="{ADMIN.admin_id}">{ADMIN.username}</a></td>
                    <td>{ADMIN.full_name}</td>
                    <!-- BEGIN: group -->
                    <td class="text-center">
                        <input type="checkbox" name="admin[{ADMIN.admin_id}][]" value="{GROUP.key}"{GROUP.checked} class="selcolap{ADMIN.admin_id} selcolgp{GROUP.key}">
                    </td>
                    <!-- END: group -->
                </tr>
                <!-- END: admin -->
            </tbody>
        </table>
    </div>
    <div class="text-center">
        <input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" />
    </div>
</form>
<!-- END: data -->
<!-- END: main -->
