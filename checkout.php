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

        $price_array = [];

        foreach ($cart_items as $product) {
            array_push($price_array, $product['price_paid']);
        }

        $total_price = array_sum($price_array);
    }
}


?>

<div class="container py-5">

    <h1>Checkout</h1>
    <div class="table-responsive row">
        <div class="col-6">
            <form class="p-3 border bg-light">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Phone Number</label>
                    <input type="password" class="form-control" id="exampleInputPassword1">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Address</label>
                    <input type="password" class="form-control" id="exampleInputPassword1">
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
        <div class="col-6">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cart_items)): ?>
                        <?php foreach ($cart_items as $product): ?>
                            <tr>
                                <td><?php echo $product['product_name'] ?></td>
                                <td><?php echo $product['quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr style="text-align: center;">
                            <td colspan='4'>No product</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="p-3 bg-light text-center">
                <h3>Total: ₦<?php echo number_format($total_price) ?></h3>
            </div>
        </div>
    </div>
</div>

<?php include('./partials/footer.php') ?>