<?php 
$conn = new mysqli('localhost', 'root', '', 'kt2php');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql_loai = "SELECT * FROM loaisp";
$result_loai = $conn->query($sql_loai);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link rel="stylesheet" href="css/themsp.css"> 
    <script>
        function confirmSubmit() {
            return confirm("Bạn có chắc chắn muốn thêm sản phẩm này?");
        }

        function showError(message) {
            document.getElementById("error-message").innerText = message;
            document.getElementById("error-popup").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        }

        function closeErrorPopup() {
            document.getElementById("error-popup").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }

        <?php if (isset($_GET['message'])): ?>
            window.onload = function() {
                showError("<?php echo htmlspecialchars($_GET['message']); ?>");
            };
        <?php endif; ?>
    </script>
</head>
<body>

<h2>Thêm sản phẩm</h2>
<div class="form-container">

    <form action="xu_ly_them_sanpham.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmSubmit();">
        <label for="mahang">Mã hàng:</label>
        <input type="text" id="mahang" name="mahang" required>
        <label for="tenhang">Tên hàng:</label>
        <input type="text" id="tenhang" name="tenhang" required>
        <label for="soluong">Giá:</label>
        <input type="number" id="giahang" name="giahang" required>
        <label for="mota">Số lượng:</label>
        <input type="number" id="soluong" name="soluong">
        <label for="giahang">Mã loại:</label>
    <select id="maloai" name="maloai" required>
    <?php
    if ($result_loai->num_rows > 0) {
        while ($row_loai = $result_loai->fetch_assoc()) {
            echo "<option value='{$row_loai['Maloai']}'>{$row_loai['Maloai']}</option>"; 
        }
    }
    ?>
    </select>
        <label for="hinhanh">Hình ảnh:</label>
        <input type="file" id="hinhanh" name="hinhanh" accept="image/*">
        <button type="submit">Thêm sản phẩm</button>
    </form>
</div>
<div id="overlay" class="overlay" onclick="closeErrorPopup()"></div>
<div id="error-popup" class="error-popup">
    <h3>Lỗi</h3>
    <p id="error-message"></p>
    <button onclick="closeErrorPopup()">Đóng</button>
</div>
<a href="dssp.php">Quay lại danh sách sản phẩm</a>
</body>
</html>
<?php
$conn->close();
?>
