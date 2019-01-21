<?php

/**
 * @Project VBFA MEMBER-MANAGER
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2018 VINADES.,JSC. All rights reserved
 * @License: GNU/GPL version 2 or any later version
 * @Createdate Wed, 21 Nov 2018 02:52:58 GMT
 */

if (!defined('NV_MAINFILE'))
    die('Stop!!!');

$lang_translator['author'] = 'VINADES.,JSC (contact@vinades.vn)';
$lang_translator['createdate'] = '21/11/2018, 02:52';
$lang_translator['copyright'] = '@Copyright (C) 2018 VINADES.,JSC All rights reserved';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

$lang_module['permissions'] = 'Phân quyền';
$lang_module['save'] = 'Lưu lại';
$lang_module['to'] = 'đến';
$lang_module['status'] = 'Hoạt động';
$lang_module['func'] = 'Chức năng';
$lang_module['back'] = 'Trở về trang trước';
$lang_module['viewmore'] = 'Chi tiết';
$lang_module['viewdetailuser'] = 'Thông tin chi tiết hội viên';

$lang_module['search_other'] = 'Khác';
$lang_module['search_all_branch'] = 'Tất cả ngành học';
$lang_module['search_all_group'] = 'Tất cả chi hội';
$lang_module['search_all_edutype'] = 'Tất cả loại hình đào tạo';
$lang_module['search_all_concernarea'] = 'Tất cả lĩnh vực quan tâm';
$lang_module['search_adv'] = 'Nâng cao';
$lang_module['search_from'] = 'Từ';
$lang_module['search_to'] = 'Đến';

$lang_module['permissions_no_admin'] = 'Chưa có tài khoản quản trị module nào được cấp phép';
$lang_module['permissions_help'] = 'Mời check vào ô tương ứng tại các nhóm (chi hội) để cấp quyền quản lý chi hội đó cho người điều hành';

$lang_module['importexcel'] = 'Nhập dữ liệu';
$lang_module['importexcel_h2'] = 'Hướng dẫn nhập hội viên';
$lang_module['importexcel_step1'] = 'Nhấp vào đây để tải về file Excel mẫu (nếu chưa có file excel mẫu)';
$lang_module['importexcel_step2'] = 'Điền thông tin hội viên vào file excel mẫu đó';
$lang_module['importexcel_step3'] = 'Hoàn thành form bên dưới để import hội viên';
$lang_module['importexcel_select_group'] = 'Chọn chi hội';
$lang_module['importexcel_select_invalidmode'] = 'Xử lý dữ liệu không hợp lệ';
$lang_module['importexcel_select_invalidmode0'] = 'Bỏ qua các dòng lỗi';
$lang_module['importexcel_select_invalidmode1'] = 'Kiểm ra, không import';
$lang_module['importexcel_select_existsmode'] = 'Xử lý dữ liệu trùng<sup>(*)</sup>';
$lang_module['importexcel_select_existsmode0'] = 'Bỏ qua các dòng trùng';
$lang_module['importexcel_select_existsmode1'] = 'Ghi đè thông tin hội viên';
$lang_module['importexcel_select_existsmode_help'] = '(*) Hội viên có cùng email được coi là trùng lặp';
$lang_module['importexcel_select_file'] = 'Chọn file excel';
$lang_module['importexcel_error_file'] = 'Lỗi: Chưa chọn chi hội';
$lang_module['importexcel_error_filetype'] = 'Lỗi: File tải lên không hỗ trợ. Chỉ chọn file XLS, XLSX hoặc ODS';
$lang_module['importexcel_error_file_minetype'] = 'Lỗi: Không đọc được dữ liệu của file excel này';
$lang_module['importexcel_error_email_empty'] = 'Lỗi dòng số %s: Email trống';
$lang_module['importexcel_error_email'] = 'Lỗi email dòng số %s: %s';
$lang_module['importexcel_error_studytime'] = 'Lỗi dòng số %s: Thời gian học %s không hợp lệ';

$lang_module['setgroupcode'] = 'Đặt mã chi hội';
$lang_module['setgroupcode_name'] = 'Tên chi hội';
$lang_module['setgroupcode_code'] = 'Mã chi hội';
$lang_module['setgroupcode_note1'] = 'Mã chi hội gồm 3 chữ cái viết hoa. Sau khi đặt thì mã không thể thay đổi<sup>(*)</sup>. Lưu ý mã phải không trùng nhau ở các chi hội';
$lang_module['setgroupcode_note2'] = '(*) Quản trị tối cao có thể thay đổi được';
$lang_module['setgroupcode_error_isset'] = 'Chi hội có ID %s không tồn tại';
$lang_module['setgroupcode_error_rule'] = 'Mã chi hội %s không hợp lệ';

$lang_module['exportexcel'] = 'Xuất dữ liệu';
$lang_module['exportexcel_filename'] = 'Danh sách hội viên';
$lang_module['exportexcel_fromto'] = 'Trong khoảng thời gian';
$lang_module['exportexcel_selbranch'] = 'Ngành học';
$lang_module['exportexcel_sellearningtasks'] = 'Nhiệm vụ học tập';

$lang_module['sendmail'] = 'Gửi email';
$lang_module['sendmail_new'] = 'Gửi email mới';
$lang_module['sendmail_edit'] = 'Hiệu chỉnh email gửi';
$lang_module['sendmail_sender_info'] = 'Thông tin người gửi';
$lang_module['sendmail_sender_default'] = 'Nếu không nhập hệ thống sẽ lấy tên website và email liên hệ của site';
$lang_module['sendmail_sender_name'] = 'Tên';
$lang_module['sendmail_sender_email'] = 'Email';
$lang_module['sendmail_send_user'] = 'Gửi cho hội viên';
$lang_module['sendmail_send_user_help'] = 'Nhập tên hoặc mã hội viên để tìm kiếm và chọn';
$lang_module['sendmail_send_group'] = 'Gửi cho chi hội';
$lang_module['sendmail_email_subject'] = 'Tiêu đề email';
$lang_module['sendmail_error_receiver'] = 'Lỗi: Bạn chưa chọn hội viên nhận hoặc chi hội nhận email';
$lang_module['sendmail_error_title'] = 'Lỗi: Chưa nhập tiêu đề email';
$lang_module['sendmail_error_content'] = 'Lỗi: Chưa nhập nội dung email';
$lang_module['sendmail_to_single'] = 'Hội viên';
$lang_module['sendmail_to_group'] = 'Chi hội';
$lang_module['sendmail_change_receiver'] = 'Thay đổi người nhận (hệ thống sẽ thực hiện gửi lại email)';
$lang_module['sendmail_sender'] = 'Người gửi';
$lang_module['sendmail_receiver'] = 'Người nhận';
$lang_module['sendmail_list'] = 'Danh sách các email đã gửi';
$lang_module['sendmail_send_time'] = 'Ngày gửi';
$lang_module['sendmail_send_status'] = 'Email đã gửi';

$lang_module['sendmaildl_title'] = 'Chi tiết gửi email';
$lang_module['sendmaildl'] = 'Danh sách các email gửi đi';
$lang_module['sendmaildl_ucode'] = 'Mã hội viên';
$lang_module['sendmaildl_st0'] = 'Chưa gửi';
$lang_module['sendmaildl_st1'] = 'Thành công';
$lang_module['sendmaildl_st2'] = 'Thất bại';
$lang_module['sendmaildl_status'] = 'Trạng thái';

$lang_module['report'] = 'Xuất báo cáo';
$lang_module['report_excel'] = 'Xuất excel';
$lang_module['report_view'] = 'Xem trên site';
$lang_module['report_sel_field'] = 'Chọn trường dữ liệu';
$lang_module['report_field_stt'] = 'STT';
$lang_module['report_field_full_name'] = 'Họ và tên';
$lang_module['report_field_birthday'] = 'Ngày sinh';
$lang_module['report_field_workplace'] = 'Cơ quan làm việc hiện tại';
$lang_module['report_field_email'] = 'Email';
$lang_module['report_field_phone'] = 'Điện thoại';
$lang_module['report_field_belgiumschool'] = 'Trường đã học tại Bỉ';
$lang_module['report_field_vnschool'] = 'Trường đã học tại Việt Nam';
$lang_module['report_field_course'] = 'Khóa';
$lang_module['report_field_studytime'] = 'Thời gian học';
$lang_module['report_field_studytime1'] = 'Thời gian học từ';
$lang_module['report_field_edutype'] = 'Loại hình đào tạo';
$lang_module['report_field_branch'] = 'Ngành học';
$lang_module['report_field_concernarea'] = 'Lĩnh vực quan tâm';
$lang_module['report_field_contactinfo'] = 'Thông tin liên hệ';
$lang_module['report_field_othernote'] = 'Ghi chú';
$lang_module['report_error_field'] = 'Lỗi: Vui lòng chọn ít nhất một trường dữ liệu để hiển thị hoặc xuất ra excel';
$lang_module['report_error_group_id'] = 'Lỗi: Vui lòng chọn ít nhất một chi hội để hiển thị hoặc xuất ra excel';
$lang_module['report_showhide'] = 'Hiển thị/Ẩn các điều kiện';

$lang_module['main'] = 'Danh sách hội viên';
$lang_module['main_have_noginfo'] = 'Các chi hội <strong>%s</strong> chưa được thiết lập mã chi hội, chưa thể nhập hội viên vào đó. Mời nhấp vào đây để thiết lập';
$lang_module['main_no_group'] = 'Bạn chưa được cấp quyền quản lý các chi hội nào do đó không thể xem danh sách hội viên tại đây!';
