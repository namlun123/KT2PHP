<?php
session_start();
include("connect.inp");

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION["user"])) {
    // Người dùng đã đăng nhập
    $user = $_SESSION["user"];

    // Xóa chi tiết đơn hàng của người dùng trong giỏ hàng
    $sql_delete_details = "DELETE chitietdathang FROM chitietdathang
                           INNER JOIN dondathang ON chitietdathang.sohoadon = dondathang.sohoadon
                           WHERE dondathang.nguoidathang = '$user' AND dondathang.chedo = 0";

    // Thực thi câu lệnh xóa chi tiết đơn hàng
    if ($con->query($sql_delete_details)) {
        // Thông báo xóa thành công
        $_SESSION['message'] = 'Đã xóa toàn bộ chi tiết đơn hàng trong giỏ.';
    } else {
        // Thông báo lỗi
        $_SESSION['message'] = 'Lỗi khi xóa chi tiết đơn hàng.';
    }

    // Chuyển hướng về trang giỏ hàng
    header("Location: giohang.php");
    exit();
} else {
   // Người dùng chưa đăng nhập, kiểm tra giỏ hàng trong cơ sở dữ liệu dựa trên session_id

    // Lấy session_id để xác định giỏ hàng tạm thời
    $sessionID = session_id();

    // Xóa chi tiết đơn hàng tạm thời của người dùng chưa đăng nhập trong cơ sở dữ liệu
    $sql_delete_details_temp = "DELETE chitietdathang FROM chitietdathang
    INNER JOIN dondathang ON chitietdathang.sohoadon = dondathang.sohoadon
    WHERE dondathang.nguoidathang = '$sessionID' AND dondathang.chedo = 0";

    // Thực thi câu lệnh xóa chi tiết đơn hàng tạm thời
    if ($con->query($sql_delete_details_temp)) {
        // Thông báo xóa thành công
        $_SESSION['message'] = 'Đã xóa toàn bộ giỏ hàng tạm thời.';
    } else {
        // Thông báo lỗi
        $_SESSION['message'] = 'Lỗi khi xóa giỏ hàng tạm thời.';
    }

    // Chuyển hướng về trang giỏ hàng
    header("Location: giohang.php");
    exit();
}

$con->close();
?>