<?php
session_start();
include('config.php');
$productID = $_GET['productID'];

$sql = "SELECT products.productID, products.name AS productName, products.price, 
               sizes.size_id, sizes.size_name, product_sizes.stock
        FROM products 
        INNER JOIN product_sizes ON products.productID = product_sizes.product_id
        INNER JOIN sizes ON product_sizes.size_id = sizes.size_id
        WHERE products.productID = :productID";

$stmt = $pdo->prepare($sql);
$stmt->execute([':productID' => $productID]);

$result = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!isset($result['productID'])) {
        $result['productID'] = $row['productID'];
        $result['productName'] = $row['productName'];
        $result['price'] = $row['price'];
        $result['sizes'] = []; // Tạo một mảng rỗng sizes để chứa thông tin về các size của sản phẩm
    }
    $result['sizes'][] = [
        'size_id' => $row['size_id'],
        'size_name' => $row['size_name'],
        'stock' => $row['stock']
    ];
}

echo json_encode($result);
?>