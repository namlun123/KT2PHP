<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- JavaScript kiểm tra tham số error trong URL và hiển thị alert -->
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('error') === 'invalid_credentials') {
            alert('Thông tin đăng nhập không đúng hoặc tài khoản không hoạt động.');
        }
    </script>
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
