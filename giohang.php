<?php
        include("connect.inp");
        session_start();
        $user = isset($_SESSION["user"]) ? $_SESSION["user"] : 'admin';

        // Lấy dữ liệu giỏ hàng của người dùng
        $sql = "SELECT chitietdathang.sohoadon, chitietdathang.mahang, tenhang, hinhanh, giaban, chitietdathang.soluong 
            FROM sanpham 
            INNER JOIN chitietdathang ON sanpham.mahang = chitietdathang.mahang
            INNER JOIN dondathang ON dondathang.sohoadon = chitietdathang.sohoadon
            WHERE chedo = 0 AND nguoidathang = '$user' ";
        $result = $con->query($sql);
        $result = $con->query($sql);

        if ($result->num_rows > 0) {
            echo "<form action='xldathang.php' method='post'>";
            echo "<table><tr><td>STT</td><td>Mã hàng</td><td>Tên hàng</td><td>Hình ảnh</td><td>Số lượng</td><td>Giá bán</td><td>Thành tiền</td><td>Xóa</td></tr>";

            $i = 1;    
            while ($row = $result->fetch_assoc()) {
                $thanhtien = $row['giaban'] * $row['soluong'];
                echo "<tr>
                    <td>$i</td>
                    <td>{$row['mahang']}<input type='hidden' value='{$row['mahang']}' name='mahang$i'></td>
                    <td>{$row['tenhang']}</td>
                    <td><img src='{$row['hinhanh']}' width='50'></td>
                    <td><input type='number' id='soluong$i' value='{$row['soluong']}' name='soluong$i' min='1' onchange='tinhtien($i);'></td>
                    <td id='gia$i'>{$row['giaban']}</td>
                    <td class='thanhtien' id='thanhtien$i'>{$thanhtien} VNĐ</td>
                    <td><a href='xlxoagiohang.php?mahang={$row['mahang']}&sohoadon={$row['sohoadon']}' onclick='return ktraxoa();'>Xóa</a></td>
                </tr>";
                $i++;
            }
            echo "</table>";
            echo "<div style='text-align: center; margin-top: 20px; font-weight: bold;'>Tổng tiền: <span id='tongtien'>0 VNĐ</span></div>";
            echo "<script>tinhTongTien();</script>"; // Tính tổng tiền khi tải trang
            echo "<div style='text-align: center; margin-top: 20px; font-weight: bold;'>VAT: <span id='VAT'>0 VNĐ</span></div>";
            echo "<div style='text-align: center; margin-top: 20px; font-weight: bold;'>Thành tiền: <span id='thanhTienTong'>0 VNĐ</span></div>";

            echo "<input type='hidden' value='$i' name='slmahang'>";
        } else {
            echo "<p style='text-align: center;'>Giỏ hàng của bạn hiện đang trống.</p>";
        }
        $con->close();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333433;
        }
        h3 {
            text-align: center;
            color: #555;
        }
        table {
            width: 70%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-align: center;
        }
        td {
            text-align: center;
            vertical-align: middle;
        }
        img {
            border-radius: 8px;
        }
        #tongtien, #VAT, #thanhTienTong {
            font-size: 1.2em;
            font-weight: bold;
            color: #d9534f;
        }
        .summary {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }
        .summary div {
            margin: 10px 0;
        }
        #dathang {
            width: 70%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        #dathang input[type="text"], #dathang select {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        #dathang input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        #dathang input[type="submit"]:hover {
            background-color: #218838;
        }
        a {
            color: #d9534f;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
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
            tinhTongTien();
        }

        // Tính tổng tiền và cập nhật VAT, thành tiền
        function tinhTongTien() {
            var total = 0;
            var rows = document.getElementsByClassName("thanhtien");
            for (var i = 0; i < rows.length; i++) {
                total += parseFloat(rows[i].innerText.replace(" VNĐ", "").replace(/,/g, "")) || 0;
            }

            var VAT = total * 0.1;  // Tính VAT là 10% của tổng tiền
            var shipping = parseFloat(document.getElementById("shippingCost").innerText.replace(" VNĐ", "").replace(/,/g, "")) || 0;
            var finalAmount = total + VAT + shipping;

            // Hiển thị kết quả
            document.getElementById("tongtien").innerText = total.toLocaleString() + " VNĐ";
            document.getElementById("VAT").innerText = VAT.toLocaleString() + " VNĐ";
            document.getElementById("thanhTienTong").innerText = finalAmount.toLocaleString() + " VNĐ";
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
                case "Tiền Giang":
                case "Trà Vinh":
                case "Vĩnh Long":
                    shippingCost = 50000;
                    break;
                default:
                    shippingCost = 0;
            }

            document.getElementById("shippingCost").innerText = shippingCost.toLocaleString() + " VNĐ";
            tinhTongTien();
        }

        // Khi trang tải lại, tính tổng tiền và các chi phí khác
        window.onload = function() {
            tinhTongTien();
        };
    </script>
</head>
<body>
    <div id="dathang" style="margin-left: 20%">
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
        <label for="shippingCost">Phí vận chuyển:</label>
        <span id="shippingCost">0 VNĐ</span><br>
        <input type='hidden' name='vat' value="0">
        <input type='hidden' name='tongtien' value="0">
        <input type='hidden' name='shipping' value="0">
        <form action="xldathang.php" method="post">
        <input type="submit" value="Đặt hàng">
        </form>
    </div>
</body>
</html>