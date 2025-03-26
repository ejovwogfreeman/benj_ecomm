<?php

session_start();
include('config/db.php');

$product_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$product_id) {
    die("Invalid product ID.");
}

// Fetch product details
$product_sql = "SELECT * FROM products WHERE product_id = '$product_id'";
$product_query = mysqli_query($conn, $product_sql);

if (!$product_query || mysqli_num_rows($product_query) == 0) {
    die("Product not found.");
}

$product = mysqli_fetch_assoc($product_query);
$product_name = $product['product_name'];
$product_price = $product['product_price'];

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
    $cart_items_query = "SELECT * FROM cart_items WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
    $cart_item_result = mysqli_query($conn, $cart_items_query);

    if (mysqli_num_rows($cart_item_result) > 0) {
        // Product already in cart, update quantity
        $cart_item_row = mysqli_fetch_assoc($cart_item_result);
        $new_quantity = $cart_item_row['quantity'] + 1;
        $new_price = $new_quantity * $product_price;

        $update_quantity_sql = "UPDATE cart_items 
                                SET quantity = '$new_quantity', price_paid = '$new_price' 
                                WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
        mysqli_query($conn, $update_quantity_sql);
        // redirect
        $_SESSION['message'] = 'cart updated successfully';
        header('Location: index.php');
    } else {
        // Product not in cart, insert it
        $cart_item_id = uniqid();
        $created_at = date("Y-m-d H:i:s");
        $insert_cart_item_sql = "INSERT INTO cart_items (cart_item_id, cart_id, product_id, product_name, quantity, unit_price, price_paid) 
                                 VALUES ('$cart_item_id', '$cart_id', '$product_id', '$product_name', '1', '$product_price', '$product_price')";
        mysqli_query($conn, $insert_cart_item_sql);
        // redirect
        $_SESSION['message'] = 'cart updated successfully';
        header('Location: index.php');
    }
} else {
    // No open cart exists, create one
    $cart_id = uniqid();
    $created_at = date("Y-m-d H:i:s");

    $create_cart_sql = "INSERT INTO carts (cart_id, user_id, status, created_at) 
                    VALUES ('$cart_id', '$user_id', 'open', '$created_at')";

    // Execute the query and check if the cart is created
    if (mysqli_query($conn, $create_cart_sql)) {
        // Fetch the cart_id of the newly created cart
        $get_cart_id_sql = "SELECT cart_id FROM carts WHERE user_id = '$user_id' AND status = 'open' ORDER BY created_at DESC LIMIT 1";
        $cart_result = mysqli_query($conn, $get_cart_id_sql);

        if ($cart_row = mysqli_fetch_assoc($cart_result)) {
            $cart_id = $cart_row['cart_id']; // Retrieve the cart_id
        } else {
            die("Error fetching new cart ID: " . mysqli_error($conn));
        }

        // Now insert the product into the cart_items table
        $cart_item_id = uniqid();
        $insert_cart_item_sql = "INSERT INTO cart_items (cart_item_id, cart_id, product_id, product_name, quantity, unit_price, price_paid) 
                             VALUES ('$cart_item_id', '$cart_id', '$product_id', '$product_name', '1', '$product_price', '$product_price')";

        if (!mysqli_query($conn, $insert_cart_item_sql)) {
            die("Error adding product to cart: " . mysqli_error($conn));
        }

        // redirect
        $_SESSION['message'] = 'cart updated successfully';
        header('Location: index.php');
    } else {
        die("Error creating cart: " . mysqli_error($conn));
    }
}

// echo "Product added to cart successfully.";
