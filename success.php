<?php

session_start();

include('./partials/header.php');

?>

<div class="container py-5">

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="text-center alert alert-success alert-dismissible fade show" role="alert">
            <strong>Successful!</strong> <?php echo $_SESSION['success'] ?><br>
            <a href="index.php" class="alert-link text-decoration-underline">Continue Shopping</a>
        </div>
        <?php unset($_SESSION['success']) ?>
    <?php endif; ?>

</div>

<?php

include('./partials/footer.php');

?>