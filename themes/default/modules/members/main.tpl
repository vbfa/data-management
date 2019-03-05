<!-- BEGIN: main -->

<h1 class="margin-bottom-lg">{LANG.list_members}</h1>
<form method="get" action="{FORM_ACTION}" class="form-inline">
    <!-- BEGIN: no_rewrite -->
    <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}">
    <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}">
    <!-- END: no_rewrite -->
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
    </div>
</form>

<div class="flex-table mt-3 mb-3">
    <div class="flex-table-head">
        <div class="name">{LANG.f_name}</div>
        <div class="branch">{LANG.f_branch}</div>
        <div class="studytime">{LANG.f_studytime}</div>
        <div class="workplace">{LANG.f_workplace}</div>
        <div class="group">{LANG.f_group}</div>
    </div>
    <div class="flex-table-body">
        <!-- BEGIN: loop -->
        <div class="item">
            <div class="name">
                <h3><strong><a href="#" data-toggle="showmemberinfo" data-target="#memberInfo{ROW.stt}"><i class="fa fa-user-circle-o<!-- BEGIN: noinfo --> text-muted<!-- END: noinfo -->" aria-hidden="true"<!-- BEGIN: haveinfo --> data-toggle="tipmemberinfo" data-title="{LANG.contactinfo}"<!-- END: haveinfo -->></i> {ROW.full_name}</a></strong></h3>
                <div class="hidden" id="memberInfo{ROW.stt}" title="{LANG.contactinfo}">
                    <div class="clearfix">{ROW.contactinfo}</div>
                </div>
            </div>
            <div class="branch"><span class="label-name">{LANG.f_branch}: </span>{ROW.branch}</div>
            <div class="studytime"><span class="label-name">{LANG.f_studytime}: </span>{ROW.studytime}</div>
            <div class="workplace"><span class="label-name">{LANG.f_workplace}: </span>{ROW.workplace}</div>
            <div class="group"><span class="label-name">{LANG.f_group}: </span>{ROW.group}</div>
        </div>
        <!-- END: loop -->
    </div>
</div>

<!-- BEGIN: generate_page -->
<div class="text-center margin-top">{GENERATE_PAGE}</div>
<!-- END: generate_page -->

<!-- END: main -->
