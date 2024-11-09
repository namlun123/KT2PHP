<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
</style>
</head>
<script>
    function ktraxoa() {
        return confirm("Bạn có thực sự muốn xóa không?");
    }
</script>
<body>
    <div class="container">
        <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 1) {
                    echo "<span id='tb' style='display:none'>Thêm thành công</span>";
                } else {
                    echo "<span id='tb' style='display:none'>Lỗi thêm</span>";
                }
            }

            include("connect.inp");

            $sql = "select count(mahang) as ts From sanpham";
            $result = $con->query($sql);
            $row = $result->fetch_assoc();
            $sum_record = $row["ts"];
            $each_record = 4;
            $page = isset($_GET["page"]) ? $_GET["page"] : 1;
            $offset = ($page - 1) * $each_record;

            $sql = "SELECT * FROM Sanpham LIMIT $each_record OFFSET $offset";
            $result = $con->query($sql);

            if ($result->num_rows > 0) {
                echo "<div class='table-container'>
                    <table>
                        <tr>
                            <th>STT</th>
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Hình ảnh</th>
                            <th>Giá</th>
                            <th>Chi tiết</th>
                            <th>Sửa</th>
                            <th>Xóa</th>
                        </tr>";
                
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                    <td>$i</td>
                    <td>{$row['mahang']}</td>
                    <td>{$row['tenhang']}</td>
                    <td><img src='image/{$row["hinhanh"]}' alt='{$row["tenhang"]}' style='width: 50px; height: 50px;'></td>
                    <td>{$row['giahang']}</td>
                    <td class='actions'><a href='chitiet_mathang.php?Masp={$row['mahang']}' class='details-btn'>Chi tiết</a></td>
                    <td class='actions'><a href='suasp.php?Masp={$row['mahang']}' class='edit-btn'><i class='fas fa-edit'></i></a></td>
                    <td class='actions'><a href='xoaLoai.php?Masp={$row['mahang']}' class='delete-btn' onclick='return ktraxoa();'><i class='fas fa-trash-alt'></i></a></td>
                    </tr>";
                    $i++;

                }
                echo "</table>";
                echo "<a href='themsp.php' class='add-product'><i class='fas fa-plus'></i> Thêm sản phẩm</a>";
                echo "</div>";
            } else {
                echo "Không có dữ liệu trong bảng";
            }
            $con->close();  
        ?>
        
        <div class="pagination">
            <?php
                for ($p = 1; $p <= ceil($sum_record / $each_record); $p++) {
                    echo "<a href='dssp.php?page={$p}'>$p</a> ";
                }
           
