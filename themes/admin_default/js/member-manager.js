/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

// Đình chỉ, kích hoạt lại email gửi
function nv_change_email_status(id) {
    var nv_timer = nv_settimeout_disable('change_status' + id, 4000);
    $.post(
        script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=sendmail&nocache=' + new Date().getTime(),
        'changestatus=1&id=' + id,
        function(res) {
            if (res != 'OK') {
                alert(nv_is_change_act_confirm[2]);
                location.reload();
            }
        }
    );
}

// Xóa email gửi
function nv_delete_emailsend(id) {
    if (confirm(nv_is_del_confirm[0])) {
        $.post(
            script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=sendmail&nocache=' + new Date().getTime(),
            'delete=1&id=' + id,
            function(res) {
                if (res == 'OK') {
                    location.reload();
                } else {
                    alert(nv_is_del_confirm[2]);
                }
            }
        );
    }
}

$(document).ready(function() {
    // Chọn hết các admin vào nhóm phân quyền
    $('[data-toggle="selcolgp"]').on('click', function(e) {
        e.preventDefault();
        var group_id = $(this).data('id');
        var numInput = $('.selcolgp' + group_id).length;
        var numInputSel = $('.selcolgp' + group_id + ':checked').length;
        if (numInput > numInputSel) {
            $('.selcolgp' + group_id).prop('checked', true);
        } else {
            $('.selcolgp' + group_id).prop('checked', false);
        }
    });

    // Chọn hết các nhóm vào admin phân quyền
    $('[data-toggle="selcolap"]').on('click', function(e) {
        e.preventDefault();
        var admin_id = $(this).data('id');
        var numInput = $('.selcolap' + admin_id).length;
        var numInputSel = $('.selcolap' + admin_id + ':checked').length;
        if (numInput > numInputSel) {
            $('.selcolap' + admin_id).prop('checked', true);
        } else {
            $('.selcolap' + admin_id).prop('checked', false);
        }
    });

    // Chọn/Bỏ chọn
    $('[data-toggle="checkuncheck"]').on('click', function(e) {
        e.preventDefault();
        var css = $(this).data('css');
        var numInput = $('.' + css).length;
        var numInputSel = $('.' + css + ':checked').length;
        if (numInput > numInputSel) {
            $('.' + css).prop('checked', true);
        } else {
            $('.' + css).prop('checked', false);
        }
    });

    // Xóa gửi cho hội viên
    $(document).delegate('.email-receiver-remove', 'click', function(e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    $('[data-toggle="crcv"]').on('click', function(e) {
        e.preventDefault();
        $('[name="change_receiver"]').val(1);
        $('.showsendnew').removeClass('hidden');
        $(this).remove();
    });

    // Hiển thị/Ẩn các điều kiện
    $('#reportShowHide').on('click', function(e) {
        e.preventDefault();
        $('#reportInputForm').toggleClass('hidden');
    });

    // Xem chi tiết hội viên
    $('[data-toggle="tipUserByID"]').on('click', function(e) {
        e.preventDefault();
        var target = $($(this).data('target'));
        modalShow(target.attr('title'), target.html());
    });

    // Duyệt một hội viên
    $('[data-toggle="queueAccept"]').on('click', function(e) {
        e.preventDefault();
        if (confirm($(this).data("msg"))) {
            $.post(
                script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=queue&nocache=' + new Date().getTime(),
                'queueaccept=1&userid=' + $(this).data("userid"),
                function(res) {
                    if (res == 'OK') {
                        location.reload();
                        return;
                    }
                    alert(res);
                }
            );
        }
    });

    // Từ chối một hội viên
    $('[data-toggle="queueRefuse"]').on('click', function(e) {
        e.preventDefault();
        if (confirm($(this).data("msg"))) {
            $.post(
                script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=queue&nocache=' + new Date().getTime(),
                'queuerefuse=1&userid=' + $(this).data("userid"),
                function(res) {
                    location.reload();
                }
            );
        }
    });
});
