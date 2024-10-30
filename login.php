<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-commerce Fashion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <form method="POST" action="xllogin.php" class="login-form">
            <h2>Đăng nhập</h2>
            <label for="user">Tên tài khoản:</label>
            <input type="text" id="user" name="user" required><br>
            <label for="pass">Mật khẩu:</label>
            <input type="password" id="pass" name="pass" required><br>
            <input type="submit" value="Đăng nhập">
        </form>
    </div>
</body>
</html>
