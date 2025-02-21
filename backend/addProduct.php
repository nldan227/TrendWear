<?php
session_start();
include("config.php");

$data = json_decode(file_get_contents("php://input"), true);
$newProductID = $data['newIDProduct'];
$productName = $data['newProductName'];
$productPrice = $data['newProductPrice'];
$categoryID = $data['newProductCategory'];
$sizes = $data['sizes'];

try {
    $headers = getallheaders();
    $csrfTokenFromHeader = $headers['X-CSRF-TOKEN'] ?? '';
    if ($csrfTokenFromHeader !== $_SESSION['csrf_token']) {
        http_response_code(403); 
        echo json_encode(["success" => false, "message" => "CSRF token không hợp lệ"]);
        exit();
    }
    unset($_SESSION['csrf_token']);
    // Thêm sản phẩm vào bảng products
    $sql = "INSERT INTO products (productID, name, price, categoryID) VALUES (:productID, :name, :price, :categoryID)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':productID' => $newProductID, ':name' => $productName, ':price' => $productPrice, ':categoryID' => $categoryID]);
    // Thêm size và số lượng vào bảng product_sizes
    $sql = "INSERT INTO product_sizes (product_id, size_id, stock) 
            VALUES (:product_id, :size_id, :stock)";
    $stmt = $pdo->prepare($sql);
    foreach ($sizes as $size) {
        $stmt->execute([
            ':product_id' => $newProductID,
            ':size_id' => $size['sizeID'],
            ':stock' => $size['stock']
        ]);
    }
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Lỗi: " . $e->getMessage()]);
}
?>