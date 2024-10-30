<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
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
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            text-align: center;
            font-size: 1rem;
        }

        table th {
            background-color: #4CAF50;
            color: #ffffff;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .actions a {
            text-decoration: none;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin: 0 4px;
        }

        .details-btn { background-color: #2196F3; }
        .edit-btn { background-color: #ffc107; }
        .delete-btn { background-color: #f44336; }

        .delete-btn:hover { background-color: #d32f2f; }
        .edit-btn:hover { background-color: #ffa000; }
        .details-btn:hover { background-color: #1976d2; }

        .add-product {
            display: inline-block;
            margin: 1rem 0;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: #ffffff;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .add-product:hover {
            background-color: #388E3C;
        }

        .pagination {
            margin: 1.5rem 0;
            font-size: 1rem;
        }

        .pagination a {
            margin: 0 5px;
            color: #4CAF50;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #4CAF50;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination a:hover {
            background-color: #4CAF50;
            color: #ffffff;
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
