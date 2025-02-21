<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token không hợp lệ");
    }
    $errors = [];

    // Lấy dữ liệu từ form
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    $full_name = htmlspecialchars($_POST['full_name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $phone_number = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $gender = htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8');

    // Kiểm tra dữ liệu nhập vào
    if (empty($username)) {
        $errors['username'] = "Vui lòng nhập tên người dùng.";
    }
    if (empty($email)) {
        $errors['email'] = "Vui lòng nhập email.";
    }
    if (empty($full_name)) {
        $errors['full_name'] = "Vui lòng nhập họ tên.";
    }
    if (empty($password)) {
        $errors['password'] = "Vui lòng nhập mật khẩu.";
    }
    if (empty($phone_number)) {
        $errors['phone'] = "Vui lòng nhập số điện thoại.";
    }

    // Kiểm tra username, email, số điện thoại đã tồn tại chưa
    $sql_check = "SELECT * FROM users WHERE username = :username OR email = :email OR phone_number = :phone_number";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([
        ':username' => $username,
        ':email' => $email,
        ':phone_number' => $phone_number
    ]);

    if ($stmt_check->rowCount() > 0) {
        $existing_user = $stmt_check->fetch(PDO::FETCH_ASSOC);
        if ($existing_user['username'] == $username) {
            $errors['username'] = "Tên người dùng đã tồn tại!";
        }
        if ($existing_user['email'] == $email) {
            $errors['email'] = "Email đã tồn tại!";
        }
        if ($existing_user['phone_number'] == $phone_number) {
            $errors['phone'] = "Số điện thoại đã tồn tại!";
        }
    }

    // Kiểm tra mật khẩu mạnh hay không
    if (strlen($password) < 8) {
        $errors['password'] = "Mật khẩu phải có ít nhất 8 ký tự.";
    }
    $uppercase = preg_match('/[A-Z]/', $password);
    $lowercase = preg_match('/[a-z]/', $password);
    $number = preg_match('/[0-9]/', $password);
    $special = preg_match('/[~!@#$%^&*()."?><}|]/', $password);

    if ($uppercase + $lowercase + $number + $special < 2) {
        $errors['password'] = "Mật khẩu không đủ mạnh.";
    }

    // Nếu có lỗi, chuyển hướng về trang đăng ký và truyền lỗi qua URL
    if (!empty($errors)) {
        $query_string = http_build_query(['errors' => $errors, 'old_data' => $_POST]);
        header("Location: register?$query_string");
        exit();
    }

    // Nếu không có lỗi, lưu vào database
    $options = [
        'memory_cost' => 1<<16, // 64MB (vừa đủ cho XAMPP)
        'time_cost'   => 3,     // Lặp lại 3 lần
        'threads'     => 2      // Sử dụng 2 CPU thread
    ];
    $hashed_password = password_hash($password, PASSWORD_ARGON2ID, $options);
    $sql = "INSERT INTO users (username, email, gender, phone_number, password) 
                   VALUES (:username, :email, :gender, :phone_number, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':gender' => $gender,
        ':phone_number' => $phone_number,
        ':password' => $hashed_password
    ]);

    // Chuyển hướng đến trang đăng nhập sau khi đăng ký thành công
    echo "<script>
        alert('Đăng ký thành công');
        window.location.href = 'login';
    </script>";
    exit();
}
?>
