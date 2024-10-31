<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Dashboard</title>
    <!-- Thêm Font Awesome để dùng biểu tượng -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="navbar">
        <a href="trangchu.php" class="nav-link">Trang chủ</a>
        <?php
            session_start();
            if (isset($_SESSION["user"]) && $_SESSION["permiss"] == 1) {
                echo "<a href='#' class='nav-link'>Quản lý người dùng</a>";
            }
            if (!isset($_SESSION["user"])) {
                echo "<a href='login.php' class='nav-link'>Login</a>";
            }
            if (isset($_SESSION["user"])) {
                echo "<a href='dssp.php' class='nav-link'>Danh sách mặt hàng</a>";
                echo "<span class='welcome-text'>Xin chào, {$_SESSION['user']}</span>";
                echo "<a href='cart.php' class='nav-link cart'><i class='fas fa-shopping-cart'></i></a>";
                echo "<a href='logout.php' class='nav-link logout'>Đăng xuất</a>";
            }
        ?>
    </div>
</body>
</html>
