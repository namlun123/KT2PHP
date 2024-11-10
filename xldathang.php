<?php
session_start();
include("connect.inp");

// Lấy thông tin từ form
$nguoinhanhang = $_POST['nguoinhan'];
$diachinhanhang = $_POST['diachi'];
$sodienthoai = $_POST['sdt'];
$province = $_POST['province'];  // Thành phố từ form
$slmahang = $_POST['slmahang'];

// Kiểm tra nếu người dùng đã đăng nhập, lấy tên người dùng từ session
if (isset($_SESSION["user"])) {
    $user = $_SESSION["user"];
} else {
    // Người dùng chưa đăng nhập, sử dụng session ID
    $user = session_id();
}

// Kiểm tra nếu có đơn đặt hàng chưa thanh toán của người dùng
$check_order_query = "SELECT * FROM dondathang WHERE nguoidathang='$user' AND chedo=0";
$result = $con->query($check_order_query);

if ($result->num_rows > 0) {
    // Nếu có đơn đặt hàng đang chờ, cập nhật thông tin nhận hàng
    $update_dondathang = "UPDATE dondathang SET nguoinhanhang='$nguoinhanhang', 
        sodienthoai='$sodienthoai', diachinhanhang='$diachinhanhang', thanhpho='$province', chedo=1
        WHERE nguoidathang='$user' AND chedo=0";
    $con->query($update_dondathang);
    // Lấy số hóa đơn của đơn hàng hiện tại
    $order = $result->fetch_assoc();
    $sohoadon = $order['sohoadon'];
} else {
    // Nếu chưa có đơn đặt hàng, tạo mới
    $insert_dondathang = "INSERT INTO dondathang (nguoidathang, nguoinhanhang, sodienthoai, diachinhanhang, thanhpho, chedo)
        VALUES ('$user', '$nguoinhanhang', '$sodienthoai', '$diachinhanhang', '$province', 1)";
    $con->query($insert_dondathang);

    // Lấy mã số hóa đơn của đơn hàng mới tạo
    $sohoadon = $con->insert_id;
}

// Lặp qua các sản phẩm trong giỏ hàng và cập nhật chi tiết đơn hàng
for ($i = 1; $i <= $slmahang; $i++) {
    $soluong = $_POST['soluong' . $i];
    $mahang = $_POST['mahang' . $i];

    // Kiểm tra xem sản phẩm đã tồn tại trong chi tiết đặt hàng chưa
    $check_product_query = "SELECT * FROM chitietdathang WHERE sohoadon='$sohoadon' AND mahang='$mahang'";
    $product_result = $con->query($check_product_query);

    // Lấy giá sản phẩm từ bảng sanpham
    $query_gia = "SELECT giahang FROM sanpham WHERE mahang='$mahang'";
    $gia_result = $con->query($query_gia);

    if ($gia_result->num_rows > 0) {
        $gia_row = $gia_result->fetch_assoc();
        $giaban = $gia_row['giahang'];

        if ($product_result->num_rows > 0) {
            // Nếu có, cập nhật số lượng và giá bán
            $update_chitietdathang = "UPDATE chitietdathang SET soluong=$soluong, giaban=$giaban WHERE sohoadon='$sohoadon' AND mahang='$mahang'";
            $con->query($update_chitietdathang);
        } else {
            // Nếu không, thêm sản phẩm vào chi tiết đặt hàng
            $insert_chitietdathang = "INSERT INTO chitietdathang (sohoadon, mahang, soluong, giaban) VALUES ('$sohoadon', '$mahang', $soluong, $giaban)";
            $con->query($insert_chitietdathang);
        }
    } else {
        echo "Không tìm thấy sản phẩm với mã hàng $mahang.";
    }
}
// Sau khi đặt hàng thành công
if (isset($_SESSION['cart'])) {
    // Reset giỏ hàng cho người dùng chưa đăng nhập
    unset($_SESSION['cart']); // Xóa giỏ hàng khỏi session
}


// Chuyển hướng đến trang đơn đặt hàng
header("Location: dondathang.php?sohoadon=$sohoadon");
$con->close();
exit();
?>