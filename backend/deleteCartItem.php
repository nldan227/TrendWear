<?php
session_start();
include("config.php");

$data = json_decode(file_get_contents("php://input"), true);

$headers = getallheaders();
$csrfTokenFromHeader = $headers['X-CSRF-TOKEN'] ?? '';
if ($csrfTokenFromHeader !== $_SESSION['csrf_token']) {
    http_response_code(403); 
    echo json_encode(["success" => false, "message" => "CSRF token không hợp lệ"]);
    exit();
}
unset($_SESSION['csrf_token']);
$userID = $_SESSION['id'];
$productID = $data['productID'];
$sizeID = $data['sizeID'];

$sql = "DELETE FROM cart_items WHERE userID = :userID AND productID = :productID AND size_id = :sizeID";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([':userID' => $userID, ':productID' => $productID, ':sizeID' => $sizeID]);

echo json_encode(["success" => $success]);
?>