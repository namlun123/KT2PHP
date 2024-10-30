<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm </title>
    <style>
      body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9; /* Màu nền nhẹ */
    color: #333; /* Màu chữ tối */
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.container {
    width: 80%;
    margin-top: 2rem;
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
    background-color: #ffffff; /* Màu nền trắng cho bảng */
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

table th, table td {
    padding: 12px 15px;
    border: 1px solid #ccc; /* Màu xám cho border */
    text-align: center;
    font-size: 1rem;
}

table th {
    background-color: #003366; /* Màu xanh dương đậm */
    color: #ffffff; /* Màu chữ trắng */
}

table tr:nth-child(even) {
    background-color: #f2f2f2; /* Màu xám nhạt cho hàng chẵn */
}

.actions a {
    text-decoration: none;
    color: #ffffff; /* Màu chữ trắng */
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.9rem;
    margin: 0 4px;
}

.details-btn { 
    background-color: #0056b3; /* Xanh dương đậm */
}

.edit-btn { 
    background-color: #888888; /* Màu xám */
}

.delete-btn { 
    background-color: #b22222; /* Màu đỏ tối */
}

.delete-btn:hover { 
    background-color: #a31c1c; /* Màu đỏ tối khi hover */
}

.edit-btn:hover { 
    background-color: #666666; /* Màu xám tối khi hover */
}

.details-btn:hover { 
    background-color: #004494; /* Xanh dương nhạt khi hover */
}

.add-product {
    display: inline-block;
    margin: 1rem 0;
    padding: 8px 16px;
    background-color: #0056b3; /* Xanh dương đậm */
    color: #ffffff; /* Màu chữ trắng */
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s;
}

.add-product:hover {
    background-color: #004494; /* Xanh dương nhạt khi hover */
}

.pagination {
    margin: 1.5rem 0;
    font-size: 1rem;
}

.pagination a {
    margin: 0 5px;
    color: #003366; /* Xanh dương đậm */
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 4px;
    border: 1px solid #003366; /* Xanh dương đậm */
    transition: background-color 0.3s, color 0.3s;
}

.pagination a:hover {
    background-color: #003366; /* Xanh dương đậm khi hover */
    color: #ffffff; /* Màu chữ trắng khi hover */
}

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
                echo "<table>
                    <tr>
                        <th>STT</th>
                        <th>Mã sản phẩm</th>
                        <th>Tên sản phẩm</th>
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
                        <td>{$row['giahang']}</td>
                        <td class='actions'><a href='chitiet_mathang.php?Masp={$row['mahang']}' class='details-btn'>Chi tiết</a></td>
                        <td class='actions'><a href='suaLoai.php?Masp={$row['mahang']}' class='edit-btn'>Sửa</a></td>
                        <td class='actions'><a href='xoaLoai.php?Masp={$row['mahang']}' class='delete-btn' onclick='return ktraxoa();'>Xóa</a></td>
                    </tr>";
                    $i++;
                }
                echo "</table>";
                echo "<a href='themsp.php' class='add-product'>Thêm sản phẩm</a>";
            } else {
                echo "Không có dữ liệu trong bảng";
            }
            $con->close();  
        ?>
        
        <div class="pagination">
            <?php
                for ($p = 1; $p <= ceil($sum_record / $each_record); $p++) {
                    echo "<a href='list_mathang.php?page={$p}'>$p</a> ";
                }
            ?>
        </div>
    </div>

    <script>
        let message = document.getElementById("tb").innerText;
        if (message) alert(message);
    </script>
</body>
</html>
