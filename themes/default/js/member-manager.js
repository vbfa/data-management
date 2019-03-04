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
        // TODO
    });
});
