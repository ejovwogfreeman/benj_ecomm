<?php

session_start();

include('./partials/header.php');

include('./config/db.php');

// $user_id = '676c783d19098';
$user_id = '676c783d10p98';

// Check if an open cart exists for the user
$cart_query = "SELECT * FROM carts WHERE user_id = '$user_id' AND status = 'open'";
$cart_result = mysqli_query($conn, $cart_query);

if (mysqli_num_rows($cart_result) > 0) {
    // Open cart exists, get cart ID
    $cart_row = mysqli_fetch_assoc($cart_result);
    $cart_id = $cart_row['cart_id'];

    // Check if product is already in the cart
    $cart_items_query = "SELECT * FROM cart_items WHERE cart_id = '$cart_id'";
    $cart_item_result = mysqli_query($conn, $cart_items_query);

    if (mysqli_num_rows($cart_item_result) > 0) {
        // Product already in cart, update quantity
        $cart_items = mysqli_fetch_all($cart_item_result, MYSQLI_ASSOC);
    }
}


?>

<div class="container py-5">

    <h1>Your Cart</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price (₦)</th>
                    <th>Price Paid (₦)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cart_items)): ?>
                    <?php foreach ($cart_items as $product): ?>
                        <tr>
                            <td><?php echo $product['product_name'] ?></td>
                            <td><?php echo $product['quantity'] ?></td>
                            <td><?php echo number_format($product['unit_price']) ?></td>
                            <td><?php echo number_format($product['price_paid']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr style="text-align: center;">
                        <td colspan='4'>No product</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('./partials/footer.php') ?>