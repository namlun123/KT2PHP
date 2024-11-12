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
        // Nếu người dùng chưa đăng nhập, xóa sản phẩm khỏi giỏ hàng tạm thời (dựa trên session_id)

        // Lấy session_id để xác định giỏ hàng tạm thời
        $sessionID = session_id();

        // Kiểm tra xem giỏ hàng tạm thời có tồn tại trong cơ sở dữ liệu không
        $sql = "SELECT chitietdathang.sohoadon, chitietdathang.mahang 
                FROM chitietdathang 
                INNER JOIN dondathang ON chitietdathang.sohoadon = dondathang.sohoadon
                WHERE dondathang.chedo = 0 AND dondathang.nguoidathang = '$sessionID' AND chitietdathang.mahang = '$mahang'";

        $result = $con->query($sql);

        if ($result->num_rows > 0) {
            // Xóa sản phẩm khỏi cơ sở dữ liệu
            $sqlDelete = "DELETE FROM chitietdathang WHERE sohoadon IN (SELECT sohoadon FROM dondathang WHERE nguoidathang = '$sessionID' AND chedo = 0) AND mahang = '$mahang'";

            if ($con->query($sqlDelete) === TRUE) {
                $_SESSION['message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
                header("Location: giohang.php");
                exit();
            } else {
                $_SESSION['message'] = 'Lỗi khi xóa sản phẩm: ' . $con->error;
                header("Location: giohang.php");
                exit();
            }
        } else {
            $_SESSION['message'] = 'Sản phẩm không tồn tại trong giỏ hàng tạm thời.';
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