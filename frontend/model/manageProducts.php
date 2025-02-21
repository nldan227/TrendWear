<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh mục sản phẩm</title>
    <style>
         .title {
            margin-left: 2%;
            margin-top: 1%;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
            text-align: center;
        }

        th {
            background-color: #254880;
            text-align: center;
            border-color:rgb(0, 0, 0);
            color: white;

        }

        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-view {
            background-color:rgb(142, 144, 145);
            color: white;
            text-decoration: none;
            padding: 6px 10px;
            margin-right: 5px;
        }

        .btn-view:hover {
            background-color:rgb(118, 125, 129);
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 10px;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-add {
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            background-color: #254880; /* Màu nền xanh lá */
            color: white; 
            border: none; 
            border-radius: 5px;
            cursor: pointer; 
            width: 150px; 
            text-align: center; 
            margin-left: 85%;
        }

        .btn-add:hover {
            background-color:rgb(86, 112, 154); 
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(83, 81, 81, 0.5);
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 20px;
            width: 50%;
            border-radius: 10px;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        #productModal .modal-content table {
            width: 80%;
            text-align: center;
            margin-left: 10%;
        }
        .modal-content label {
            margin-top: 2%;
            font-weight: bold;
        }

        #productModal .modal-content table th {
            background-color:#254880;
            border-color: rgb(0, 0, 0);
           
        }

        .modal-content button:hover {
            opacity: 0.8;
        }

        .modal-content input {
            margin-top: 1%;
            padding: 5px;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
        }

      
    </style>
</head>
<body>
<?php 
    session_start();
    include("../layout/headerManager.html");
    include("../../backend/config.php");
    // Kiểm tra nếu chưa đăng nhập
    if (!isset($_SESSION['id'])) {
        header("Location: login"); 
        exit();
    }

    if ($_SESSION['role'] !== 'manager') {echo "<script>
        alert('Bạn không có quyền truy cập trang này!');
        history.back();  
      </script>";
        exit();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $sql_getDetailProduct = "SELECT products.productID, products.name AS productName, products.price, categories.name AS categoryName, sizes.size_name, SUM(stock) AS total_stock
            FROM products 
            INNER JOIN categories 
            ON products.categoryID = categories.categoryID
            INNER JOIN product_sizes
            ON products.productID = product_sizes.product_id
            INNER JOIN sizes 
            ON product_sizes.size_id = sizes.size_id 
            GROUP BY products.productID";
    $stmt = $pdo->query($sql_getDetailProduct);

    $sql_getSize = "SELECT * FROM sizes ORDER BY size_id";
    $size = $pdo->query($sql_getSize);

    $sql_getCategories = "SELECT * FROM Categories";
    $category = $pdo->query($sql_getCategories);


?>
    <div class="list-clothes">
        <div class="title">Danh mục sản phẩm</div>
        <hr class="underline">
        <div class=""></div>
        <table id="product-list" style="margin-left: 5%; margin-top: 2%; width: 90%">
            <thead>
                <tr>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Loại</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['productID'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['categoryName'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['total_stock'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= number_format($row['price'], 0, ',', '.') . " VND"; ?></td>
                        <td>
                            <button class="btn btn-view" onclick="openModal('productModal', '<?= htmlspecialchars($row['productID'], ENT_QUOTES, 'UTF-8'); ?>')">Xem</button>
                            <button class="btn btn-delete" onclick="deleteProduct('<?= htmlspecialchars($row['productID'], ENT_QUOTES, 'UTF-8'); ?>')">Xóa</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="btn">
            <button class="btn-add" onclick="openModal('addProductModal')">+ Thêm sản phẩm</button>
        </div>
    </div>

    <!-- Modal Chi tiết sản phẩm -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('productModal')">&times;</span>
            <h2 style = "text-align: center;">Chi tiết sản phẩm</h2>
            <label>Mã sản phẩm:</label>
            <input type="text" id="productID" readonly>
            <label>Tên sản phẩm:</label>
            <input type="text" id="productName">
            <label>Giá sản phẩm:</label>
            <input type="number" id="productPrice">
            <h3 style="margin-top: 2%;">Số lượng theo size:</h3>
            <table style="margin-top: 2%;" id="sizeTable">
                <thead>
                    <tr>
                        <th>Size</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <button style="margin-top: 2%; font-size: 16px; font-weight: bold; padding: 8px; color:white; border:none; border-radius: 5px; background-color: #254880;;" onclick="updateProduct()">Cập nhật</button>
        </div>
    </div>

    <!-- Modal Thêm sản phẩm -->
    <div id="addProductModal" class="modal">
        <div class="modal-content" style="height: 90%">
            <span class="close" onclick="closeModal('addProductModal')">&times;</span>
            <h2 style = "text-align: center;">Thêm sản phẩm</h2>
            <label>Mã sản phẩm:</label>
            <input type="text" id="newIDProduct">
            <label>Tên sản phẩm:</label>
            <input type="text" id="newProductName">
            <label>Danh mục:</label>
                <select id="newProductCategory" style="margin-top:2%; padding:5px">
                    <option value="">-- Chọn danh mục --</option>
                    <?php
                    while ($row = $category->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['categoryID']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            <label>Giá sản phẩm:</label>
            <input type="number" id="newProductPrice">
            <h3 style="margin-top: 2%;">Số lượng theo size:</h3>
            <table style="margin-top: 2%; width: 60%; margin-left: 20%" id="addSizeTable">
                <thead>
                    <tr>
                        <th>Size</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while ($row = $size->fetch( PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>{$row['size_name']}</td>";
                    echo "<td><input style='border:none; text-align:center' type='number' value='0' size-id={$row['size_id']}></td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
            <button style="margin-top: 2%; font-size: 16px; font-weight: bold; padding: 8px; color:white; border:none; border-radius: 5px; background-color: #254880;;" onclick="addProduct()">Thêm</button>
        </div>
    </div>
    <?php include("../layout/footerManager.html") ?>
</body>
<script>
    const csrfToken = '<?= $_SESSION['csrf_token']; ?>';

    function openModal(modalID, productID = null) {
        const modal = document.getElementById(modalID);
        modal.style.display = "flex";

        if (productID) {
            // Nếu có productID thì gọi API lấy chi tiết sản phẩm
            fetch(`ctlGetProductDetails.php?productID=${encodeURIComponent(productID)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('productID').value = data.productID;
                    document.getElementById('productName').value = data.productName;
                    document.getElementById('productPrice').value = data.price;

                    let sizeTable = document.querySelector("#sizeTable tbody");
                    sizeTable.innerHTML = ""; // Xóa dữ liệu cũ

                    data.sizes.forEach(size => {
                        let row = `<tr>
                                    <td>${size.size_name}</td>
                                    <td><input style="border: none; text-align: center" 
                                            type="number" value="${size.stock}" 
                                            data-size-id="${size.size_id}"></td>
                                </tr>`;
                        sizeTable.innerHTML += row;
                    });
                });
        }
    }

    function closeModal(modal) {
        document.getElementById(modal).style.display = "none";
    }

    function updateProduct() {
        let productID = document.getElementById('productID').value;
        let productName = document.getElementById('productName').value;
        let productPrice = document.getElementById('productPrice').value;
        if (!productName || !productPrice ){
            alert("Vui lòng nhập đủ thông tin.");
            exit;
        }
        let sizes = [];
        document.querySelectorAll("#sizeTable tbody tr").forEach(row => {
            let sizeID = row.querySelector("input").getAttribute("data-size-id");
            let stock = row.querySelector("input").value;
            sizes.push({ sizeID, stock });
        });

        fetch('ctlUpdateProduct.php', {
            method: 'POST',
            headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
         },
            body: JSON.stringify({ productID, productName, productPrice, sizes })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Cập nhật thành công!");
                closeModal();
                location.reload();
            } else {
                alert("Lỗi khi cập nhật!");
            }
        });
    }

    function deleteProduct(productID) {
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
            fetch(`ctlDltProduct.php`, {
                method: 'DELETE',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ productID: productID })
            })
            .then(response => response.text())
            .then(text => {
                console.log("Phản hồi từ server:", text);
                const data = JSON.parse(text);

                if (data.success) {
                    alert("Xóa thành công!");
                    location.reload();
                } else {
                    alert("Lỗi khi xóa: " + data.message);
                }
            })
            .catch(error => {
                console.error("Lỗi:", error);
                alert("Không thể xóa sản phẩm.");
            });
        }
    }
    
    function addProduct() {
        let newIDProduct = document.getElementById('newIDProduct').value;
        let newProductName = document.getElementById('newProductName').value;
        let newProductPrice = document.getElementById('newProductPrice').value;
        let newProductCategory = document.getElementById('newProductCategory').value; 

        if (!newIDProduct || !newProductName || !newProductPrice || !newProductCategory) {
            alert("Vui lòng nhập đầy đủ thông tin sản phẩm.");
            return;
        }
        let sizes = [];
        document.querySelectorAll("#addSizeTable tbody tr").forEach(row => {
            let sizeID = row.querySelector("input").getAttribute("size-id");
            let stock = row.querySelector("input").value.trim(); 

             // Chỉ thêm những size có số lượng và lớn hơn 0
            if (stock !== "" && stock > 0) {
                sizes.push({sizeID, stock });
            }
        });

        if (sizes.length === 0) {
            alert("Vui lòng nhập ít nhất một kích cỡ sản phẩm.");
            return;
        }

        fetch('ctlAddProduct.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
         },
            body: JSON.stringify({ newIDProduct, newProductName, newProductPrice, newProductCategory, sizes })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Thêm sản phẩm thành công!");
            } else {
                alert("Lỗi khi thêm sản phẩm: " + data.message);
            }
        })
        .catch(error => console.error("Lỗi:", error));
    }
</script>
</html>