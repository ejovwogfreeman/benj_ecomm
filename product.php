<?php

include('./partials/header.php');

include('./config/db.php');

$product_id = isset($_GET['id']) ? $_GET['id'] : null;

$sql = "SELECT * FROM products WHERE product_id = '$product_id'";

$query = mysqli_query($conn, $sql);

$product = mysqli_fetch_assoc($query);

?>

<div class="container py-5">
    <h1 class="text-start">Prouct Detail</h1>
    <div class="d-sm-flex d-block">
        <div style="flex: 1">
            <img src="<?php echo 'data:image/jpeg;base64,' . base64_encode($product['product_image']) ?>" class="card-img-top" alt="...">
        </div>
        <div style="flex: 1" class="ms-sm-3 mt-sm-0 ms-0 mt-2">
            <h2><?php echo $product['product_name'] ?></h2>
            <p>Category: <?php echo $product['product_category'] ?></p>
            <p><?php echo $product['product_description'] ?></p>
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="card-title m-0">₦<?php echo number_format($product['product_price']) ?></h5>
                <span class="text-danger">₦<del><?php echo number_format($product['product_price'] + 0.2 * $product['product_price']) ?></del></span>
            </div>
            <a href="<?php echo "add_to_cart.php?id=" . $product['product_id'] ?>" class="btn btn-primary">Add To Cart</a>
        </div>
    </div>
</div>

<?php include('./partials/footer.php') ?>