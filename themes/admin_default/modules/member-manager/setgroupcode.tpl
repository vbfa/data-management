<!-- BEGIN: main -->
<!-- BEGIN: info -->
<div class="alert alert-info">{LANG.setgroupcode_note1}</div>
<!-- END: info -->
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 50%" class="text-right">{LANG.setgroupcode_name}</th>
                    <th style="width: 50%" class="text-left">{LANG.setgroupcode_code}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    <td class="text-right align-middle">{ROW.title}</td>
                    <td class="align-middle">
                        <!-- BEGIN: edit -->
                        <input type="text" class="form-control w150" name="groupcode[{ROW.key}]" value="{ROW.value}">
                        <!-- END: edit -->
                        <!-- BEGIN: text -->
                        {ROW.value}
                        <!-- END: text -->
                    </td>
                </tr>
                <!-- END: loop -->
            </tbody>
        </table>
    </div>
    <div class="text-center"><input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" /></div>
</form>
<!-- BEGIN: info2 -->
<hr>
<p><i>{LANG.setgroupcode_note2}</i></p>
<!-- END: info2 -->
<!-- END: main -->
