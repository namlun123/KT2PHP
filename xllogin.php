<?php
session_start();
include("connect.inp");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tentaikhoan = $_POST["user"];
    $matkhau = $_POST["pass"];

    // Truy vấn thông tin người dùng từ cơ sở dữ liệu
    $sql = "SELECT tentaikhoan, matkhau, quyen, hoatdong FROM tblnguoidung WHERE tentaikhoan = '$tentaikhoan'";
    $result = $con->query($sql);

    // Kiểm tra nếu người dùng tồn tại và tài khoản đang hoạt động
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Xác minh mật khẩu và trạng thái hoạt động của tài khoản
        if ($matkhau == $row["matkhau"] && $row["hoatdong"] == 1) {
            $_SESSION["user"] = $row["tentaikhoan"];
            $_SESSION["permiss"] = $row["quyen"];

            // Cập nhật thời gian đăng nhập gần nhất
            $update_last_login = "UPDATE tblnguoidung SET lastlogin = NOW() WHERE tentaikhoan = '$tentaikhoan'";
            $con->query($update_last_login);

            header("location:index.php");
            exit();
        } else {
            // Thiết lập thông báo lỗi nếu mật khẩu sai hoặc tài khoản không hoạt động
            $_SESSION["error_message"] = "Tên đăng nhập hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.";
            header("location:index.php");
            exit();
        }
    } else {
        // Thiết lập thông báo lỗi nếu người dùng không tồn tại
        $_SESSION["error_message"] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        header("location:index.php");
        exit();
    }
}
?>
