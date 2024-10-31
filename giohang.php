<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <style>
        table {
            width: 70%;
            margin-left: 15%;
        }
        table, tr, td {
            border: 1px solid;
        }
    </style>
    <script>
        // Hàm xác nhận xóa
        function ktraxoa() {
            return confirm("Bạn có muốn xóa không?");
        }

        // Hàm hiển thị thông báo lỗi
        function showError(message) {
            alert(message);
        }
        
        // Hàm tính thành tiền khi thay đổi số lượng
        function tinhtien(row) {
            var soluong = document.getElementById("soluong" + row).value;
            var gia = document.getElementById("gia" + row).innerText;
            var thanhtien = soluong * parseFloat(gia);
            document.getElementById("thanhtien" + row).innerText = thanhtien + " VNĐ";
            tinhTongTien(); // Tính lại tổng tiền khi thay đổi số lượng
        }

        // Hàm tính tổng tiền của giỏ hàng
        function tinhTongTien() {
            var total = 0;
            var rows = document.getElementsByClassName("thanhtien");
            for (var i = 0; i < rows.length; i++) {
                total += parseFloat(rows[i].innerText);
            }
            document.getElementById("tongtien").innerText = total + " VNĐ";
        }

        // Hiển thị thông báo nếu sản phẩm đã được xóa
        window.onload = function() {
            if (new URLSearchParams(window.location.search).get('deleted') === 'true') {
                alert("Sản phẩm đã được xóa khỏi giỏ hàng thành công!");
            }

            // Hiển thị thông báo lỗi
            const error = new URLSearchParams(window.location.search).get('error');
            if (error) {
                let message;
                switch (error) {
                    case '1':
                        message = "Có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng. Vui lòng thử lại.";
                        break;
                    case '2':
                        message = "Không tìm thấy đơn hàng phù hợp hoặc đơn hàng đã hoàn thành.";
                        break;
                    case '3':
                        message = "Yêu cầu không hợp lệ. Vui lòng thử lại.";
                        break;
                }
                alert(message);
            }
        };
    </script>
</head>
<body>
    <?php
        include("connect.inp");
        session_start();
        $user = isset($_SESSION["user"]) ? $_SESSION["user"] : 'admin';

        // Lấy dữ liệu giỏ hàng của người dùng
        $sql = "SELECT chitietdathang.sohoadon, chitietdathang.mahang, tenhang, hinhanh, giaban, chitietdathang.soluong 
            FROM sanpham 
            INNER JOIN chitietdathang ON sanpham.mahang = chitietdathang.mahang
            INNER JOIN dondathang ON dondathang.sohoadon = chitietdathang.sohoadon
            WHERE chedo = 0 AND nguoidathang = '$user' AND chitietdathang.nguoixoa is null";
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
                    <td class='thanhtien' id='thanhtien$i'>$thanhtien VNĐ</td>
                    <td><a href='xlxoagiohang.php?mahang={$row['mahang']}&sohoadon={$row['sohoadon']}'>Xóa</a></td>
                </tr>";
                $i++;
            }
            echo "</table>";
            echo "<div style='text-align: center; margin-top: 20px; font-weight: bold;'>Tổng tiền: <span id='tongtien'>0 VNĐ</span></div>";
            echo "<script>tinhTongTien();</script>"; // Tính tổng tiền khi tải trang
            echo "<input type='hidden' value='$i' name='slmahang'>";
        } else {
            echo "<p style='text-align: center;'>Giỏ hàng của bạn hiện đang trống.</p>";
        }
        $con->close();
    ?>
    <div id="dathang" style="margin-left: 20%">
        <h3>Thông tin giao hàng</h3>
        Người nhận hàng:<input type="text" name="nguoinhan"><br>
        Địa chỉ nhận hàng:<input type="text" name="diachi"><br>
        Số điện thoại liên hệ:<input type="text" name="sodt"><br>
        <input type='submit' value='Đặt hàng'>
    </div>
    </form>
</body>
</html>
