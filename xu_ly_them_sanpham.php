<?php
session_start(); // Bắt đầu phiên làm việc
$conn = new mysqli('localhost', 'root', '', 'kt2php');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mahang = $_POST['mahang'];
    $tenhang = $_POST['tenhang'];
    $soluong = $_POST['soluong'];
    $giahang = $_POST['giahang'];
    $maloai = $_POST['maloai'];

    $nguoithem = $_SESSION['tentaikhoan'];
    if (empty($mahang) || empty($tenhang) || empty($soluong) || empty($giahang) || empty($maloai)) {
        header("Location: them_sanpham.php?message=Lỗi: Tất cả các trường đều bắt buộc.");
        exit();
    }
    if (!is_numeric($soluong) || !is_numeric($giahang)) {
        header("Location: them_sanpham.php?message=Lỗi: Số lượng và giá hàng phải là số.");
        exit();
    }
    $hinhanh = '';
    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "image/"; 
        $file_name = basename($_FILES["hinhanh"]["name"]); 
        $target_file = $target_dir . $file_name; 

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); 
        }

        if (!move_uploaded_file($_FILES["hinhanh"]["tmp_name"], $target_file)) {
            header("Location: them_sanpham.php?message=Lỗi: Không thể tải lên hình ảnh.");
            exit();
        }
        $hinhanh = $file_name; 
    } else {
        header("Location: them_sanpham.php?message=Lỗi: Vui lòng chọn hình ảnh.");
        exit();
    }
    $sql_ktra = "SELECT * FROM sanpham WHERE Mahang = '$mahang' OR Tenhang = '$tenhang'";
    $result = $conn->query($sql_ktra);

    if ($result->num_rows > 0) {
        header("Location: them_sanpham.php?message=Lỗi: Sản phẩm đã tồn tại.");
    } else {
        $sql = "INSERT INTO sanpham (Mahang, Tenhang, Soluong, Giahang, Maloai, Hinhanh, Nguoithem, Ngaythem) 
                VALUES ('$mahang', '$tenhang', $soluong, $giahang, '$maloai', '$hinhanh', '$nguoithem', NOW())";

        if ($conn->query($sql) === TRUE) {
            header("Location: dssp.php?message=Thêm sản phẩm thành công");
        } else {
            header("Location: them_sanpham.php?message=Lỗi: " . $conn->error);
        }
    }
}
$conn->close();
?>
