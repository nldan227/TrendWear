<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json"); // Đảm bảo phản hồi là JSON
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

if (!isset($_SESSION['id']) || empty($data['orderItems'])) {
    echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ!"]);
    exit;
}
$userID = $_SESSION['id'];
$nameReceiver = $data['nameReceiver'];
$phoneReceiver = $data['phoneReceiver'];
$addressReceiver = $data['addressReceiver'];
$note = isset($data['note']) ? $data['note'] : "";
$totalPrice = $data['total_price'];

try {
    // Tạo đơn hàng trong bảng orders
    $sql = "INSERT INTO orders (userID, receiver_name, phone, address, note, totalPrice, date ) 
            VALUES (:userID, :receiver_name, :phone, :address, :note, :totalPrice, :date)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":userID" => $userID,
        ":receiver_name" => $nameReceiver,
        ":phone" => $phoneReceiver,
        ":address" => $addressReceiver,
        ":note" => $note,
        ":totalPrice" => $totalPrice, 
        ":date" => date("Y-m-d H:i:s")
    ]);
    
    $orderID = $pdo->lastInsertId(); // Lấy ID đơn hàng vừa tạo

    // Lưu từng sản phẩm vào bảng detail_order
    foreach ($data['orderItems'] as $item) {
        $productID = $item['productID'];
        $sizeID = $item['sizeID'];
        $quantity = $item['quantity'];

        // Thêm chi tiết đơn hàng
        $sql = "INSERT INTO detail_order (orderID, productID, size_id, quantity) 
                VALUES (:orderID, :productID, :size_id, :quantity)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":orderID" => $orderID,
            ":productID" => $productID,
            ":size_id" => $sizeID,
            ":quantity" => $quantity
        ]);

        // UPDATE lại số lượng sản phẩm 
        $sql = "UPDATE product_sizes SET stock = stock - :quantity WHERE product_id = :productID AND size_id = :sizeID ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":productID" => $productID,
            ":sizeID" => $sizeID,
            ":quantity" => $quantity
        ]);

    }
        
    // Xóa sản phẩm khỏi giỏ hàng sau khi đặt hàng thành công
    $sql = "DELETE FROM cart_items WHERE userID = :userID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":userID" => $userID]);
    echo json_encode(["success" => true, "message" => "Đặt hàng thành công!"]);
} catch (Exception $e) {
    $error_details = [
        "error_message" => $e->getMessage(),
        "order_data" => $data, // Thông tin đầu vào từ người dùng
        "last_sql" => $sql,    // Câu lệnh SQL cuối cùng
        "user_id" => $userID
    ];
    echo json_encode(["success" => false, "message" => "Lỗi khi đặt hàng!", "details" => $error_details]);
}


?>