<?php
session_start();
include("connect.inp");

// Kiểm tra xem mã hàng và số hóa đơn có được truyền qua URL không
if (isset($_GET['mahang'])) {
    $mahang = $_GET['mahang'];

    // Kiểm tra xem người dùng có đang đăng nhập không
    if (isset($_SESSION["user"])) {
        $user = $_SESSION["user"];

        // Nếu người dùng đã đăng nhập, xóa sản phẩm trong cơ sở dữ liệu
        if (isset($_GET['sohoadon'])) {
            $sohoadon = $_GET['sohoadon'];

            // Xóa sản phẩm khỏi chi tiết đơn đặt hàng trong cơ sở dữ liệu
            $sql = "DELETE FROM chitietdathang WHERE sohoadon = '$sohoadon' AND mahang = '$mahang' AND EXISTS (SELECT * FROM dondathang WHERE nguoidathang = '$user' AND sohoadon = '$sohoadon')";
            if ($con->query($sql) === TRUE) {
                // Lưu thông báo vào session và chuyển hướng
                $_SESSION['message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
                header("Location: giohang.php");
                exit();
            } else {
                // Lưu lỗi vào session và chuyển hướng
                $_SESSION['message'] = 'Lỗi: ' . $con->error;
                header("Location: giohang.php");
                exit();
            }
        } else {
            $_SESSION['message'] = 'Thiếu thông tin số hóa đơn.';
            header("Location: giohang.php");
            exit();
        }
    } else {
        // Nếu người dùng chưa đăng nhập, xóa sản phẩm khỏi giỏ hàng trong session
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['mahang'] == $mahang) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
                    header("Location: giohang.php");
                    exit();
                }
            }
            // Đặt lại các chỉ số của giỏ hàng
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            $_SESSION['message'] = 'Giỏ hàng không tồn tại.';
            header("Location: giohang.php");
            exit();
        } else {
            $_SESSION['message'] = 'Giỏ hàng không tồn tại.';
            header("Location: giohang.php");
            exit();
        }
    }
} else {
    $_SESSION['message'] = 'Không có mã hàng được cung cấp.';
    header("Location: giohang.php");
    exit();
}

// Đóng kết nối
$con->close();
?>
