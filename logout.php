<?php
session_start();

// Xóa tất cả dữ liệu trong session
session_unset();

// Hủy session
session_destroy();

// Điều hướng về trang chủ hoặc trang đăng nhập
header("Location: index.php");
exit;
?>
