<?php
require_once("./header.php");
require_once("./db.php");
?>
<div class="container py-5">
    <!-- Hero Section -->
    <div class="row align-items-center min-vh-75 mb-5">

        <!-- Left Content -->
        <div class="col-lg-6 mb-4 mb-lg-0">
            <span class="badge bg-danger mb-3">New Collection 2026</span>
            <h1 class="display-4 fw-bold mb-3">
                Discover Premium Fashion For Everyone
            </h1>
            <p class="text-muted fs-5 mb-4">
                Shop the latest trends with exclusive discounts.
                Premium quality products at affordable prices.
            </p>

            <div class="d-flex gap-3">
                <a href="#" class="btn btn-dark btn-lg px-4">Shop Now</a>
                <a href="#" class="btn btn-outline-dark btn-lg px-4">Explore</a>
            </div>
        </div>

        <!-- Right Content -->
        <div class="col-lg-6 text-center">
            <img src="./assets/images/hero-banner.avif"
                class="img-fluid rounded shadow-lg"
                alt="Hero Image">
        </div>

    </div>

    <!-- Latest Products -->
    <div class="row py-5 g-4">
        <div class="col-12 text-center display-6">Latest Products</div>
        <?php
        //Select latest 6 product query 
        $query = "SELECT * FROM `products` ORDER BY `id` DESC LIMIT 0, 6";
        $runQuery = db::conn()->query($query);
        $products = $runQuery->fetch_all(MYSQLI_ASSOC);

        if ($runQuery->num_rows > 0) {
        ?>
            <?php foreach ($products as $product) { ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <a href="./product-details.php?id=<?= (int)$product['id'] ?>">
                            <img src="./assets/images/products/<?= htmlspecialchars($product['image']) ?>" class="card-img-top object-fit-cover" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 300px;">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="./product-details.php?id=<?= (int)$product['id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h5>
                            <p class="card-text small">
                                <?php if (!empty($product['regular_price']) && (float)$product['regular_price'] > (float)$product['discount_price']) { ?>
                                    <span class="text-decoration-line-through text-muted">$<?= number_format((float)$product['regular_price'], 2) ?></span>
                                <?php } ?>
                                <span class="fw-bold text-dark">$<?= number_format((float)$product['discount_price'], 2) ?></span>
                            </p>
                            <div class="mt-auto d-flex gap-2">
                                <a class="btn btn-outline-dark flex-grow-1" href="./product-details.php?id=<?= (int)$product['id'] ?>">View Details</a>
                                <a class="btn btn-dark flex-grow-1" href="./addCart.php?id=<?= (int)$product['id'] ?>">Add to cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="h1 text-center">No product found</div>
        <?php } ?>
    </div>
</div>

<?php
require_once("./footer.php");
?>

<?php if (isset($_GET['addCart']) && $_GET['addCart'] == 1) {  ?>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Successfull</strong>
            <small>New product added to cart</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Thank you for buying from us!
        </div>
    </div>
</div>

<script>
    const toastLiveExample = document.getElementById('liveToast')
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
    toastBootstrap.show()
</script>

<?php } ?>