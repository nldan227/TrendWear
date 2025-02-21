<?php 
    include '../layout/header.php' ;
    include '../../backend/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
       
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            position: relative;
            cursor: pointer;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .product-info {
            padding: 10px;
        }

        .product-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .product-price {
            color: red;
            font-size: 18px;
            font-weight: bold;
        }

        .product-rating {
            color: #fdd835;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Modal (Popup) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow-y: auto; /* Cho phép cuộn xuống nếu nội dung dài */
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 20px;
            width: 50%;
            border-radius: 10px;
            position: relative;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
        }

        .modal img {
            width: 60%;
            height: auto;
            border-radius: 5px;
            margin-left: 20%;
        }

        .modal h2 {
            text-align: center;
        }

        .size-options {
            margin: 15px 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .size-btn {
            padding: 8px 12px;
            border: 1px solid #333;
            background: white;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
        }

        .size-btn:hover {
            background: #ddd;
        }

        .btn-group {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .cart-btn, .buy-btn {
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .cart-btn {
            background: #f39c12;
            color: white;
        }

        .buy-btn {
            background: #e74c3c;
            color: white;
        }

        .cart-btn:hover {
            background: #d68910;
        }

        .buy-btn:hover {
            background: #c0392b;
        }
        .size-btn.active {
            background:rgb(144, 144, 144); 
            color: white;
            border-color:rgb(0, 0, 0);
        }
    </style>
</head>
<body>
    <div class="product-container">
        <?php
         if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $sizesQuery = $pdo->query("SELECT size_id, size_name FROM sizes ORDER BY size_id");
        $sizes = $sizesQuery->fetchAll(PDO::FETCH_ASSOC);
        // Lấy danh sách sản phẩm từ database
        $stmt = $pdo->query("
        SELECT products.productID, products.name AS product_name, products.img, products.price, sizes.size_name, sizes.size_id, product_sizes.stock 
        FROM products 
        INNER JOIN categories 
        ON products.categoryID = categories.categoryID 
        INNER JOIN product_sizes 
        ON products.productID = product_sizes.product_id 
        INNER JOIN sizes 
        ON sizes.size_id= product_sizes.size_id 
        WHERE categories.name = 'Thời trang trẻ em';");

        $stockBySize = []; // Mảng lưu stock theo size cho từng sản phẩm
        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productID = htmlspecialchars($row['productID'], ENT_QUOTES, 'UTF-8');
            $sizeID = htmlspecialchars($row['size_id'], ENT_QUOTES, 'UTF-8');
            $size = htmlspecialchars($row['size_name'], ENT_QUOTES, 'UTF-8');
            $stock = htmlspecialchars($row['stock'], ENT_QUOTES, 'UTF-8');
            if (!isset($products[$productID])) {
                $products[$productID] = [
                    'name' => $row['product_name'],
                    'img' => $row['img'],
                    'price' => $row['price']
                ];
            }
            $stockBySize[$productID][$sizeID]= [
                'sizeName' => $size,  
                'stock' => $stock
            ];

        }
            // Thêm số lượng theo từng size vào mảng của sản phẩm đó
        foreach ($products as $productID => $product) {
            // Tính tổng số lượng stock của tất cả các size cho một productID.
            $totalStock = array_reduce($stockBySize[$productID], function($sum, $item) {
                return $sum + $item['stock'];
            }, 0);
            $stockJson = htmlspecialchars(json_encode($stockBySize[$productID], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
            echo "<script> console.log('Stock Data:', $stockJson); </script>";    
            echo "<div class='product-card' onclick=\"openModal(
                '{$productID}', 
                '{$product['name']}', 
                '{$product['img']}', 
                '{$product['price']}', 
                '{$totalStock}', 
                '{$stockJson}'
            )\">";
            
            echo "<img class='product-image' src='{$product['img']}' alt='{$product['name']}'>";
            echo "<div class='product-info'>";
            echo "<p class='product-title'>{$product['name']}</p>";
            echo "<p class='product-price'>₫{$product['price']}</p>";
            echo "</div></div>";
        }
        ?>
    </div>


    <!-- Modal hiển thị chi tiết sản phẩm -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="modalImg" src="" alt="Chi tiết sản phẩm">
            <h2 id="modalTitle"></h2>
            <p><strong>Giá:</strong> <span id="modalPrice"></span></p>
            <label for="quantity"><strong>Số lượng:</strong></label>
            <input style="width: 40px" type="number" id="quantity" name="quantity" min="1">
            <p><strong>Size:</strong></p>
            <div class="size-options">
                <?php foreach ($sizes as $size): ?>
                    <button class="size-btn" data-size="<?= htmlspecialchars($size['size_id']); ?>">
                        <?= $size['size_name']; ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <p><strong>Số lượng có sẵn:</strong> <span id="available-stock"></span></p>
        
            <div class="btn-group">
                <button class="cart-btn" onclick="addToCart()">🛒 Thêm vào giỏ hàng</button>
            </div>
        </div>
    </div>

    <script>        
        const csrfToken = '<?= $_SESSION['csrf_token']; ?>';

        function openModal(productID, title, imgSrc, price, totalStock, stockBySize) {
            document.getElementById('productModal').setAttribute('data-product-id', productID); // Lưu ID sản phẩm vào modal
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalImg').src = imgSrc;
            document.getElementById('modalPrice').textContent = price;
            document.getElementById('productModal').style.display = 'block';
            document.getElementById('available-stock').textContent = totalStock;
            let stockData = JSON.parse(stockBySize);

            document.querySelectorAll('.size-btn').forEach(btn => {
                let sizeID = btn.getAttribute('data-size');

                if (stockData[sizeID] && stockData[sizeID].stock > 0) {
                    btn.classList.remove('disabled');
                    btn.removeAttribute('title');
                    btn.style.cursor = 'pointer';
                } else {
                    btn.classList.add('disabled'); // Làm mờ và vô hiệu hóa size hết hàng
                    btn.setAttribute('title', 'Hết hàng');
                    btn.style.cursor = 'not-allowed';
                }
                // Thêm sự kiện click chỉ cho size còn hàng
                btn.onclick = function() {
                    if (!btn.classList.contains('disabled')) {
                        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        document.getElementById('available-stock').textContent = stockData[sizeID]?.stock || 0;
                    } else {
                        alert("❌ Sản phẩm này đã hết hàng ở size bạn chọn!");
                    }
                };
            });
        }

        function closeModal() {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('productModal').style.display = 'none';
        }
        function addToCart() {
            let productID = document.getElementById('productModal').getAttribute('data-product-id');
            let quantity = document.getElementById('quantity').value
            let selectedSize = document.querySelector('.size-btn.active')?.getAttribute('data-size') || "";
            console.log("Size được chọn:", selectedSize);
           // Kiểm tra nếu giá hoặc size bị thiếu
            if (quantity === "") {
                alert("⚠️ Lỗi: Vui lòng nhập số lượng sản phẩm");
                exit();
            }
            if (selectedSize === "") {
                alert("⚠️ Vui lòng chọn size trước khi thêm vào giỏ hàng!");
                exit();
            }
            // Gửi sản phẩm lên server bằng AJAX
            fetch('ctlAddCartItem.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    "X-CSRF-TOKEN": csrfToken
                 },
                body: JSON.stringify({ productID, quantity, selectedSize })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("🛒 Sản phẩm đã được thêm vào giỏ hàng!");
                    } else {
                        alert("❌ Lỗi khi thêm giỏ hàng!");
                    }
            });
            }

    </script>

</body>
</html>
