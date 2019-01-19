<!-- BEGIN: main -->

<h1 class="margin-bottom-lg">{LANG.list_members}</h1>
<form method="get" action="" class="form-inline">
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

<div class="flex-table mt-3">
    <div class="flex-table-head">
        <div class="name">Họ và tên</div>
        <div class="branch">Ngành học</div>
        <div class="studytime">Thời gian học</div>
        <div class="workplace">Cơ quan đang công tác</div>
        <div class="group">Chi hội</div>
    </div>
    <div class="flex-table-body">
        <!-- BEGIN: loop -->
        <div class="item">
            <div class="name">
                <h3><strong><a href="#" data-toggle="showmemberinfo" data-target="#memberInfo{ROW.stt}">{ROW.full_name}</a></strong></h3>
                <div class="hidden" id="memberInfo{ROW.stt}" title="{LANG.contactinfo}">
                    <div class="clearfix">{ROW.contactinfo}</div>
                </div>
            </div>
            <div class="branch"><span class="label-name">Ngành học: </span>{ROW.branch}</div>
            <div class="studytime"><span class="label-name">Thời gian học: </span>{ROW.studytime}</div>
            <div class="workplace"><span class="label-name">Cơ quan đang công tác: </span>{ROW.workplace}</div>
            <div class="group"><span class="label-name">Chi hội: </span>{ROW.group}</div>
        </div>
        <!-- END: loop -->
    </div>
</div>

<!-- BEGIN: generate_page -->
<div class="text-center margin-top">{GENERATE_PAGE}</div>
<!-- END: generate_page -->

<!-- END: main -->
