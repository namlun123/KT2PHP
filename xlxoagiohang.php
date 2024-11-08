<?php
include("connect.inp");
session_start();

if (isset($_GET['mahang']) && isset($_GET['sohoadon'])) {
    $mahang = $_GET['mahang'];
    $sohoadon = $_GET['sohoadon'];

    // Kiểm tra xem người dùng có quyền xóa sản phẩm này hay không
    $user = isset($_SESSION['user']) ? $_SESSION['user'] : 'admin';

    $sql = "DELETE FROM chitietdathang WHERE mahang = '$mahang' AND sohoadon = '$sohoadon'";
    if ($con->query($sql) === TRUE) {
        echo "<script>alert('Xóa sản phẩm thành công!'); window.location.href='giohang.php';</script>";
    } else {
        echo "<script>alert('Xóa sản phẩm thất bại!'); window.location.href='giohang.php';</script>";
    }
} else {
    echo "<script>alert('Thông tin không đầy đủ để xóa!'); window.location.href='giohang.php';</script>";
}

$con->close();
?> 