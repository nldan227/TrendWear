<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Header</title>
    <style> 
        @import url('https://fonts.googleapis.com/css2?family=Bungee+Spice&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Oi&display=swap');
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        #header-search {
            height:12%;
            width: 100%;
            background-color: #254880;
            display: flex;
            align-items: center;   
            justify-content: space-around;
        }

        #header-menu {
            height:8%;
            width: 100%;
            display: flex;
            align-items: center; /* căn theo trục dọc */
            justify-content: space-around;
        }
        #header-menu .right{
            display: flex;
        }
        #header-menu .right a{
            color: #254880;
            font-weight: bold;
            font-size: 18px;
            text-decoration: none;
        }

        .sub {
            margin: 0px 20px;
        }
     
        #header-search .left p {
            color: white;
        } 
       
        #header-search .right {
            width: 50%;
            height: 50%;
            display: flex;
            
        }
        
        .search-input {
            position: relative; /* Làm cha để button căn theo */
            width: 100%;
            height: 100%;
        }

        .search-input input {
            width: 100%;
            height: 100%;
            padding: 0 40px 0 10px; /* Tạo khoảng trống bên phải cho button */
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .search-input button {
            position: absolute;
            right: 5px; /* Đặt ở góc phải */
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 18px;
            color: #254880;
        }

        .search-input button:hover {
            color: #1a355e; /* Hover effect */
        }

        .search-input form {
            display: flex;
            height: 100%;
        }

        .search-icon {
            margin-left: 2%;
            height: 35%;
            background-color: #254880;
        }

        .search-input input {
            width: 100%;
            height: 100%;
            padding: 0 2%;
            border: none;
            border-radius: 5px;

        }

        .search-close {
            border: none;
            background-color: white;
        }
    </style>
</head>
<body>
    <?php 
        session_start();
        include("../../backend/config.php");
        if(isset($_SESSION['id'])){
            $stmt = $pdo->prepare('SELECT username FROM users WHERE userID = :id');
            $stmt->execute(['id' => $_SESSION['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    ?>
    <div id="header-menu">
        <div class="left">
            <p style="color: #254880;">Miễn phí vận chuyển cho mọi đơn hàng từ 999.000 VNĐ - Hotline 1900 2666 79</p> 
        </div>
        <?php if(!isset($row)): ?>
            <div class="right">
                <div class="sub">
                    <i class="fa-solid fa-user"></i>
                    <a href="login" class="">Đăng nhập</a>
                </div>
                <div class="sub">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <a href="register" class="">Đăng ký</a>
                </div>
            </div>
        <?php endif; ?>
        <?php if(isset($row)): ?>
            <div class="right">
                <div class="sub">
                    <i class="fa-solid fa-user"></i>
                    <a href="ctlLogout.php" class="">Đăng xuất</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div id="header-search">
        <div class="left">
            <a href="/FIS_Intern/TrendWear/" style="text-decoration: none; color: inherit;">
                <p style="font-family: 'Oi', serif; font-weight: 400; font-size: 30px; font-style: normal;">TrendWear</p>
            </a>
        </div>
        <div class="right">
            
            <div class="search-input">
                <form action="searchResults.php" method="GET" id="search-bar">
                    <input type="text" name="keyword" style="color: black; font-size:16px;" placeholder="Tìm kiếm sản phẩm">
                    <button type="submit" id="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>

                </form>
            </div>
           
            <a href="cart">
                <i class="fa-solid fa-cart-shopping" style="color: white; font-size: 30px; padding-left: 20px;"></i>
            </a>
        </div>
    </div>
</body>
</html>