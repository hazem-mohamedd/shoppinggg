<?php
include 'dbConnection.php';

if (!isset($_SESSION['username'])) {
    header("Location: account.php");
    exit();
}

// Handle cart removal
if (isset($_GET['remove']) && isset($_SESSION['cart'])) {
    $index = $_GET['remove'];
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
    header("Location: cart.php");
    exit();
}

// Handle checkout
if (isset($_POST['checkout'])) {
    $user_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE username = '{$_SESSION['username']}'"))['id'];
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    $tax = $total * 0.175; // 17.5% tax
    $grand_total = $total + $tax;

    $sql = "INSERT INTO orders (user_id, name, email, phone, address, city, postalCode, paymentType, total, status) 
            VALUES ('$user_id', '$_SESSION[username]', 'user@example.com', '1234567890', '123 Street', 'Cairo', 12345, 'Cash', '$grand_total', 'pending')";
    mysqli_query($conn, $sql);
    $order_id = mysqli_insert_id($conn);

    foreach ($_SESSION['cart'] as $item) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$order_id', '{$item['id']}', '{$item['quantity']}', '{$item['price']}')";
        mysqli_query($conn, $sql);
    }
    unset($_SESSION['cart']);
    header("Location: index.php?message=Order placed successfully!");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Cart - Redstore</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <a href="index.php"><img src="images/logo.png" width="125px"></a>
            </div>
            <nav>
                <ul id="MenuItems">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="account.php">Account</a></li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <a href="cart.php"><img src="images/cart.png" width="30px" height="30px"></a>
            <img src="images/menu.png" class="menu-icon" onclick="menutoggle()">
        </div>
    </div>

    <div class="cart-page">
        <table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total = 0;
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $index => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;

                // Get image name from DB
                $result = mysqli_query($conn, "SELECT image FROM products WHERE id = '{$item['id']}'");
                $image_data = mysqli_fetch_assoc($result);
                $image_name = $image_data['image'];

                // Build image path
                $image_path = __DIR__ . '/Admin/uploads/' . $image_name;
                $image_url = 'Admin/uploads/' . $image_name;

                echo "<tr>
                    <td class='cart-info'>";

                // Check if image exists
                if ($image_name && file_exists($image_path)) {
                    echo "<img src='$image_url' style='width:100px; height:auto;'>";
                } else {
                    echo "<img src='images/placeholder.png' style='width:100px; height:auto;'>";
                }

                echo "<div>
                        <p>{$item['name']}</p>
                        <small>Price: \${$item['price']}</small>
                        <br>
                        <a href='cart.php?remove=$index'>Remove</a>
                    </div>
                    </td>
                    <td>{$item['quantity']}</td>
                    <td>\$$subtotal</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>Your cart is empty.</td></tr>";
        }
        ?>
    </tbody>
</table>

        <div class="total-price">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td><?php echo isset($total) ? "\$$total" : "$0.00"; ?></td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td><?php echo isset($total) ? "\$" . ($total * 0.175) : "$0.00"; ?></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td><?php echo isset($total) ? "\$" . ($total + ($total * 0.175)) : "$0.00"; ?></td>
                </tr>
            </table>
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <form method="post">
                    <button type="submit" name="checkout" class="btn">Checkout</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col-1">
                    <h3>Download Our App</h3>
                    <p>Download App for Android and ios mobile phone.</p>
                    <div class="app-logo">
                        <img src="images/play-store.png">
                        <img src="images/app-store.png">
                    </div>
                </div>
                <div class="footer-col-2">
                    <img src="images/logo-white.png">
                    <p>Our Purpose Is To Sustainably Make the Pleasure and Benefits of Sports Accessible to the Many.</p>
                </div>
                <div class="footer-col-3">
                    <h3>Useful Links</h3>
                    <ul>
                        <li>Coupons</li>
                        <li>Blog Post</li>
                        <li>Return Policy</li>
                        <li>Join Affiliate</li>
                    </ul>
                </div>
                <div class="footer-col-4">
                    <h3>Follow us</h3>
                    <ul>
                        <li>Facebook</li>
                        <li>Twitter</li>
                        <li>Instagram</li>
                        <li>Youtube</li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="copyright">Copyright 2021 - Apurba Kr. Pramanik</p>
        </div>
    </div>

    <script>
        var MenuItems = document.getElementById("MenuItems");
        MenuItems.style.maxHeight = "0px";
        function menutoggle() {
            if (MenuItems.style.maxHeight == "0px") {
                MenuItems.style.maxHeight = "200px";
            } else {
                MenuItems.style.maxHeight = "0px";
            }
        }
    </script>
</body>
</html>
