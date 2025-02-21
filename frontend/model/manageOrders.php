<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Orders</title>
    <style>
        .title {
            margin-left: 2%;
            margin-top: 1%;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
            text-align: center;
        }

        th {
            background-color: #254880;
            text-align: center;
            border-color:rgb(0, 0, 0);
            color: white;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-view {
            background-color:rgb(142, 144, 145);
            color: white;
            text-decoration: none;
            padding: 6px 10px;
            margin-right: 5px;
        }

        .btn-view:hover {
            background-color:rgb(118, 125, 129);
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 10px;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

    </style>
</head>
<body>
    <?php 
        session_start();
        include("../layout/headerManager.html");
        include("../../backend/config.php"); 
        // Kiểm tra nếu chưa đăng nhập
        if (!isset($_SESSION['id'])) {
            header("Location: login"); 
            exit();
        }

        if ($_SESSION['role'] !== 'manager') {echo "<script>
            alert('Bạn không có quyền truy cập trang này!');
            history.back();  
          </script>";
            exit();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        // Lấy danh sách đơn hàng từ database
        $sql = "SELECT orderID, date, totalPrice, address, receiver_name, phone, status FROM orders ORDER BY date DESC";
        $stmt = $pdo->query($sql);
    ?>
    <div class="title">Trạng thái đơn hàng</div>
    <hr class="underline">
    <div class=""></div>
    <table id="product-list" style="margin-left: 5%; margin-top: 2%; width: 90%">
        <thead>
            <tr>
                <th style="width: 5%">Mã đơn hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Địa chỉ</th>
                <th>Người nhận</th>
                <th>Số điện thoại</th>
                <th>Trạng thái</th>
                <th>Hành động</th>

            </tr>
        </thead>
        <tbody>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['orderID'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= number_format(htmlspecialchars($row['totalPrice'], ENT_QUOTES, 'UTF-8'), 0, ',', '.'); ?> VND</td>
                    <td><?= htmlspecialchars($row['address'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['receiver_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <a href="getOrderDetails.php?orderID=<?= urlencode($row['orderID']); ?>" class="btn btn-view">Xem</a>
                        <button class="btn btn-delete" onclick="deleteOrder('<?= htmlspecialchars($row['orderID'], ENT_QUOTES, 'UTF-8'); ?>')">Xóa</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        const csrfToken = '<?= $_SESSION['csrf_token']; ?>';

        function deleteOrder(orderID) {
            if (confirm("Bạn có chắc muốn xóa đơn hàng này không?")) {
                fetch('ctlDltOrder.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        "X-CSRF-TOKEN": csrfToken
                     },
                    body: JSON.stringify({ orderID })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("✅ Đã xóa đơn hàng thành công!");
                        location.reload(); // Cập nhật trang
                    } else {
                        alert("❌ Xóa đơn hàng thất bại!");
                    }
                })
                .catch(error => console.error("Lỗi khi xóa đơn hàng:", error));
            }
        }
    </script>

    <?php include("../layout/footerManager.html") ?>
</body>
</html>