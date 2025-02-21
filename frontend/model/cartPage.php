<?php
 include("../layout/header.php"); 
 include("../../backend/config.php"); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <style>
        .title {
            font-weight: bold;
            padding: 2%;
        }
        .main {
            display: flex;
        }
        .left-cart {
            width: 60%;
            height: 100%;
        }
        .right-cart {
            width: 40%;
            height: 100%;
        }
        .cart-items {
            display: flex;
        }
        .detail {
            display: flex;
            padding: 1%;
        }
        .sub-infor2 {
            display: flex; 
            align-items: flex-end; /* Căn giữa theo chiều dọc */
            gap: 10px; /* Khoảng cách giữa label và input */
            font-size: 14px;
        }

        .img {
            width: 15%;
            padding: 1%;
            margin-left: 2%;
        }
        .img img {
            width: 100%;
            height: auto;
        }
        .quantity {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .quantity button {
            width: 30px;
            height: 30px;
            border: none;
            background: #ddd;
            cursor: pointer;
            font-size: 16px;
        }

        .quantity input {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            margin: 0 5px;
        }
        .order-summary {
            margin: 4%;
        }
        .order-summary input {
            padding: 8px;
            margin-top: 2px;
            border: 1px solid #d9d9d9;
            height: 10%;
        }
    </style>
</head>
<body>
    <?php
    // Kiểm tra nếu người dùng chưa đăng nhập
    if (!isset($_SESSION['id'])) {
        echo "<script>
                alert('Vui lòng đăng nhập để xem giỏ hàng.');
                window.location.href = 'login'; // Chuyển hướng về trang đăng nhập
            </script>";
        exit();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];
    $user_id = $_SESSION['id'];
    $sql = "SELECT SUM(quantity) AS total 
            FROM cart_items
            WHERE userID = :userID ";
    $stmt = $pdo -> prepare($sql);
    $stmt->execute([':userID' => $user_id]);
    $total_cartItems = $stmt->fetch(PDO::FETCH_ASSOC);
    $sql = "SELECT users.name AS nameUser, products.name, products.productID, products.price, products.img, sizes.size_id, sizes.size_name, cart_items.quantity 
            FROM cart_items
            INNER JOIN products
            ON products.productID = cart_items.productID
            INNER JOIN sizes 
            ON cart_items.size_id = sizes.size_id
            INNER JOIN users
            ON users.userID = cart_items.userID 
            WHERE users.userID = :user_id ";

    $stmt = $pdo -> prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    // Lấy tất cả kết quả dưới dạng mảng liên kết
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    ?>
    <div class="main">
        <div class="left-cart">
            <div class="title">
                <h2>Giỏ Hàng</h2>
                <h3>Mặt hàng (<span class="summary-items"><?= $total_cartItems['total'] ?></span>)</h3> 
            </div>
            <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $cartItem): ?>
                    <div class="cart-items" data-product-id="<?= $cartItem['productID'] ?>" data-size-id="<?= $cartItem['size_id'] ?>">
                        <div class="img">
                            <img src="<?= $cartItem['img'] ?>" alt="<?= $cartItem['name'] ?>">
                        </div>
                        <div class="detail">
                            <div class="sub-infor1">
                                <p><strong><?= $cartItem['name'] ?></strong></p>
                                <p class="price">Giá: <?= number_format($cartItem['price'], 0, ',', '.') ?> VND</p>
                                <p>Kích cỡ: <?= $cartItem['size_name'] ?></p>
                                <div class="quantity">
                                    <button class="decrease">-</button>
                                    <input type="text" class="quantity-input" value="<?= $cartItem['quantity'] ?>">
                                    <button class="increase">+</button>
                                </div>
                            </div>
                            <div class="sub-infor2">
                                <label>Tạm tính:</label>
                                <input type="text" style="border: none" class="totalPriceItems" 
                                    value="<?= number_format($cartItem['price'] * $cartItem['quantity'], 0, ',', '.') ?> VND">
                            </div>
                        </div>
                    </div>
                    <hr style="width: 90%; margin-left: 2%; border: 0.5px solid rgb(198, 198, 198)">
                 <?php endforeach; ?>
            <?php else: ?>
                <p style="margin-left: 3%">Hiện không có sản phẩm nào trong giỏ hàng.</p>
            <?php endif; ?>
        </div>
        <div class="right-cart">
            <div class="order-summary">
                <h2>Thông tin đơn hàng (<span class="summary-items"><?= $total_cartItems['total'] ?></span>)</h2>
                <p style="margin: 10px 0px ">Địa chỉ nhận hàng:</p>
                <input type="text" name="nameReceiver" placeholder="Họ tên người nhận">
                <input type="text" name="phoneReceiver" placeholder="Số điện thoại người nhận">
                <input style = "margin-top: 10px; width: 74%" type="text" name="addressReceiver" placeholder="Địa chỉ nhận hàng">
                <input style = "margin-top: 10px; width: 74%" type="text" name="note" placeholder="Ghi chú">
                <p style = "margin-top: 10px;">Phí vận chuyển: <span>Miễn Phí</span></p>
                <p style="margin-top: 10px"><strong>Tổng tiền: <span id="total-price"><?= number_format(array_sum(array_map(fn($cartItem) => $cartItem['price'] * $cartItem['quantity'], $cartItems)), 0, ',', '.') ?></span> VND</strong></p>
                
                <button style="margin-top: 10px; width: 78%" class="checkout">Đặt hàng</button>
                <a style="display: block; margin-top: 10px" href="index.php" class="continue-shopping">Tiếp tục mua hàng</a>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = '<?= $_SESSION['csrf_token']; ?>';

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector(".checkout").addEventListener("click", placeOrder);
            const updateTotal = () => {
                let subtotal = 0;
                let quantity_item = 0;
                document.querySelectorAll(".cart-items").forEach((item) => {
                    let price = parseFloat(item.querySelector(".price").innerText.replace(/\D/g, ""));
                    let quantity = parseInt(item.querySelector(".quantity-input").value);
                    let subtotalEl = item.querySelector(".totalPriceItems");                    
                    let total = price * quantity;
                    subtotalEl.value = total.toLocaleString() + " VND";
                    subtotal += total;
                    quantity_item += quantity;
                });
                document.querySelectorAll(".summary-items").forEach(element => {
                    element.innerHTML = quantity_item;
                });
                document.getElementById("total-price").innerText = subtotal.toLocaleString();
            };

            document.querySelectorAll(".increase").forEach((btn) => {
                btn.addEventListener("click", function () {
                    let input = this.previousElementSibling;
                    let newQuantity = parseInt(input.value) + 1;
                    input.value = newQuantity;
                    updateCart(input, newQuantity);
                });
            });

            document.querySelectorAll(".decrease").forEach((btn) => {
                btn.addEventListener("click", function () {
                    let input = this.nextElementSibling;
                    let newQuantity = parseInt(input.value) - 1;

                    if (newQuantity < 1) {
                        if (confirm("Bạn có muốn xoá sản phẩm này không?")) {
                            deleteCartItem(input);
                            return;
                        } else {
                            return;
                        }
                    }

                    input.value = newQuantity;
                    updateCart(input, newQuantity);
                });
            });

            function updateCart(input, newQuantity) {
                let cartItem = input.closest(".cart-items");
                let productID = cartItem.getAttribute("data-product-id");
                let sizeID = cartItem.getAttribute("data-size-id");

                fetch("ctlUpdateCart.php", {
                    method: "POST",
                    headers: { 
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                     },
                    body: JSON.stringify({ productID, sizeID, quantity: newQuantity})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateTotal();
                    } else {
                        alert("❌ Cập nhật số lượng thất bại!");
                    }
                });
            }

            function deleteCartItem(input) {
                let cartItem = input.closest(".cart-items");
                let productID = cartItem.getAttribute("data-product-id");
                let sizeID = cartItem.getAttribute("data-size-id");

                fetch("ctlDltCartItem.php", {
                    method: "POST",
                    headers: { 
                        "Content-Type": "application/json", 
                        "X-CSRF-TOKEN": csrfToken 
                    },
                    body: JSON.stringify({ productID, sizeID})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cartItem.remove();
                        updateTotal();
                    } else {
                        alert("❌ Xóa sản phẩm thất bại!");
                    }
                });
            }

            // Xử lý khi ấn nút Thanh toán
            function placeOrder() {
                let nameReceiver = document.querySelector("input[name='nameReceiver']").value;
                let phoneReceiver = document.querySelector("input[name='phoneReceiver']").value;
                let addressReceiver = document.querySelector("input[name='addressReceiver']").value;
                let note = document.querySelector("input[name='note']").value;
                
                if (!nameReceiver || !phoneReceiver || !addressReceiver) {
                    alert("❌ Vui lòng nhập đầy đủ thông tin nhận hàng!");
                    return;
                }

                let orderItems = [];
                let total_price = document.querySelector("#total-price").innerText.replace(/\D/g, "");
                document.querySelectorAll(".cart-items").forEach((item) => {
                    let productID = item.getAttribute("data-product-id");
                    console.log(productID);
                    let sizeID = item.getAttribute("data-size-id");
                    console.log(sizeID);
                    let quantity = item.querySelector(".quantity-input").value;
                    console.log(quantity);
                    orderItems.push({ productID, sizeID, quantity});
                    console.log(JSON.stringify(orderItems));

                });

                fetch("ctlPlaceOrder.php", {
                    method: "POST",
                    headers: { 
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                     },
                    body: JSON.stringify({
                        nameReceiver, phoneReceiver, addressReceiver, note, total_price, orderItems
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("🎉 Đặt hàng thành công!");
                        location.reload();
                    } else {
                        alert("❌ Lỗi khi đặt hàng!");
                    }
                });
            }
            
        });
    </script>
</body>
</html>