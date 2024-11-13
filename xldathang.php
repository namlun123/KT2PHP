<?php
session_start();
include("connect.inp");

// Lấy thông tin từ form
$nguoinhanhang = $_POST['nguoinhan'];
$diachinhanhang = $_POST['diachi'];
$sodienthoai = $_POST['sdt'];
$province = $_POST['province'];  // Thành phố từ form
$slmahang = $_POST['slmahang'];

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION["user"])) {
    $user = $_SESSION["user"]; // Người dùng đã đăng nhập
        // Kiểm tra nếu có đơn hàng chưa hoàn thành của người dùng
        $check_order_query = "SELECT * FROM dondathang WHERE nguoidathang='$user' AND chedo=0";
        $result = $con->query($check_order_query);
    
        if ($result->num_rows > 0) {
            // Nếu có đơn đặt hàng chưa thanh toán, lấy mã hóa đơn
            $order = $result->fetch_assoc();
            $sohoadon = $order['sohoadon'];
    
            // Cập nhật thông tin nhận hàng và thay đổi trạng thái chế độ đơn hàng thành 1 (hoàn tất)
            $update_dondathang = "UPDATE dondathang SET nguoinhanhang='$nguoinhanhang', 
                sodienthoai='$sodienthoai', diachinhanhang='$diachinhanhang', thanhpho='$province', chedo=1, ngaydathang=NOW()
                WHERE sohoadon='$sohoadon' AND nguoidathang='$user' AND chedo=0";
            $con->query($update_dondathang);  // Chuyển trạng thái chế độ đơn hàng thành 1 (hoàn tất)
    }   else {
        // Nếu không có đơn hàng chưa hoàn thành, tạo đơn hàng mới
        $insert_dondathang = "INSERT INTO dondathang (nguoidathang, nguoinhanhang, sodienthoai, diachinhanhang, thanhpho, chedo)
            VALUES ('$user', '$nguoinhanhang', '$sodienthoai', '$diachinhanhang', '$province', 1)";
        $con->query($insert_dondathang);
        $sohoadon = $con->insert_id;  // Lấy mã số hóa đơn

        // Kiểm tra giỏ hàng có sản phẩm không, nếu có, chèn vào chi tiết đơn hàng
        $check_cart_query = "SELECT * FROM chitietdathang WHERE nguoidathang = '$user' AND chedo = 0";
        $cart_result = $con->query($check_cart_query);

        if ($cart_result->num_rows > 0) {
            // Chèn các sản phẩm vào chi tiết đơn hàng
            while ($cart_item = $cart_result->fetch_assoc()) {
                $mahang = $cart_item['mahang'];
                $soluong = $cart_item['soluong'];
                $giaban = $cart_item['giaban'];
                $insert_cart = "INSERT INTO chitietdathang (sohoadon, mahang, soluong, giaban)
                VALUES ('$sohoadon', '$mahang', '$soluong', '$giaban')";
                $con->query($insert_cart);
            }
        } else {
            // Nếu giỏ hàng trống, hiển thị thông báo
            $_SESSION['message'] = 'Giỏ hàng của bạn hiện đang trống.';
            echo "<button onclick=\"window.location.href='index.php'\" class='continue-shopping-btn'>Quay lại trang chủ</button>";
            exit();
        }
    }

} else {
    // Nếu người dùng chưa đăng nhập, sử dụng session ID làm guest ID
    $user = session_id();

    // Kiểm tra nếu đã có đơn hàng tạm thời của người dùng chưa đăng nhập
    $check_order_query = "SELECT * FROM dondathang WHERE nguoidathang='$user' AND chedo=0";
    $result = $con->query($check_order_query);

    if ($result->num_rows > 0) {
        // Nếu có đơn hàng tạm, cập nhật đơn hàng
        $update_dondathang = "UPDATE dondathang SET nguoinhanhang='$nguoinhanhang', 
            sodienthoai='$sodienthoai', diachinhanhang='$diachinhanhang', thanhpho='$province', chedo=1, ngaydathang=NOW()
            WHERE nguoidathang='$user' AND chedo=0";
        $con->query($update_dondathang);

        // Lấy số hóa đơn của đơn hàng hiện tại
        $order = $result->fetch_assoc();
        $sohoadon = $order['sohoadon'];
    } else {
        // Nếu chưa có đơn đặt hàng tạm, tạo mới
        $insert_dondathang = "INSERT INTO dondathang (nguoidathang, nguoinhanhang, sodienthoai, diachinhanhang, thanhpho, chedo)
            VALUES ('$user', '$nguoinhanhang', '$sodienthoai', '$diachinhanhang', '$province', 0)";
        $con->query($insert_dondathang);

        // Lấy mã số hóa đơn của đơn hàng mới tạo
        $sohoadon = $con->insert_id;
    }
}

// Lặp qua các sản phẩm trong giỏ hàng và cập nhật chi tiết đơn hàng
for ($i = 1; $i <= $slmahang; $i++) {
    $soluong = $_POST['soluong' . $i];
    $mahang = $_POST['mahang' . $i];

    // Kiểm tra xem sản phẩm đã tồn tại trong chi tiết đặt hàng chưa
    $check_product_query = "SELECT * FROM chitietdathang WHERE sohoadon='$sohoadon' AND mahang='$mahang'";
    $product_result = $con->query($check_product_query);

    // Lấy giá sản phẩm từ bảng sanpham
    $query_gia = "SELECT giahang, soluong FROM sanpham WHERE mahang='$mahang'";
    $gia_result = $con->query($query_gia);

    if ($gia_result->num_rows > 0) {
        $gia_row = $gia_result->fetch_assoc();
        $giaban = $gia_row['giahang'];
        $soluongton = $gia_row['soluong']; // Số lượng sản phẩm trong kho

        if ($soluongton >= $soluong) {
            if ($product_result->num_rows > 0) {
                // Nếu có, cập nhật số lượng và giá bán
                $update_chitietdathang = "UPDATE chitietdathang SET soluong=$soluong, giaban=$giaban WHERE sohoadon='$sohoadon' AND mahang='$mahang'";
                $con->query($update_chitietdathang);
            } else {
                // Nếu không, thêm sản phẩm vào chi tiết đặt hàng
                $insert_chitietdathang = "INSERT INTO chitietdathang (sohoadon, mahang, soluong, giaban) VALUES ('$sohoadon', '$mahang', $soluong, $giaban)";
                $con->query($insert_chitietdathang);
            }

            // Cập nhật số lượng sản phẩm trong kho
            $new_soluong = $soluongton - $soluong; // Trừ số lượng đã đặt
            $update_soluong_sanpham = "UPDATE sanpham SET soluong = $new_soluong WHERE mahang = '$mahang'";
            $con->query($update_soluong_sanpham);
        } else {
            echo "Sản phẩm với mã hàng $mahang không đủ số lượng trong kho.";
        }
    } else {
        echo "Không tìm thấy sản phẩm với mã hàng $mahang.";
    }
}

// Chuyển hướng đến trang đơn đặt hàng
header("Location: dondathang.php?sohoadon=$sohoadon");
$con->close();
exit();
?>
