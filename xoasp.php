<?php
session_start();
include("connect.inp");

// Kiểm tra nếu người dùng đã đăng nhập và có quyền hợp lệ
if (!isset($_SESSION["user"])) {
    header("location:index.php");
    exit();
}

// Kiểm tra quyền của người dùng (quyền phải là 1)
if ($_SESSION["permiss"] != 1) {
    header("Location: dssp.php?status=4");  // Nếu không phải admin, chuyển hướng về danh sách sản phẩm với thông báo lỗi
    exit();
}

if (isset($_GET["Masp"])) {
    $mahang = $_GET["Masp"];
    
    // Lấy tên người dùng từ session
    $username = $_SESSION["user"];

    // Cập nhật sản phẩm để đánh dấu là đã xóa
    $sql = "UPDATE Sanpham SET is_deleted = 1, nguoixoa = '$username', ngayxoa = NOW() WHERE mahang = '$mahang'";
    
    // Thực thi câu lệnh SQL
    if ($con->query($sql) === TRUE) {
        // Cập nhật thành công, chuyển hướng lại trang danh sách sản phẩm với thông báo thành công
        header("Location: dssp.php?status=3");
    } else {
        // Nếu không thành công, chuyển hướng về trang danh sách sản phẩm với thông báo lỗi
        header("Location: dssp.php?status=2");
    }
}

$con->close();
?>