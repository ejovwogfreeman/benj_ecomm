<?php

session_start();

include('./partials/header.php');

include('./config/db.php');

$sql = "SELECT * FROM products ORDER BY created_at DESC";

$query = mysqli_query($conn, $sql);

$result = mysqli_fetch_all($query, MYSQLI_ASSOC);

$message = isset($_SESSION['message']) ? $_SESSION['message'] : null;

?>

<div class="container py-5">
    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Successful!</strong> <?php echo $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']) ?>
    <?php endif; ?>

    <h1>All Proucts</h1>
    <div class="row">
        <?php if (count($result) > 0): ?>
            <?php foreach ($result as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <div class="card">
                        <img src="<?php echo 'data:image/jpeg;base64,' . base64_encode($product['product_image']) ?>" class="card-img-top" alt="...">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="card-title m-0"><?php echo $product['product_name'] ?></h5>
                                <span>
                                    <small class="category-text"><?php echo $product['product_category'] ?></small>
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="card-title m-0">₦<?php echo number_format($product['product_price']) ?></h5>
                                <span class="text-danger">₦<del><?php echo number_format($product['product_price'] + 0.2 * $product['product_price']) ?></del></span>
                            </div>
                            <p class="card-text"><?php echo substr($product['product_description'], 0, 50) ?></p>
                            <a href="<?php echo 'product.php?id=' . $product['product_id'] ?>" class="btn btn-primary w-100">View Product</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No product to display</p>
        <?php endif; ?>
    </div>
</div>

<?php include('./partials/footer.php') ?>