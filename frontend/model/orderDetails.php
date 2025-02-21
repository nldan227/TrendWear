<?php
 session_start();
 include("../layout/headerManager.html");
 include('../../backend/config.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng</title>
    <style>
        .container { 
            width: 90%; 
            margin: auto; 
            padding: 20px; 
        }
        .container p, h3, h2 {
            margin-top: 2%;
        }
        table { 
            width: 100%; 
            margin-top: 2%;
            border-collapse: collapse; 
        }
        table, th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
            text-align: center;
        }
      
        th { 
            background-color: #254880; 
            color: white; 
        }
    </style>
</head>
<body>
    <?php
    // Kiểm tra nếu chưa đăng nhập
    if (!isset($_SESSION['id'])) {
        header("Location: login"); 
        exit();
    }

    if ($_SESSION['role'] !== 'manager') {echo "<script>
        alert('Bạn không có quyền truy cập trang này!');
        history.back();  // Chuyển hướng sau khi cảnh báo
      </script>";
        exit();
    }

    if (!isset($_GET['orderID'])) {
        die("Không tìm thấy đơn hàng!");
    }

    $orderID = $_GET['orderID'];

    // Lấy thông tin đơn hàng
    $sql = "SELECT * FROM orders WHERE orderID = :orderID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':orderID' => $orderID]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Đơn hàng không tồn tại!");
    }
    ?>
    <div class="container">
        <h2>Chi Tiết Đơn Hàng #<?= htmlspecialchars($order['orderID'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><strong>Ngày đặt:</strong> <?= htmlspecialchars($order['date'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['receiver_name'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Tổng tiền:</strong> <?= number_format($order['totalPrice'], 0, ',', '.') ?> VND</p>
        <p><strong>Trạng thái:</strong> <?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></p>
        <h3>Sản phẩm trong đơn hàng:</h3>
        <table>
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Kích cỡ</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT p.name, s.size_name, do.quantity, p.price 
                        FROM detail_order as do
                        JOIN products p ON do.productID = p.productID
                        JOIN sizes s ON do.size_id = s.size_id
                        WHERE do.orderID = :orderID";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':orderID' => $orderID]);

                while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $productName = htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');
                    $sizeName = htmlspecialchars($item['size_name'], ENT_QUOTES, 'UTF-8');
                    $quantity = htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8');
                    $price = number_format($item['price'], 0, ',', '.');
                    $totalPrice = number_format($item['price'] * $item['quantity'], 0, ',', '.');
    
                    echo "<tr>
                            <td>{$productName}</td>
                            <td>{$sizeName}</td>
                            <td>{$quantity}</td>
                            <td>{$price} VND</td>
                            <td>{$totalPrice} VND</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <a style="text-decoration: none; color: black; font-weight: bold;" href="manageOrders">⬅️ Quay lại</a>
    </div>
</body>
</html>