<?php

include("connect.inp");
// Đặt TTL cho session
$session_lifetime = 300; // 5 phút
ini_set('session.gc_maxlifetime', $session_lifetime);
setcookie(session_name(), session_id(), time() + $session_lifetime);

session_start();
// Thiết lập múi giờ cho Việt Nam (UTC+7)
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kiểm tra thời gian của session_id
if (!isset($_SESSION['session_start_time'])) {
    // Nếu session mới được tạo, lưu thời gian bắt đầu
    $_SESSION['session_start_time'] = time();
}

// Tính toán thời gian đã trôi qua kể từ khi session bắt đầu
$elapsed_time = time() - $_SESSION['session_start_time'];

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION["user"])) {
    // Nếu người dùng đã đăng nhập, lấy tên người dùng từ session
    $user = $_SESSION["user"];
    echo "Xin chào, " . $user . "!";
} else {
    // Nếu chưa đăng nhập, hiển thị thông báo
    echo "Bạn chưa đăng nhập.";
}
// Kiểm tra xem session đã hết hạn hay chưa
if ($elapsed_time > $session_lifetime && !isset($_SESSION['user'])) {
// Nếu session đã hết hạn, hủy session
    session_unset();     // Xóa tất cả dữ liệu session
    session_destroy();   // Hủy session
    echo "Session đã hết hạn và bị hủy. ";
} else {
    // Cập nhật lại thời gian session nếu session vẫn còn hoạt động
    $_SESSION['session_start_time'] = time();
}
// Lấy session ID 
$sessionID = session_id();
// Xóa các chi tiết đơn hàng tạm cũ đã quá thời gian (vượt quá session_lifetime)
$sql_delete_old_order_details = "DELETE FROM chitietdathang WHERE sohoadon IN (
                                SELECT sohoadon FROM dondathang 
                                WHERE chedo = 0 
                                AND nguoidathang = '" . session_id() . "' 
                                AND TIMESTAMPDIFF(SECOND, ngaychonhang, NOW()) > $session_lifetime
 )";
if ($con->query($sql_delete_old_order_details) === TRUE) {
    //echo "Chi tiết đơn hàng cũ đã được xóa.<br>";
    } else {
    echo "Lỗi khi xóa chi tiết đơn hàng: " . $con->error . "<br>";
}

// Xóa các đơn hàng tạm cũ đã quá thời gian (vượt quá session_lifetime)
$sql_delete_old_orders = "DELETE FROM dondathang 
                        WHERE chedo = 0 
                        AND nguoidathang = '" . session_id() . "' 
                        AND TIMESTAMPDIFF(SECOND, ngaychonhang, NOW()) > $session_lifetime";
if ($con->query($sql_delete_old_orders) === TRUE) {
    //echo "Đơn hàng cũ đã được xóa.<br>";
    } else {
    echo "Lỗi khi xóa đơn hàng: " . $con->error . "<br>";
}

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Xóa thông báo sau khi hiển thị
}

// Lấy dữ liệu giỏ hàng của người dùng
if (isset($_SESSION["user"])) {
    // Nếu đã đăng nhập, lấy giỏ hàng từ cơ sở dữ liệu
    $sql = "SELECT chitietdathang.sohoadon, chitietdathang.mahang , tenhang, hinhanh, giaban, chitietdathang.soluong as soluong_dagui, sanpham.soluong
        FROM sanpham 
        INNER JOIN chitietdathang ON sanpham.mahang = chitietdathang.mahang
        INNER JOIN dondathang ON dondathang.sohoadon = chitietdathang.sohoadon
        WHERE chedo = 0 AND nguoidathang = '$user' ";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        
        echo "<form action='xldathang.php' method='post'>"; // Đặt id cho form chính
        echo "<table><tr><td>STT</td><td>Mã hàng</td><td>Tên hàng</td><td>Hình ảnh</td><td>Số lượng</td><td>Giá bán</td><td>Thành tiền</td><td>Xóa</td></tr>";

        $i = 1;
        while ($row = $result->fetch_assoc()) {
            $thanhtien = $row['giaban'] * $row['soluong_dagui'];
            $mahang = $row['mahang'];
            $soluong_tonkho = $row['soluong'];
            // Định dạng thành tiền với dấu phẩy
            $formatted_thanhtien = number_format($thanhtien, 0, ',', '.'); // Định dạng số không có phần thập phân và ngăn cách hàng nghìn bằng dấu chấm
            echo "<tr>
                <td>$i</td>
                <td>{$row['mahang']}<input type='hidden' name='mahang$i' value='{$row['mahang']}'></td>
                <td>{$row['tenhang']}</td>
                <td><img src='image/{$row["hinhanh"]}' alt='{$row["tenhang"]}' style='width: 50px; height: 50px;'></td>
               <td>
                    <input type='number' id='soluong$i' value='{$row['soluong_dagui']}' name='soluong$i' min='1' max='$soluong_tonkho' onchange='checkQuantity($i, $soluong_tonkho); tinhtien($i)'>
                    <span id='warning$i' style='color: red; display: none;'>Hiện trong kho chỉ còn $soluong_tonkho! Bạn vui lòng chọn ít hơn.</span>
                </td>   
                <td id='gia$i'>{$row['giaban']}</td>
                <td class='thanhtien' id='thanhtien$i' data-thanhtien='{$thanhtien}'>{$formatted_thanhtien} VNĐ</td>
                <td><a href='xlxoaspgiohang.php?mahang={$row['mahang']}&sohoadon={$row['sohoadon']}' onclick='return ktraxoa();'>Xóa</a></td>
            </tr>";
            $i++;
        }
        echo "</table>";
    
        // Thêm nút xác nhận
        echo "<div style='text-align: center; margin-top: 20px;'>
            <button type='button' onclick='if (confirm(\"Bạn có muốn xóa giỏ hàng không?\")) { window.location.href=\"xlxoagio.php\"; }' class='delete-btn'>Xóa giỏ hàng</button>
            <button type='button' onclick=\"window.location.href='index.php'\" class='continue-shopping-btn'>Tiếp tục mua hàng</button>
        </div>";
        echo "<input type='hidden' value='$i' name='slmahang'>";


    } else {
        echo "<p style='text-align: center;'>Giỏ hàng của bạn hiện đang trống.</p>";
        echo "<div style='display: flex; justify-content: center; align-items: center;'>
        <button onclick=\"window.location.href='index.php'\" class='continue-shopping-btn'>Quay lại trang chủ</button>
      </div>";

    }
} else {
    
    // Nếu người dùng chưa đăng nhập, lấy giỏ hàng từ session
    // Sử dụng session_id để lấy giỏ hàng tạm thời từ cơ sở dữ liệu
    $sql = "SELECT chitietdathang.sohoadon, chitietdathang.mahang, tenhang, hinhanh, giaban, chitietdathang.soluong as soluong_dagui, sanpham.soluong
            FROM sanpham 
            INNER JOIN chitietdathang ON sanpham.mahang = chitietdathang.mahang
            INNER JOIN dondathang ON dondathang.sohoadon = chitietdathang.sohoadon
            WHERE chedo = 0 AND nguoidathang = '$sessionID' "; // Dùng session_id() để xác định giỏ hàng tạm
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        
        echo "<form action='xldathang.php' method='post'>";
        echo "<table><tr><td>STT</td><td>Mã hàng</td><td>Tên hàng</td><td>Hình ảnh</td><td>Số lượng</td><td>Giá bán</td><td>Thành tiền</td><td>Xóa</td></tr>";

        $i = 1;
        while ($row = $result->fetch_assoc()) {
            $thanhtien = $row['giaban'] * $row['soluong_dagui'];
            $mahang = $row['mahang'];
            $soluong_tonkho = $row['soluong'];
             // Định dạng thành tiền với dấu phẩy
            $formatted_thanhtien = number_format($thanhtien, 0, ',', '.'); // Định dạng số không có phần thập phân và ngăn cách hàng nghìn bằng dấu chấm
            echo "<tr>
                <td>$i</td>
                <td>{$row['mahang']}<input type='hidden' value='{$row['mahang']}' name='mahang$i'></td>
                <td>{$row['tenhang']}</td>
                <td><img src='image/{$row["hinhanh"]}' alt='{$row["tenhang"]}' style='width: 50px; height: 50px;'></td>
                <td>
                    <input type='number' id='soluong$i' value='{$row['soluong_dagui']}' name='soluong$i' min='1' max='$soluong_tonkho' onchange='checkQuantity($i, $soluong_tonkho); tinhtien($i)'>
                    <span id='warning$i' style='color: red; display: none;'>Hiện trong kho chỉ còn $soluong_tonkho! Bạn vui lòng chọn ít hơn.</span>
                </td>           
                <td id='gia$i'>{$row['giaban']}</td>
                <td class='thanhtien' id='thanhtien$i' data-thanhtien='{$thanhtien}'>{$formatted_thanhtien} VNĐ</td>
                <td><a href='xlxoaspgiohang.php?mahang={$row['mahang']}&sohoadon={$row['sohoadon']}' onclick='return ktraxoa();'>Xóa</a></td>
            </tr>";
            $i++;
        }
        echo "</table>";
    
        echo "<div style='text-align: center; margin-top: 20px;'>";
        echo "<button onclick='if (confirm(\"Bạn có muốn xóa giỏ hàng không?\")) { window.location.href=\"xlxoagio.php\"; }' class='delete-btn'>Xóa giỏ hàng</button>";
        echo "<button onclick=\"window.location.href='index.php'\" class='continue-shopping-btn'>Tiếp tục mua hàng</button>";
        echo "</div>";
        echo "<input type='hidden' value='$i' name='slmahang'>";
    } else {
        echo "<p style='text-align: center;'>Giỏ hàng của bạn hiện đang trống.</p>";
        echo "<div style='display: flex; justify-content: center; align-items: center;'>
        <button onclick=\"window.location.href='index.php'\" class='continue-shopping-btn'>Quay lại trang chủ</button>
      </div>";
    }
}
$con->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="css/giohang.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function checkQuantity(index, maxQuantity) {
    const quantityInput = document.getElementById(`soluong${index}`);
    const warningMessage = document.getElementById(`warning${index}`);
    
    if (quantityInput.value > maxQuantity) {
        warningMessage.style.display = 'block';
        quantityInput.value = maxQuantity;
    } else {
        warningMessage.style.display = 'none';
    }
}

    function validateQuantities() {
        let isValid = true;
        const itemCount = document.querySelectorAll('input[name^="soluong"]').length;

        for (let i = 1; i <= itemCount; i++) {
            const quantityInput = document.getElementById(`soluong${i}`);
            const maxQuantity = parseInt(quantityInput.getAttribute("max"), 10);

            if (quantityInput.value > maxQuantity) {
                document.getElementById(`warning${i}`).style.display = 'block';
                isValid = false;
            }
        }
        return isValid;
    }
        // Hàm xác nhận xóa
        function ktraxoa() {
            return confirm("Bạn có muốn xóa không?");
        }

        function showError(message) {
                    alert(message);
        }
        
        // Tính thành tiền mỗi khi số lượng thay đổi
        function tinhtien(row) {
                var soluong = document.getElementById("soluong" + row).value;
                var gia = document.getElementById("gia" + row).innerText;
                var thanhtien = soluong * parseFloat(gia);
                document.getElementById("thanhtien" + row).innerText = thanhtien.toLocaleString() + " VNĐ";
                // Cập nhật lại giá trị data-thanhtien để hàm tinhTongTien() có thể tính chính xác
                document.getElementById("thanhtien" + row).setAttribute("data-thanhtien", thanhtien);
                tinhTongTien();
        }
        function tinhTongTien() {
            var total = 0;
            var rows = document.getElementsByClassName("thanhtien");

            // Lặp qua từng dòng và tính tổng tiền
            for (var i = 0; i < rows.length; i++) {
                // Lấy giá trị gốc từ data-thanhtien
                var price = parseFloat(rows[i].getAttribute("data-thanhtien"));
                if (!isNaN(price)) {
                    total += price;
                }
            }

            // Tính VAT (10%)
            var VAT = total * 0.1;

            // Lấy chi phí vận chuyển
            var shipping = parseFloat(document.getElementById("shippingCost").innerText.replace(" VNĐ", "").replace(/,/g, "")) || 0;

            // Tính tổng cuối cùng
            var finalAmount = total + VAT + shipping;

            // Đảm bảo giá trị không bị làm tròn và hiển thị đúng
            document.getElementById("tongtien").innerText = formatCurrency(total);
            document.getElementById("VAT").innerText = formatCurrency(VAT);
            document.getElementById("thanhTienTong").innerText = formatCurrency(finalAmount);
        }

        // Hàm định dạng tiền tệ
        function formatCurrency(amount) {
            return amount.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',') + " VNĐ";
        }


    // Tính phí vận chuyển và cập nhật thành tiền
    function calculateShipping() {
        var province = document.getElementById("province").value;
        var shippingCost = 0;

        switch (province) {
            case "Hà Nội":
                shippingCost = 30000;
                break;
            case "Hà Giang":
            case "Hà Nam":
            case "Hải Dương":
            case "Hải Phòng":
            case "Hòa Bình":
            case "Hưng Yên":
            case "Lai Châu":
            case "Lạng Sơn":
            case "Ninh Bình":
            case "Phú Thọ":
            case "Quảng Ninh":
            case "Sơn La":
            case "Thái Bình":
            case "Thái Nguyên":
            case "Tuyên Quang":
            case "Yên Bái":
                shippingCost = 40000;
                break;
            case "Đà Nẵng":
            case "Bình Định":
            case "Hà Tĩnh":
            case "Thanh Hóa":
            case "Khánh Hòa":
            case "Nghệ An":
            case "Ninh Thuận":
            case "Phú Yên":
            case "Quảng Bình":
            case "Quảng Nam":
            case "Quảng Ngãi":
            case "Quảng Trị":
            case "Thừa Thiên Huế":
            case "Vĩnh Phúc":
                shippingCost = 45000;
                break;
            case "An Giang":
            case "Bà Rịa - Vũng Tàu":
            case "Bạc Liêu":
            case "Bến Tre":
            case "Bình Dương":
            case "Bình Phước":
            case "Cà Mau":
            case "Cần Thơ":
            case "Đắk Lắk":
            case "Đắk Nông":
            case "Gia Lai":
            case "Kiên Giang":
            case "Kon Tum":
            case "Lâm Đồng":
            case "Sóc Trăng":
            case "Tây Ninh":
            case "Long An":
            case "Tiền Giang":
            case "Trà Vinh":
            case "Vĩnh Long":
                shippingCost = 50000;
                break;
            default:
                shippingCost = 0;
        }

        document.getElementById("shippingCost").innerText = formatCurrency(shippingCost);
        tinhTongTien();
    }

    // Khi trang tải lại, tính tổng tiền và các chi phí khác
    window.onload = function() {
        tinhTongTien();
    };
    </script>
</head>
<body>
    <div id="dathang">
        <h3>Thông tin giao hàng</h3>
        Người nhận hàng:<input type="text" name="nguoinhan" required><br>
        <label for="province">Tỉnh/Thành phố:</label>
        <select id="province" name="province" onchange="calculateShipping()" required>
            <option value="">Chọn tỉnh/thành phố</option>
            <option value="Hà Nội">Hà Nội</option>
            <option value="An Giang">An Giang</option>
            <option value="Bà Rịa - Vũng Tàu">Bà Rịa - Vũng Tàu</option>
            <option value="Bạc Liêu">Bạc Liêu</option>
            <option value="Bến Tre">Bến Tre</option>
            <option value="Bình Dương">Bình Dương</option>
            <option value="Bình Phước">Bình Phước</option>
            <option value="Bình Định">Bình Định</option>
            <option value="Cà Mau">Cà Mau</option>
            <option value="Cần Thơ">Cần Thơ</option>
            <option value="Đà Nẵng">Đà Nẵng</option>
            <option value="Đắk Lắk">Đắk Lắk</option>
            <option value="Đắk Nông">Đắk Nông</option>
            <option value="Gia Lai">Gia Lai</option>
            <option value="Hà Giang">Hà Giang</option>
            <option value="Hà Nam">Hà Nam</option>
            <option value="Hà Tĩnh">Hà Tĩnh</option>
            <option value="Hải Dương">Hải Dương</option>
            <option value="Hải Phòng">Hải Phòng</option>
            <option value="Hòa Bình">Hòa Bình</option>
            <option value="Hưng Yên">Hưng Yên</option>
            <option value="Khánh Hòa">Khánh Hòa</option>
            <option value="Kiên Giang">Kiên Giang</option>
            <option value="Kon Tum">Kon Tum</option>
            <option value="Lai Châu">Lai Châu</option>
            <option value="Lâm Đồng">Lâm Đồng</option>
            <option value="Lạng Sơn">Lạng Sơn</option>
            <option value="Nghệ An">Nghệ An</option>
            <option value="Ninh Bình">Ninh Bình</option>
            <option value="Ninh Thuận">Ninh Thuận</option>
            <option value="Phú Thọ">Phú Thọ</option>
            <option value="Phú Yên">Phú Yên</option>
            <option value="Quảng Bình">Quảng Bình</option>
            <option value="Quảng Nam">Quảng Nam</option>
            <option value="Quảng Ngãi">Quảng Ngãi</option>
            <option value="Quảng Ninh">Quảng Ninh</option>
            <option value="Quảng Trị">Quảng Trị</option>
            <option value="Sóc Trăng">Sóc Trăng</option>
            <option value="Sơn La">Sơn La</option>
            <option value="Tây Ninh">Tây Ninh</option>
            <option value="Thái Bình">Thái Bình</option>
            <option value="Thái Nguyên">Thái Nguyên</option>
            <option value="Thanh Hóa">Thanh Hóa</option>
            <option value="Thừa Thiên Huế">Thừa Thiên Huế</option>
            <option value="Tiền Giang">Tiền Giang</option>
            <option value="Trà Vinh">Trà Vinh</option>
            <option value="Tuyên Quang">Tuyên Quang</option>
            <option value="Vĩnh Long">Vĩnh Long</option>
            <option value="Vĩnh Phúc">Vĩnh Phúc</option>
            <option value="Yên Bái">Yên Bái</option>
        </select><br>
        Địa chỉ:<input type="text" name="diachi" required><br>
        Số điện thoại:<input type="text" name="sdt" required><br>

        <!-- <label for="shippingCost">Phí vận chuyển:</label>
        <span id="shippingCost">0 VNĐ</span><br> -->
        <input type='hidden' name='vat' value="0">
        <input type='hidden' name='tongtien' value="0">
        <input type='hidden' name='shipping' value="0">
        <form action="xldathang.php" method="post">
        <input type="submit" value="Đặt hàng">
        </form>
    </div>
    <?php
     echo "<div style='text-align: center; margin-top: 20px; font-weight: bold;'>Tổng tiền: <span id='tongtien'>0 VNĐ</span></div>";
     echo "<script>tinhTongTien();</script>"; // Tính tổng tiền khi tải trang
     echo "<div style='text-align: center; margin-top: 20px; font-weight: bold;'>VAT: <span id='VAT'>0 VNĐ</span></div>";
     echo "<div style='text-align: center; margin-top: 20px; font-weight: bold;'>Phí vận chuyển: <span id='shippingCost'>0 VNĐ</span></div>";
     echo "<div style='text-align: center; margin-top: 20px; font-weight: bold;'>Thành tiền: <span id='thanhTienTong'>0 VNĐ</span></div>";
     ?>
</body>
</html>



