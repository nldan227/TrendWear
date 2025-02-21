<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleFrm.css">
    <title>Đăng nhập</title>
</head>
<body>
    <?php 
    include '../layout/header.php';
    $error_message = isset($_GET['error']) ? $_GET['error'] : '';
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>
    <div class="main">
        <form action="ctlLogin.php" method="POST" class="frm" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <p>ĐĂNG NHẬP</p>
            <div class="field-input">
                <div class="label">
                    <i class="fa-solid fa-user"></i>
                    <label for="username" style="font-weight: 600">Tên đăng nhập</label>
                    <label for="username" style="color: red;">*</label>

                </div>
                <input type="text" name="username" id="username" placeholder="Nhập tên đăng nhập" >
            </div>

            <div class="field-input">
                <div class="label">
                    <i class="fa-solid fa-lock"></i>
                    <label for="password" style="font-weight: 600" >Mật khẩu</label>
                    <label for="username"  style="color: red;">*</label>
                </div>
                <input type="password" name="password" id="password" placeholder="Nhập mật khẩu">
            </div>

            <span class="error" id="Error" style="text-align: center; color: red; margin-bottom:2%"><?php echo htmlspecialchars($error_message); ?></span>

            <div class="submit">
                <input type="submit" style = "font-weight: 600; font-size: 16px" class="btn" name="submit" value="Đăng nhập" required>
            </div>
        </form>
    </div>
</body>
</html>