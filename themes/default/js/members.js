/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 1 - 31 - 2010 5 : 12
 */

$(function() {
    // Hiển thị thông tin liên hệ của hội viên
    $('[data-toggle="showmemberinfo"]').on('click', function(e) {
        e.preventDefault();
        modalShowByObj($(this).data('target'));
    });

    // Icon thông tin liên hệ của hội viên
    $('[data-toggle="tipmemberinfo"]').tooltip();
});
