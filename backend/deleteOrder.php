<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
$orderID = $data['orderID'];
$sql = "DELETE FROM orders WHERE orderID = :orderID";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([':orderID' => $orderID])) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
?>