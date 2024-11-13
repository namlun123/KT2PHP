<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Đặt Hàng</title>
    <link rel="stylesheet" href="css/dondathang.css">
    <link rel="stylesheet" href="css/giohang.css">
</head>
<body>
<?php
include("connect.inp");
session_start();
$user = $_SESSION["user"] ?? 'admin';

// Kiểm tra nếu người dùng đã nhấn nút "Xem tất cả đơn hàng"
$showAllOrders = isset($_GET['showAll']) ? true : false;

// Câu truy vấn SQL
$sql = "SELECT dondathang.sohoadon, dondathang.thanhpho, chitietdathang.mahang, tenhang, hinhanh, giaban, chitietdathang.soluong 
        FROM sanpham 
        INNER JOIN chitietdathang ON sanpham.mahang = chitietdathang.mahang
        INNER JOIN dondathang ON dondathang.sohoadon = chitietdathang.sohoadon
        WHERE dondathang.chedo = 1 AND dondathang.nguoidathang = '$user'";

// Nếu không yêu cầu xem tất cả đơn hàng, chỉ lấy đơn hàng mới nhất
if (!$showAllOrders) {
    $sql .= " AND dondathang.sohoadon = (SELECT MAX(sohoadon) FROM dondathang WHERE nguoidathang = '$user' AND chedo = 1)";
}
$sql .= " ORDER BY chitietdathang.sohoadon";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Sohoadon</th><th>Mã hàng</th><th>Tên hàng</th><th>Hình ảnh</th><th>Số lượng</th><th>Giá bán</th><th>Thành tiền</th><th>VAT</th><th>Phí vận chuyển</th><th>Tổng thanh toán</th><th>Xóa</th></tr>";

    $currentSohoadon = null;
    $tongTienHang = 0;
    $province = '';

    function calculateShipping($province) {
        switch ($province) {
            case "Hà Nội":
                return 30000;
            case "Hà Giang": case "Hà Nam": case "Hải Dương": case "Hải Phòng": case "Hòa Bình": case "Hưng Yên": case "Lai Châu": case "Lạng Sơn": case "Ninh Bình": case "Phú Thọ": case "Quảng Ninh": case "Sơn La": case "Thái Bình": case "Thái Nguyên": case "Tuyên Quang": case "Yên Bái":
                return 40000;
            case "Đà Nẵng": case "Bình Định": case "Thanh Hóa": case "Hà Tĩnh": case "Khánh Hòa": case "Nghệ An": case "Ninh Thuận": case "Phú Yên": case "Quảng Bình": case "Quảng Nam": case "Quảng Ngãi": case "Quảng Trị": case "Thừa Thiên Huế": case "Vĩnh Phúc":
                return 45000;
            case "An Giang": case "Bà Rịa - Vũng Tàu": case "Bạc Liêu": case "Bến Tre": case "Bình Dương": case "Bình Phước": case "Cà Mau": case "Cần Thơ": case "Long An": case "Đắk Lắk": case "Đắk Nông": case "Gia Lai": case "Kiên Giang": case "Kon Tum": case "Lâm Đồng": case "Sóc Trăng": case "Tây Ninh": case "Tiền Giang": case "Trà Vinh": case "Vĩnh Long":
                return 50000;
            default:
                return 0;
        }
    }

    while ($row = $result->fetch_assoc()) {
        if ($currentSohoadon !== $row['sohoadon']) {
            if ($currentSohoadon !== null) {
                $VAT = $tongTienHang * 0.1;
                $phiShip = calculateShipping($province);
                $tongThanhToan = $tongTienHang + $VAT + $phiShip;

                echo "<tr style='font-weight: bold; background-color: #f9f9f9;'>
                        <td colspan='6' style='text-align: right;'>Tổng cộng hóa đơn $currentSohoadon:</td>
                        <td>" . number_format($tongTienHang, 0, ',', '.') . " VNĐ</td>
                        <td>" . number_format($VAT, 0, ',', '.') . " VNĐ</td>
                        <td>" . number_format($phiShip, 0, ',', '.') . " VNĐ</td>
                        <td>" . number_format($tongThanhToan, 0, ',', '.') . " VNĐ</td>
                        <td></td>
                      </tr>";

                $tongTienHang = 0;
            }
            $currentSohoadon = $row['sohoadon'];
            $province = $row['thanhpho'];
        }

        $thanhTien = $row['giaban'] * $row['soluong'];
        $tongTienHang += $thanhTien;

        echo "<tr>
            <td>{$row['sohoadon']}</td>
            <td>{$row['mahang']}</td>
            <td>{$row['tenhang']}</td>
            <td><img src='{$row['hinhanh']}' width='50'></td>
            <td>{$row['soluong']}</td>
            <td>" . number_format($row['giaban'], 0, ',', '.') . " VNĐ</td>
            <td>" . number_format($thanhTien, 0, ',', '.') . " VNĐ</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td><a href='xoaLoai.php?mahang={$row['mahang']}&sohoadon={$row['sohoadon']}'>Xóa</a></td>
        </tr>";
    }

    $VAT = $tongTienHang * 0.1;
    $phiShip = calculateShipping($province);
    $tongThanhToan = $tongTienHang + $VAT + $phiShip;

    echo "<tr style='font-weight: bold; background-color: #f9f9f9;'>
            <td colspan='6' style='text-align: right;'>Tổng cộng hóa đơn $currentSohoadon:</td>
            <td>" . number_format($tongTienHang, 0, ',', '.') . " VNĐ</td>
            <td>" . number_format($VAT, 0, ',', '.') . " VNĐ</td>
            <td>" . number_format($phiShip, 0, ',', '.') . " VNĐ</td>
            <td>" . number_format($tongThanhToan, 0, ',', '.') . " VNĐ</td>
            <td></td>
          </tr>";

    echo "</table>";
} else {
    echo "<p style='text-align: center;'>Giỏ hàng của bạn hiện đang trống.</p>";
}

// Hiển thị nút "Xem tất cả đơn hàng" nếu chỉ hiển thị đơn hàng mới nhất
echo "<div style='text-align: center; margin-top: 20px;'>";
if (!$showAllOrders) {
    echo "<button onclick=\"window.location.href='dondathang.php?showAll=true'\" class='view-all-orders-btn'>Xem tất cả đơn hàng</button>";
} else {
    echo "<button onclick=\"window.location.href='dondathang.php'\" class='view-recent-order-btn'>Xem đơn hàng mới nhất</button>";
}
echo "<button onclick=\"window.location.href='index.php'\" class='continue-shopping-btn'>Quay lại trang chủ</button>";
echo "</div>";

$con->close();
?>
</body>
</html>
