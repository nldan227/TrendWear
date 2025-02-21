<?php 
    include '../layout/header.php';
    include '../../backend/config.php';

    $keyword = $_GET['keyword'] ?? '';

    if (empty($keyword)) {
        echo "<h3 style='text-align:center;'>Vui lòng nhập từ khóa để tìm kiếm!</h3>";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết Quả Tìm Kiếm</title>
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

        .no-results {
            text-align: center;
            font-size: 20px;
            color: #777;
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <h2 style="text-align:center; margin-top: 2%">Kết quả tìm kiếm cho: "<?= htmlspecialchars($keyword) ?>"</h2>

    <div class="product-container">
        <?php
        // Lấy sản phẩm có tên chứa từ khóa tìm kiếm
        $sql = "SELECT products.productID, products.name AS product_name, products.img, products.price, sizes.size_name, sizes.size_id, product_sizes.stock 
                FROM products
                INNER JOIN product_sizes ON products.productID = product_sizes.product_id
                INNER JOIN sizes ON sizes.size_id = product_sizes.size_id
                WHERE products.name LIKE :keyword";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            echo "<div class='no-results'>Không tìm thấy sản phẩm nào phù hợp!</div>";
        } else {
            $stockBySize = [];
            $products = [];

            foreach ($results as $row) {
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

                $stockBySize[$productID][$sizeID] = [
                    'sizeName' => $size,
                    'stock' => $stock
                ];
            }

            // Hiển thị sản phẩm tìm thấy
            foreach ($products as $productID => $product) {
                $totalStock = array_reduce($stockBySize[$productID], function($sum, $item) {
                    return $sum + $item['stock'];
                }, 0);

                $stockJson = htmlspecialchars(json_encode($stockBySize[$productID], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');

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
                echo "<p class='product-price'>₫" . number_format($product['price'], 0, ',', '.') . "</p>";
                echo "</div></div>";
            }
        }
        ?>
    </div>

    <script>
        function openModal(productID, title, imgSrc, price, totalStock, stockBySize) {
            alert(`Sản phẩm: ${title}\nGiá: ₫${price}\nSố lượng còn: ${totalStock}`);
        }
    </script>

</body>
</html>
