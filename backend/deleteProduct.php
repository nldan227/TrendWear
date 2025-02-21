<?php
session_start();
header('Content-Type: application/json');
include('config.php');

try {
    $headers = getallheaders();
    $csrfTokenFromHeader = $headers['X-CSRF-TOKEN'] ?? '';
    if ($csrfTokenFromHeader !== $_SESSION['csrf_token']) {
        http_response_code(403); 
        echo json_encode(["success" => false, "message" => "CSRF token không hợp lệ"]);
        exit();
    }
    unset($_SESSION['csrf_token']);
    // Đọc dữ liệu từ body khi dùng DELETE
    $data = json_decode(file_get_contents("php://input"), true);
    $productID = $data['productID'] ?? null;

    if (!$productID) {
        throw new Exception("Thiếu Product ID");
    }

    $sql = "DELETE FROM products WHERE productID = :productID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':productID' => $productID]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Không tìm thấy sản phẩm"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>