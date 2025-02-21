<?php
session_start();
include('../backend/config.php');

try {
    // Kiểm tra nếu người dùng chưa đăng nhập
    if (!isset($_SESSION['id'])) {
        throw new Exception("Vui lòng đăng nhập trước khi thêm vào giỏ hàng!");
    }
    $headers = getallheaders();
    $csrfTokenFromHeader = $headers['X-CSRF-TOKEN'] ?? '';
    if ($csrfTokenFromHeader !== $_SESSION['csrf_token']) {
        throw new Exception("CSRF token không hợp lệ"); 
    }
    unset($_SESSION['csrf_token']);
    // Lấy dữ liệu JSON từ AJAX
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data['productID']) || !isset($data['quantity'])) {
        throw new Exception("Dữ liệu không hợp lệ!");
    }

    $productID = $data['productID'];
    $userID = $_SESSION['id'];
    $quantity = $data['quantity'];
    $size_id = $data['selectedSize'];

    // Kiểm tra sản phẩm cùng size có tồn tại trong giỏ hàng không
    $sql = "SELECT * FROM cart_items WHERE productID = :productID AND userID = :userID AND size_id = :size_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':productID' => $productID,
        ':userID' => $userID,
        ':size_id' => $size_id
    ]);
    if ($stmt->rowCount() > 0) {
        // Nếu sản phẩm đã có trong giỏ hàng, cập nhật số lượng
        $sql = "UPDATE cart_items SET quantity = quantity + :quantity WHERE productID = :productID AND userID = :userID  AND size_id = :size_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':quantity' => $quantity,
            ':productID' => $productID,
            ':userID' => $userID,
            ':size_id' => $size_id
        ]);
    } else {
        // Nếu sản phẩm chưa có trong giỏ hàng, thêm mới
        $sql = "INSERT INTO cart_items (userID, productID, quantity, size_id) VALUES (:userID, :productID, :quantity, :size_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':userID' => $userID,
            ':productID' => $productID,
            ':quantity' => $quantity,
            ':size_id' => $size_id 
        ]);
    }

    // Trả về kết quả thành công
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit();
}
?>