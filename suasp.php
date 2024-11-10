<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Sản Phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/suasp.css">
    <script>
        function confirmCancel() {
            return confirm("Bạn có muốn hủy quá trình sửa sản phẩm?");
        }

        function validateForm() {
            let tenhang = document.forms["suaspForm"]["tenhang"].value;
            let giahang = document.forms["suaspForm"]["giahang"].value;
            let soluong = document.forms["suaspForm"]["soluong"].value;

            if (tenhang === "" || giahang === "" || soluong === "") {
                alert("Vui lòng điền đầy đủ thông tin.");
                return false;
            }

            if (isNaN(giahang) || giahang <= 0) {
                alert("Giá sản phẩm phải là một số dương.");
                return false;
            }

            // Kiểm tra số lượng (phải là số nguyên dương hoặc 0)
            if (isNaN(soluong) || soluong < 0 || soluong % 1 != 0) {
                alert("Số lượng phải là số nguyên dương hoặc 0.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <?php
    session_start();
    include("connect.inp");

    // Kiểm tra mã sản phẩm
    if (isset($_GET['Masp'])) {
        $mahang = $_GET['Masp'];

        $sql = "SELECT * FROM sanpham WHERE mahang='$mahang'";
        $result = $con->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "Sản phẩm không tồn tại!";
            exit;
        }
    }

    // Lấy dữ liệu mã loại từ cơ sở dữ liệu
    $sql_maloai = "SELECT * FROM loaisp";  // Bảng loaisp chứa mã loại
    $result_maloai = $con->query($sql_maloai);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tenhang = $_POST['tenhang'];
        $giahang = $_POST['giahang'];
        $soluong = $_POST['soluong'];
        $hinhanh = $_FILES['hinhanh']['name'];
        $maloai = $_POST['maloai'];
        $nguoisua = $_SESSION['user'];
        $ngaysua = date("Y-m-d H:i:s");

        // Kiểm tra trùng tên sản phẩm
        $checkNameSql = "SELECT * FROM sanpham WHERE tenhang='$tenhang' AND mahang != '$mahang'";
        $checkNameResult = $con->query($checkNameSql);
        
        if ($checkNameResult->num_rows > 0) {
            echo "<script>alert('Tên sản phẩm đã tồn tại.');</script>";
        } else {
            // Xử lý hình ảnh
            $hinhanh = $row['hinhanh'];
            if (!empty($_FILES['hinhanh']['name'])) {
                $hinhanh = $_FILES['hinhanh']['name'];
                move_uploaded_file($_FILES['hinhanh']['tmp_name'], "image/" . $hinhanh);
            }

            $updateSql = "UPDATE sanpham SET tenhang='$tenhang', giahang='$giahang', hinhanh='$hinhanh', soluong='$soluong', maloai='$maloai', nguoisua='$nguoisua', ngaysua='$ngaysua' WHERE mahang='$mahang'";

            if ($con->query($updateSql) === TRUE) {
                echo "<script>alert('Sửa sản phẩm thành công!'); window.location.href='dssp.php';</script>";
            } else {
                echo "Lỗi: " . $con->error;
            }
        }
    }
    ?>

    <div class="container">
        <form name="suaspForm" action="" method="POST" onsubmit="return validateForm()" enctype="multipart/form-data">
            <h2>Sửa Thông Tin Sản Phẩm</h2>
            <label for="mahang">Mã Sản Phẩm:</label>
            <input type="text" name="mahang" value="<?php echo $row['mahang']; ?>" readonly><br>

            <label for="tenhang">Tên Sản Phẩm:</label>
            <input type="text" name="tenhang" value="<?php echo $row['tenhang']; ?>"><br>

            <label for="giahang">Giá Sản Phẩm:</label>
            <input type="text" name="giahang" value="<?php echo $row['giahang']; ?>"><br>

            <label for="soluong">Số Lượng:</label>
            <input type="text" name="soluong" value="<?php echo $row['soluong']; ?>"><br>

            <label for="maloai">Mã Loại:</label>
            <select name="maloai">
                <?php
                // Hiển thị các mã loại có sẵn từ cơ sở dữ liệu
                while ($loai = $result_maloai->fetch_assoc()) {
                    // Kiểm tra nếu mã loại của sản phẩm hiện tại trùng với mã loại trong cơ sở dữ liệu
                    $selected = ($row['maloai'] == $loai['maloai']) ? 'selected' : '';
                    echo "<option value='" . $loai['maloai'] . "' $selected>" . $loai['tenloai'] . "</option>"; // 'tenloai' là tên loại
                }
                ?>
            </select><br>

            <label for="hinhanh">Hình Ảnh:</label>
            <input type="file" name="hinhanh"><br>
            <img src="image/<?php echo $row['hinhanh']; ?>" alt="Hình ảnh sản phẩm" style="width: 100px; height: 100px;"><br>

            <button type="submit">Lưu</button>
            <button type="button" onclick="if(confirmCancel()) window.location.href='dssp.php'">Hủy</button>
        </form>
    </div>

    <?php $con->close(); ?>
</body>
</html>