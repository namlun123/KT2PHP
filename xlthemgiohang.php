<?php
session_start(); // Bắt đầu session để lưu trữ thông tin người dùng đăng nhập
include("connect.inp"); // Kết nối đến cơ sở dữ liệu

// Lấy các giá trị từ form (mã sản phẩm, giá, và số lượng)
$masp = $_POST['Masp']; // Mã sản phẩm
$gia = $_POST['Gia'];    // Giá sản phẩm
$soluong = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Số lượng sản phẩm (mặc định là 1 nếu không có trong POST)

//$user = isset($_SESSION["user"]) ? $_SESSION["user"] : 'admin';
// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION["user"])) {
    $user = $_SESSION["user"];  // Người dùng đã đăng nhập

    // Kiểm tra xem người dùng đã có đơn đặt hàng chưa hoàn thành (chedo = 0) trong bảng dondathang
    $sql_check = "SELECT * FROM dondathang WHERE chedo = 0 AND nguoidathang = '$user'";
    $result = $con->query($sql_check);

    if ($result->num_rows == 0) { 
        // Nếu chưa có đơn hàng nào chưa hoàn thành, tạo đơn hàng mới
        $s_sohoadon = "SELECT MAX(sohoadon) as shd FROM dondathang";
        $result = $con->query($s_sohoadon);
        $row = $result->fetch_assoc();
        $sohoadon = $row["shd"] + 1; // Tăng số hóa đơn lên 1

    // Thêm đơn hàng mới vào bảng dondathang với chế độ chưa hoàn thành (chedo = 0)
    $insert_dondathang = "INSERT INTO dondathang(sohoadon, nguoidathang, chedo) VALUES($sohoadon, '$user', 0)";
    $con->query($insert_dondathang);
    } else {
        // Nếu đã có đơn hàng chưa hoàn thành, lấy số hóa đơn đó để sử dụng
        $row = $result->fetch_assoc();
        $sohoadon = $row["sohoadon"];
    }
} else {
    // Nếu người dùng chưa đăng nhập, lưu giỏ hàng vào session tạm thời (không liên kết với người dùng)
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array(); // Khởi tạo giỏ hàng nếu chưa có
    }
    
    // Kiểm tra nếu sản phẩm đã có trong giỏ hàng (session)
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['mahang'] == $masp) {
            $item['soluong'] += $soluong;  // Cập nhật số lượng nếu sản phẩm đã có trong giỏ
            $found = true;
            break;
        }
    }
    
    // Nếu sản phẩm chưa có trong giỏ, thêm mới
    if (!$found) {
        $_SESSION['cart'][] = array(
            'mahang' => $masp,
            'giaban' => $gia,
            'soluong' => $soluong
        );
    }
    
    // Chuyển hướng về trang chi tiết sản phẩm với tham số báo thành công để hiển thị popup thông báo
    header("Location: chitiet_mathang.php?Masp=$masp&added=true&quantity=$soluong");
    exit();
}

// Kiểm tra xem sản phẩm đã tồn tại trong chi tiết đặt hàng (chitietdathang) của đơn hàng này chưa
$sql_check_item = "SELECT * FROM chitietdathang WHERE sohoadon = $sohoadon AND mahang = '$masp'";
$result_item = $con->query($sql_check_item);

if ($result_item->num_rows == 0) {
    // Nếu sản phẩm chưa có trong chi tiết đặt hàng, thêm mới sản phẩm vào chi tiết đặt hàng
    $insert_chitietdathang = "INSERT INTO chitietdathang(sohoadon, mahang, giaban, soluong) VALUES($sohoadon, '$masp', $gia, $soluong)";
    $con->query($insert_chitietdathang);
} else {
    // Nếu sản phẩm đã có trong chi tiết đặt hàng, cập nhật số lượng sản phẩm trong giỏ hàng
    $row_item = $result_item->fetch_assoc();
    $new_quantity = $row_item['soluong'] + $soluong; // Tăng số lượng hiện tại với số lượng mới
    $update_chitietdathang = "UPDATE chitietdathang SET soluong = $new_quantity WHERE sohoadon = $sohoadon AND mahang = '$masp'";
    $con->query($update_chitietdathang);
}

// Chuyển hướng về trang chi tiết sản phẩm với tham số báo thành công để hiển thị popup thông báo
header("location:chitiet_mathang.php?Masp=$masp&added=true&quantity=$soluong");
?>
