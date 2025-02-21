<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleFrm.css">
    <title>Đăng ký</title>
    <style>
    .gender-options {
        display: flex; /* Hiển thị các lựa chọn theo hàng ngang */
        gap: 20px; /* Khoảng cách giữa các lựa chọn */
        align-items: center; /* Căn giữa theo chiều dọc */
    }

    .gender-options label {
        display: flex;
        align-items: center;
        gap: 5px; /* khoảng cách giữa radio và chữ */
        font-size: 16px; 
    }

    .gender-options input{
        margin: 0;
    }

    .main {
        height: 100%;
    }
    
    .frm {
        height: 100%;
    }
   

    </style>
</head>
<body>
    <?php 
    include '../layout/header.php';
    $errors = $_GET['errors'] ?? [];
    $old_data = $_GET['old_data'] ?? [];
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>
    
    <div class="main">
        <form action="ctlRegister.php" method = "POST" class="frm" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <p>ĐĂNG KÝ</p>
            <div class="field-input">
                <div class="label">
                    <label for="username" style="font-weight: 600">Username</label>
                    <label for="username" style="color: red;">*</label>
                </div>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($old_data['username'] ?? '')?>" placeholder="Nhập email"  >
                <span class="error" id="ErrorUsername" style="text-align: center; color: red; margin-bottom:2%"><?= htmlspecialchars($errors['username'] ?? '') ?></span>
            </div>

            <div class="field-input">
                <div class="label">
                    <label for="password" style="font-weight: 600" >Mật khẩu</label>
                    <label for="username"  style="color: red;">*</label>
                </div>
                <input type="password" name="password" id="password" value="<?= htmlspecialchars($old_data['password'] ?? '')?>" placeholder="Nhập mật khẩu">
                <span class="error" id="ErrorPass" style="text-align: center; color: red; margin-bottom:2%"><?= htmlspecialchars($errors['password'] ?? '') ?></span>
            </div>

            <div class="field-input">
                <div class="label">
                    <label for="username" style="font-weight: 600">Địa chỉ email</label>
                    <label for="username" style="color: red;">*</label>

                </div>
                <input type="text" name="email" id="email" value="<?= htmlspecialchars($old_data['email'] ?? '')?>" placeholder="Nhập email" >
                <span class="error" id="ErrorEmail" style="text-align: center; color: red; margin-bottom:2%"><?= htmlspecialchars($errors['email'] ?? '') ?></span>
            </div>

            <div class="field-input">
                <div class="label">
                    <label for="full_name" style="font-weight: 600" >Họ và tên</label>
                    <label for="full_name"  style="color: red;">*</label>
                </div>
                    <input type="text" name="full_name" id="full_name" value="<?= htmlspecialchars($old_data['full_name'] ?? '')?>" placeholder="Nhập họ tên" >
                    <span class="error" id="ErrorEmail" style="text-align: center; color: red; margin-bottom:2%"><?= htmlspecialchars($errors['full_name'] ?? '') ?></span>
            </div> 

            <div class="field-input">
                <div class="label">
                    <label for="phone" style="font-weight: 600" >Số điện thoại</label>
                    <label for="phone"  style="color: red;">*</label>
                </div>
                <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($old_data['phone'] ?? '')?>" placeholder="Nhập số điện thoại">
                <span class="error" id="ErrorPhone" style="text-align: center; color: red; margin-bottom:2%"><?= htmlspecialchars($errors['phone'] ?? '') ?></span>
            </div>

            <div class="field-input">
                <div class="label" style="margin-bottom: 10px;">
                    <label for="gender" style="font-weight: 600;">Giới tính</label>
                </div>
                <div class="gender-options">
                    <label>
                        <input type="radio" name="gender" value="Male"> Nam
                    </label>
                    <label>
                        <input type="radio" name="gender" value="Female"> Nữ
                    </label>
                </div>
            </div>

            <div class="submit">
                <input type="submit" style = "font-weight: 600; font-size: 16px" class="btn" name="submit" value="Đăng ký" required>
            </div>
        </form>
    </div>
</body>
</html>