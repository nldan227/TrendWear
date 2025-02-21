<?php
session_start();
include("config.php");
header('Content-Type: application/json'); // Đảm bảo trả về JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);
$data = json_decode(file_get_contents("php://input"), true);

$headers = getallheaders();
$csrfTokenFromHeader = $headers['X-CSRF-TOKEN'] ?? '';
if ($csrfTokenFromHeader !== $_SESSION['csrf_token']) {
    http_response_code(403); 
    echo json_encode(["success" => false, "message" => "CSRF token không hợp lệ"]);
    exit();
}
$userID = $_SESSION['id'];
$productID = $data['productID'];
$sizeID = $data['sizeID'];
$quantity = $data['quantity'];

$sql = "UPDATE cart_items SET quantity = :quantity WHERE userID = :userID AND productID = :productID AND size_id = :sizeID";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([':quantity' => $quantity, ':userID' => $userID, ':productID' => $productID, ':sizeID' => $sizeID]);

echo json_encode(["success" => $success]);
?>