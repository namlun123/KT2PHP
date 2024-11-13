<?php
session_start();
include("connect.inp");

// Kiểm tra nếu người dùng đã đăng nhập, nếu không thì sử dụng session ID làm thay thế
if (isset($_SESSION["user"])) {
    $user = $_SESSION["user"];
} else {
    $user = session_id(); // Sử dụng session ID nếu người dùng chưa đăng nhập
}

if (isset($_POST['slmahang'])) {
    $slmahang = intval($_POST['slmahang']);

    // Lặp qua từng mặt hàng để cập nhật số lượng
    for ($i = 1; $i <= $slmahang; $i++) {
        if (isset($_POST["mahang$i"]) && isset($_POST["soluong$i"])) {
            $mahang = $_POST["mahang$i"];
            $soluong = intval($_POST["soluong$i"]);

            // Cập nhật số lượng sản phẩm trực tiếp trong cơ sở dữ liệu
            $sql = "UPDATE chitietdathang 
                    INNER JOIN dondathang ON dondathang.sohoadon = chitietdathang.sohoadon
                    SET chitietdathang.soluong = $soluong
                    WHERE chitietdathang.mahang = '$mahang' AND dondathang.chedo = 0 AND dondathang.nguoidathang = '$user'";
            $con->query($sql);
        }
    }

    // Lưu thông báo thành công vào session
    $_SESSION['message'] = 'Cập nhật số lượng thành công!';
} else {
    $_SESSION['message'] = 'Không có dữ liệu để cập nhật.';
}

// Chuyển hướng về trang giỏ hàng
header("Location: giohang.php");
exit();
?>