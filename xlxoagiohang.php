<?php
session_start();
include("connect.inp");

// Kiểm tra xem người dùng có đang đăng nhập hay không và tham số mã sản phẩm, số hóa đơn có tồn tại
if (isset($_SESSION['user']) && isset($_GET['mahang']) && isset($_GET['sohoadon'])) {
    $user = $_SESSION['user'];
    $mahang = $_GET['mahang'];
    $sohoadon = $_GET['sohoadon'];
    $ngayxoa = date("Y-m-d H:i:s"); // Lấy thời gian hiện tại

    // Kiểm tra xem đơn hàng có thuộc về người dùng và có trạng thái chưa hoàn thành
    $sql_check_order = "SELECT * FROM dondathang WHERE sohoadon = $sohoadon AND nguoidathang = '$user' AND chedo = 0";
    $result_order = $con->query($sql_check_order);

    if ($result_order->num_rows > 0) {
        // Nếu đơn hàng hợp lệ, tiến hành cập nhật thông tin xóa
        $sql_update_delete_info = "UPDATE chitietdathang SET nguoixoa = '$user', ngayxoa = '$ngayxoa' WHERE sohoadon = $sohoadon AND mahang = '$mahang'";
        
        if ($con->query($sql_update_delete_info) === TRUE) {
            // Nếu cập nhật thành công, chuyển hướng về trang giỏ hàng với thông báo thành công
            header("Location: giohang.php?deleted=true");
            exit();
        } else {
            // Chuyển hướng với thông báo lỗi
            header("Location: giohang.php?error=1");
            exit();
        }
    } else {
        // Chuyển hướng với thông báo lỗi nếu không tìm thấy đơn hàng
        header("Location: giohang.php?error=2");
        exit();
    }
} else {
    // Chuyển hướng với thông báo lỗi nếu yêu cầu không hợp lệ
    header("Location: giohang.php?error=3");
    exit();
}

$con->close();

?>
