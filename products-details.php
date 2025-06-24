<?php
include 'dbConnection.php';

if (!isset($_SESSION['username'])) {
    header("Location: account.php");
    exit();
}

$product_id = isset($_GET['id']) ? $_GET['id'] : 1;
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = '$product_id'"));
$related_products = mysqli_query($conn, "SELECT * FROM products WHERE id != '$product_id' LIMIT 4");

if (isset($_POST['add_to_cart'])) {
    $cart_item = [
        'id' => $product['id'],
        'name' => $product['product_name'],
        'price' => $product['price'],
        'quantity' => 1
    ];
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][] = $cart_item;
    header("Location: products-details.php?id=$product_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Product Details - Redstore</title>
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

    <div class="single-product">
        <div class="small-container">
            <div class="row">
                <div class="col-2">
<?php
$image_name = $product['image'];
$image_path = __DIR__ . '/Admin/uploads/' . $image_name;
$image_url = 'Admin/uploads/' . $image_name;

if ($image_name && file_exists($image_path)) {
    echo '<img src="' . $image_url . '" style="width:100%;">';
} else {
    echo '<img src="images/placeholder.png" style="width:100%;">';
}
?>                   
                </div>
                <div class="col-2">
                    <p>Home / Shoes</p>
                    <h1><?php echo $product['product_name']; ?></h1>
                    <h4>$<?php echo $product['price']; ?></h4>
                    <select>
                        <option>Select Size</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                    </select>
                    <form method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                    </form>
                    <h3>Product Details</h3>
                    <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="small-container">
        <h2 class="title">Related Products</h2>
        <div class="row">
            <?php while ($related_product = mysqli_fetch_assoc($related_products)): ?>
                <div class="col-4">
                    <a href="products-details.php?id=<?php echo $related_product['id']; ?>">
                        <img src="<?php echo $related_product['image']; ?>">
                        <h4><?php echo $related_product['product_name']; ?></h4>
                        <p>$<?php echo $related_product['price']; ?></p>
                    </a>
                </div>
            <?php endwhile; ?>
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
