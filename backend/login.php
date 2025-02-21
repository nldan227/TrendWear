<?php
session_start();
include("config.php");
$error_message = '';

if($_SERVER['REQUEST_METHOD']=='POST'){
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token không hợp lệ");
    }
    // Hủy CSRF Token ngay sau khi kiểm tra thành công
    unset($_SESSION['csrf_token']);

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Truy vấn để lấy chuỗi băm của mật khẩu từ cơ sở dữ liệu
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':username' => $username
    ));

    // Kiểm tra xem username tồn tại không
    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashedPasswordFromDB = $row['password']; // Mật khẩu băm từ CSDL
        $role = $row['role'];
        // Kiểm tra mật khẩu bằng password_verify
        if(password_verify($password, $hashedPasswordFromDB)){
            // Mật khẩu đúng, tạo session
            $_SESSION['id'] = $row['userID'];
            $_SESSION['role'] = $role;
            if ($role === 'manager') {
                header("Location: manageOrders");
                exit();
            }else{
                header( "Location: /FIS_Intern/TrendWear/");
                exit();
            }
        } else {
            // Mật khẩu không đúng
            $error_message = 'Tên đăng nhập hoặc mật khẩu không đúng';
            header("Location: login?error=" . urlencode($error_message));
            exit();
        }
    } else {
        // Không tìm thấy người dùng
        $error_message = 'Tên đăng nhập hoặc mật khẩu không đúng';
        header("Location: login?error=" . urlencode($error_message));
        exit();
    }
}
?>