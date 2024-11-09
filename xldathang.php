<?php
session_start();
include("connect.inp");

$nguoinhanhang = $_POST['nguoinhan'];
$diachinhanhang = $_POST['diachi'];
$sodienthoai = $_POST['sdt'];
$province = $_POST['province'];  // Add province
$user = $_SESSION["user"];
$slmahang = $_POST['slmahang'];

// Update order with shipping information
$update_dondathang = "UPDATE dondathang SET nguoinhanhang='$nguoinhanhang', 
    sodienthoai='$sodienthoai', diachinhanhang='$diachinhanhang', thanhpho='$province', chedo=1
    WHERE nguoidathang='$user' AND chedo=0";

if ($con->query($update_dondathang) === TRUE) {
    echo "Cập nhật thông tin giao hàng thành công.";
} else {
    echo "Lỗi: " . $con->error;
}

// Update order item quantities
for ($i = 1; $i < $slmahang; $i++) {
    $soluong = $_POST['soluong' . $i];
    $mahang = $_POST['mahang' . $i];
    $update_chitietdathang = "UPDATE chitietdathang SET soluong=$soluong
        WHERE mahang='$mahang'";
    $con->query($update_chitietdathang);
}

header('location:dondathang.php');
?>