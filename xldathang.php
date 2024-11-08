<?php
    session_start();
    include("connect.inp");
    $nguoinhanhang=$_POST['nguoinhan'];
    $diachinhanhang=$_POST['diachi'];
    $sodienthoai=$_POST['sodt'];    
    $user=$_SESSION["user"];
    $slmahang=$_POST['slmahang'];
    
    $update_dondathang="Update dondathang set nguoinhanhang='$nguoinhanhang', 
    sodienthoai='$sodienthoai', diachinhanhang='$diachinhanhang', chedo=1
    where nguoidathang='$user' and chedo=0";
    
    if($con->query($update_dondathang)==TRUE){
        echo "Thanh cong";
    }
    for($i=1;$i<$slmahang;$i++){
        $soluong=$_POST['soluong'.$i];
        $mahang=$_POST['mahang'.$i];
        $update_chitietdathang="Update chitietdathang set soluong=$soluong
        where mahang='$mahang'";
        echo $update_chitietdathang;
        $con->query($update_chitietdathang);
    }
    header('location:dondathang.php');
?>