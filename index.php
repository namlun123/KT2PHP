<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng trực tuyến</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <h1>THƯƠNG MẠI ĐIỆN TỬ</h1>
            <nav>
                <a href="#">Trang chủ</a>
                <a href="giohang.php">Giỏ hàng</a>
                <a href="#">Liên hệ</a>
                <a href="#">Góp ý</a>
                <a href="#">Hỏi đáp</a>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <section class="product-section">
                <!-- Sản phẩm -->
                <div class="products">
                    <?php
                    // Kết nối đến cơ sở dữ liệu
                    include("connect.inp");

                    // Thiết lập số sản phẩm trên mỗi trang
                    $limit = 8;

                    // Lấy trang hiện tại từ URL (mặc định là 1)
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    // Truy vấn tổng số sản phẩm
                    $sql_total = "SELECT COUNT(*) AS total FROM Sanpham";
                    $result_total = $con->query($sql_total);
                    $row_total = $result_total->fetch_assoc();
                    $total_items = $row_total['total'];

                    // Tính toán tổng số trang
                    $total_pages = ceil($total_items / $limit);

                    // Truy vấn sản phẩm với giới hạn và offset
                    $sql = "SELECT * FROM Sanpham LIMIT $limit OFFSET $offset";
                    $result = $con->query($sql);

                    if ($result->num_rows > 0) {
                        // Hiển thị từng sản phẩm dưới dạng thẻ
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='product-card'>
                                    <img src='images/{$row['hinhanh']}' alt='{$row['tenhang']}'>
                                    <p>{$row['giahang']} VND</p>
                                    <p>{$row['tenhang']}</p>
                        <td class='actions'><a href='chitiet_mathang.php?Masp={$row['mahang']}' class='details-btn'>Chi tiết</a></td>
                                  </div>";
                        }
                    } else {
                        echo "Không có sản phẩm nào";
                    }

                    // Đóng kết nối
                    $con->close();
                    ?>
                </div>

                <!-- Liên kết phân trang căn giữa -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php
                        for ($i = 1; $i <= $total_pages; $i++) {
                            echo "<a href='?page=$i'" . ($i == $page ? " class='active'" : "") . ">$i</a>";
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Sidebar Đăng nhập -->
            <aside class="sidebar">
                <div class="login">
                    <h2>Tài khoản</h2>
                    <form method="POST" action="xllogin.php" class="login-form">
                    <input type="text" id="user" name="user" placeholder="Tên đăng nhập">
                        <input type="password" id="pass" name="pass" placeholder="Mật khẩu">
                        <button>Đăng nhập</button>
                    </form>
                    
                </div>

                <div class="category">
                    <h3>Danh mục</h3>
                    <ul>
                        <li>Áo</li>
                        <li>Quần</li>
                        <li>Váy</li>
                        <li>Tất</li>
                    </ul>
                </div>
            </aside>
        </main>
    </div>
</body>
</html>
