<?php

session_start();
include('./partials/header.php');
include('./config/db.php');

$user_id = '676c783d10p98';

// Check if an open cart exists for the user
$cart_query = "SELECT * FROM carts WHERE user_id = '$user_id' AND status = 'open'";
$cart_result = mysqli_query($conn, $cart_query);

$cart_id = null;
$total_price = 0;
$cart_items = [];

if (mysqli_num_rows($cart_result) > 0) {
    $cart_row = mysqli_fetch_assoc($cart_result);
    $cart_id = $cart_row['cart_id'];

    // Fetch cart items
    $cart_items_query = "SELECT * FROM cart_items WHERE cart_id = '$cart_id'";
    $cart_item_result = mysqli_query($conn, $cart_items_query);

    if (mysqli_num_rows($cart_item_result) > 0) {
        $cart_items = mysqli_fetch_all($cart_item_result, MYSQLI_ASSOC);

        $price_array = array_column($cart_items, 'price_paid');
        $total_price = array_sum($price_array);
    }
}

$sec_key = 'sk_test_4947aade401a4b6cf289a04de8d50367b1142885';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $order_id = uniqid();

    if ($total_price <= 0) {
        $_SESSION['error'] = 'Your cart is empty!';
        header('Location: checkout.php');
        exit();
    }

    // Store order details in session
    $_SESSION['order_id'] = $order_id;
    $_SESSION['cart_id'] = $cart_id;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['phone_number'] = $phone_number;
    $_SESSION['address'] = $address;
    $_SESSION['amount'] = $total_price;
    $_SESSION['status'] = 'pending';

    // Paystack request payload
    $paystack_data = [
        'email' => $email,
        'amount' => $total_price * 100, // Convert to kobo
        'currency' => 'NGN',
        'reference' => $order_id,
        'callback_url' => 'http://localhost/benj_ecomm/verify_payment.php',
    ];

    // Sending Paystack request
    $curl_url = curl_init('https://api.paystack.co/transaction/initialize');
    curl_setopt($curl_url, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_url, CURLOPT_POST, true);
    curl_setopt($curl_url, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $sec_key, // Add space after "Bearer"
        'Content-Type: application/json'
    ]);
    curl_setopt($curl_url, CURLOPT_POSTFIELDS, json_encode($paystack_data));
    $response = curl_exec($curl_url);

    if (curl_errno($curl_url)) {
        $_SESSION['error'] = 'Payment initialization error: ' . curl_error($curl_url);
        header('Location: checkout.php');
        exit();
    }

    curl_close($curl_url);

    $result = json_decode($response, true);

    if ($result && isset($result['status']) && $result['status']) {
        header('Location: ' . $result['data']['authorization_url']);
        exit();
    } else {
        $_SESSION['error'] = 'Payment initialization failed, try again';
        header('Location: checkout.php');
        exit();
    }
}

?>

<div class="container py-5">
    <h1>Checkout</h1>
    <div class="table-responsive row">
        <div class="col-6">
            <form class="p-3 border bg-light" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Proceed to Pay</button>
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
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No products in cart</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="p-3 bg-light text-center">
                <h3>Total: ₦<?php echo number_format($total_price, 2); ?></h3>
            </div>
        </div>
    </div>
</div>

<?php include('./partials/footer.php'); ?>