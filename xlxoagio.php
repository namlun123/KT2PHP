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
    // Người dùng chưa đăng nhập, kiểm tra giỏ hàng trong session
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']); // Xóa giỏ hàng trong session nếu có
        $_SESSION['message'] = 'Đã xóa giỏ hàng trong session.';
    } else {
        $_SESSION['message'] = 'Giỏ hàng của bạn hiện đang trống.';
    }

    // Chuyển hướng về trang giỏ hàng
    header("Location: giohang.php");
    exit();
}

$con->close();
?>
