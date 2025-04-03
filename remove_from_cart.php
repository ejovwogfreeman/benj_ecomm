<?php

session_start();
include('config/db.php');

$product_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$product_id) {
    die("Invalid product ID.");
}

$user_id = '676c783d10p98';

// Check if an open cart exists for the user
$cart_query = "SELECT * FROM carts WHERE user_id = '$user_id' AND status = 'open'";
$cart_result = mysqli_query($conn, $cart_query);

if (mysqli_num_rows($cart_result) > 0) {
    $cart_row = mysqli_fetch_assoc($cart_result);
    $cart_id = $cart_row['cart_id'];

    // Check if product is in the cart
    $cart_items_query = "SELECT * FROM cart_items WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
    $cart_item_result = mysqli_query($conn, $cart_items_query);

    if (mysqli_num_rows($cart_item_result) > 0) {
        $cart_item_row = mysqli_fetch_assoc($cart_item_result);
        $new_quantity = $cart_item_row['quantity'] - 1;
        $product_price = $cart_item_row['price_paid'] / $cart_item_row['quantity']; // Get unit price
        $new_price = $new_quantity * $product_price;

        if ($new_quantity > 0) {
            // Update quantity if still greater than zero
            $update_quantity_sql = "UPDATE cart_items 
                                    SET quantity = '$new_quantity', price_paid = '$new_price' 
                                    WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
            mysqli_query($conn, $update_quantity_sql);
        } else {
            // Remove item from cart if quantity is zero
            $delete_item_sql = "DELETE FROM cart_items WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
            mysqli_query($conn, $delete_item_sql);
        }

        // redirect
        // $_SESSION['message'] = 'cart updated successfully';
        header('Location: cart.php');
    } else {
        echo "Product not found in cart.";
    }
} else {
    echo "No open cart found.";
}
