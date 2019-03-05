/**
 * @Project VBFA MEMBER-MANAGER
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: GNU/GPL version 2 or any later version
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

$(document).ready(function() {
    $('[data-toggle="sendEmail"]').on('click', function(e) {
        e.preventDefault();
        var loadBtn = $(this).find('.load');
        if (loadBtn.is(':visible')) {
            return;
        }
        loadBtn.removeClass('hidden');
        var email = $('#memberEmailVal').val();
        $.ajax({
            type: 'POST',
            url: nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=main&nocache=' + new Date().getTime(),
            data: {
                'sendemail': 1,
                'email': email
            }
        }).done(function(res) {
            alert(res);
            loadBtn.addClass('hidden');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert("Server error, please try again!!!");
        });
    });
});
