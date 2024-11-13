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
                <a href="index.php" class="nav-link">Trang chủ</a>
                <a href="giohang.php" class="nav-link">Giỏ hàng</a>

                <?php
                session_start();
                
                if (isset($_SESSION["error_message"])) {
                    echo "<script>alert('" . $_SESSION["error_message"] . "');</script>";
                    unset($_SESSION["error_message"]); // Xóa thông báo lỗi sau khi hiển thị
                }
                
                // Kiểm tra xem người dùng đã đăng nhập chưa
                if (isset($_SESSION["user"])) {
                    // Hiển thị thêm mục "Quản lý người dùng" nếu user có quyền quản trị (permiss == 1)
                    if ($_SESSION["permiss"] == 1) {
                        echo "<a href='#' class='nav-link'>Quản lý người dùng</a>";
                        echo "<a href='dssp.php' class='nav-link'>Quản lý sản phẩm</a>";

                    }

                    // Các liên kết cho người dùng đã đăng nhập
                    echo "<a href='dondathang.php?showAll=true' class='nav-link'>Danh sách đơn hàng</a>";
                    echo "<span class='welcome-text'>Xin chào, {$_SESSION['user']}</span>";
                    echo "<a href='logout.php' class='nav-link logout'>Đăng xuất</a>";

                } 
                ?>
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
                                    <img src='image/{$row['hinhanh']}' alt='{$row['tenhang']}'>
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
                    <!-- Kiểm tra nếu người dùng chưa đăng nhập thì hiển thị form đăng nhập -->
                    <?php if (!isset($_SESSION["user"])): ?>
                        <div class="login">
                            <h2>Tài khoản</h2>
                            <form method="POST" action="xllogin.php" class="login-form">
                                <input type="text" id="user" name="user" placeholder="Tên đăng nhập">
                                <input type="password" id="pass" name="pass" placeholder="Mật khẩu">
                                <button>Đăng nhập</button>
                            </form>
                        </div>
                    <?php endif; ?>

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
