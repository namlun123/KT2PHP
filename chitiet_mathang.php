<?php
session_start();
include("connect.inp");
$masp = $_GET["Masp"];
$sql = "SELECT * FROM sanpham WHERE mahang='$masp'";
$result = $con->query($sql);
$row = $result->fetch_assoc();
$tensp = $row['tenhang'];
$soluong_tonkho = $row['soluong'];
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm</title>
    <link rel="stylesheet" href="css/chitiet_mathang.css">
    <script>
    function checkQuantity() {
        const quantityInput = document.getElementById('quantity');
        const maxQuantity = <?php echo $soluong_tonkho; ?>;
        const warningMessage = document.getElementById('quantity-warning');
        
        if (quantityInput.value > maxQuantity) {
            warningMessage.style.display = 'block';
            warningMessage.innerText = `Hiện trong kho chỉ còn ${maxQuantity}! Bạn vui lòng chọn ít hơn.`;
            quantityInput.value = maxQuantity; // Giới hạn lại số lượng
        } else {
            warningMessage.style.display = 'none';
        }
    }

    function showPopup(productName, quantity) {
        document.getElementById('popup-message').innerText =
            `Bạn đã thêm thành công ${quantity} sản phẩm "${productName}" vào giỏ hàng.`;
        document.getElementById('popup-overlay').style.display = 'flex'; 
    }

    function closePopup() {
        document.getElementById('popup-overlay').style.display = 'none';
        document.getElementById('quantity').value = ""; 
    }

    function goToCart() {
        window.location.href = 'giohang.php';
    }

    function submitForm() {
        const quantityInput = document.getElementById('quantity');
        const maxQuantity = <?php echo $soluong_tonkho; ?>;
        
        // Kiểm tra lại số lượng nhập vào trước khi gửi yêu cầu
        if (quantityInput.value > maxQuantity) {
            document.getElementById('quantity-warning').innerText = 
                `Hiện trong kho chỉ còn ${maxQuantity}! Bạn vui lòng chọn ít hơn.`;
            document.getElementById('quantity-warning').style.display = 'block';
            return; // Dừng lại nếu số lượng không hợp lệ
        }

        // Gửi yêu cầu nếu số lượng hợp lệ
        const formData = new FormData(document.querySelector('form'));
        fetch('xlthemgiohang.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                showPopup("<?php echo $tensp; ?>", quantityInput.value);
            }
        });
    }
</script>
</head>
<body>
    <div class="container">
        <h3>Thông Tin Sản Phẩm</h3>
        <form method='POST' action='xlthemgiohang.php' onsubmit='event.preventDefault(); submitForm();'>
            <div class='product-info'>
                <label>Mã Sản Phẩm:</label> <span><?php echo $row['mahang']; ?></span>
                <input type='hidden' name='Masp' value='<?php echo $row['mahang']; ?>'>
                <label>Tên Sản Phẩm:</label> <span><?php echo $row['tenhang']; ?></span>
                <label>Giá:</label> <span><?php echo $row['giahang']; ?> VNĐ</span>
                <input type='hidden' name='Gia' value='<?php echo $row['giahang']; ?>'>
            </div>
            <div class='quantity'>
                <label>Số Lượng:</label>
                <input type="number" name="quantity" min="1" max="<?php echo $soluong_tonkho; ?>" required placeholder="Nhập số lượng" id="quantity" oninput="checkQuantity()">
            </div>
            <p id="quantity-warning" style="color: red; display: none;"></p>
            <?php if (isset($_SESSION["user"]) ? $_SESSION["user"] : 'admin') : ?>
           <input type='submit' value='Thêm vào giỏ hàng'>
            <?php endif; ?>
        </form>
        <a href='dssp.php' class='back-button'>Quay lại</a>
        <div class="popup-overlay" id="popup-overlay">
            <div class="popup-content">
                <p id="popup-message"></p>
                <div class="popup-buttons">
                    <button class="continue-shopping" onclick="closePopup()">Ở lại trang</button>
                    <button class="go-to-cart" onclick="goToCart()">Tiến đến giỏ hàng</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
