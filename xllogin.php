<?php
session_start();
include("connect.inp");

// Lấy thông tin từ form đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tentaikhoan = $_POST["user"];
    $matkhau = $_POST["pass"];

    // Kết nối đến CSDL và truy vấn thông tin người dùng
    $sql = "SELECT tentaikhoan, matkhau, quyen, hoatdong FROM tblnguoidung WHERE tentaikhoan = '$tentaikhoan'";
    $result = $con->query($sql);

    // Kiểm tra nếu người dùng tồn tại và tài khoản đang hoạt động
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Xác minh mật khẩu và kiểm tra xem tài khoản có hoạt động không
        if ($matkhau == $row["matkhau"] && $row["hoatdong"] == 1) {
            $_SESSION["user"] = $row["tentaikhoan"];
            $_SESSION["permiss"] = $row["quyen"];
            
            header("location:trangchu.php");
            exit();
        } else {
            // Nếu mật khẩu sai hoặc tài khoản không hoạt động
            header("location:login.php?error=invalid_credentials");
            exit();
        }
    } else {
        // Nếu người dùng không tồn tại
        header("location:login.php?error=invalid_credentials");
        exit();
    }
}
?>
