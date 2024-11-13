<?php
session_start();
include("connect.inp");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart']) && isset($_SESSION["user"])) {
    $mahang = $_POST['mahang'];
    $sohoadon = $_POST['sohoadon'];
    $soluong = (int)$_POST['soluong'];

    // Cập nhật số lượng trong cơ sở dữ liệu
    $sql_update = "UPDATE chitietdathang SET soluong = ? WHERE sohoadon = ? AND mahang = ?";
    $stmt = $con->prepare($sql_update);
    $stmt->bind_param("iis", $soluong, $sohoadon, $mahang);
    $stmt->execute();

    // Quay lại trang giỏ hàng sau khi cập nhật
    header("Location: giohang.php");
    exit();
}
?>