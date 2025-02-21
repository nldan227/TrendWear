<?php
session_start();
include('config.php');

$data = json_decode(file_get_contents("php://input"), true);
$headers = getallheaders();
$csrfTokenFromHeader = $headers['X-CSRF-TOKEN'] ?? '';
if ($csrfTokenFromHeader !== $_SESSION['csrf_token']) {
    http_response_code(403); 
    echo json_encode(["success" => false, "message" => "CSRF token không hợp lệ"]);
    exit();
}
unset($_SESSION['csrf_token']);
$sql = "UPDATE products SET name = :productName, price = :productPrice WHERE productID = :productID";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':productID' => $data['productID'],
    ':productName' => $data['productName'],
    ':productPrice' => $data['productPrice']
]);

foreach ($data['sizes'] as $size) {
    $sql = "UPDATE product_sizes SET stock = :stock WHERE product_id = :productID AND size_id = :sizeID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':stock' => $size['stock'],
        ':productID' => $data['productID'],
        ':sizeID' => $size['sizeID']
    ]);
}

echo json_encode(["success" => true]);
?>