<?php

session_start();

include('./config/db.php');

if (!isset($_SESSION['order_id'])) {
    $_SESSION['error'] = 'invalid order session';
    header('Loction: checkout.php');
    exit();
}

// retrieve informations from session storage
$order_id = $_SESSION['order_id'];
$cart_id = $_SESSION['cart_id'];
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$phone_number = $_SESSION['phone_number'];
$address = $_SESSION['address'];
$amount = $_SESSION['amount'];
$status = $_SESSION['status'];
$created_at = date('Y-m-d H:i:s');

$insert_order_query = "INSERT INTO orders(order_id, cart_id, user_id, email, phone_number, address, amount, status, created_at) VALUES ('$order_id', '$cart_id', '$user_id', '$email', '$phone_number', '$address', '$amount', '$status', '$created_at')";

mysqli_query($conn, $insert_order_query);


// update cart status
$update_cart_query = "UPDATE carts SET status = 'closed' WHERE cart_id = '$cart_id'";

mysqli_query($conn, $update_cart_query);

// clear session
unset($_SESSION['order_id'], $_SESSION['cart_id'], $_SESSION['user_id'], $_SESSION['email'], $_SESSION['phone_number'], $_SESSION['address'], $_SESSION['amount'], $_SESSION['status']);

$_SESSION['success'] = 'Payment successful!, Your order has been placed';

header('Location: success.php');

exit();
