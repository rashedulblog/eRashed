<?php
require_once("./header.php");
require_once("./db.php");

$conn = db::conn();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$product = null;
$categoryName = "Uncategorized";

if ($id > 0) {
    $stmt = $conn->prepare("SELECT p.*, c.name AS category_name FROM `products` p LEFT JOIN `categories` c ON c.id = p.category_id WHERE p.id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc();
            if (!empty($product['category_name'])) {
                $categoryName = $product['category_name'];
            }
        }
        $stmt->close();
    }
}
?>

<div class="container py-5">
    <?php if (!$product) { ?>
        <div class="row justify-content-center py-5">
            <div class="col-md-6 text-center">
                <div class="card border-0 shadow-sm p-5">
                    <div class="display-1 text-muted mb-3">404</div>
                    <h3 class="fw-bold mb-3">Product Not Found</h3>
                    <p class="text-muted mb-4">Sorry, the product you are looking for does not exist or has been removed.</p>
                    <a href="./shop.php" class="btn btn-dark px-4 py-2">Back to Shop</a>
                </div>
            </div>
        </div>
    <?php } else { 
        $regularPrice = (float)($product['regular_price'] ?? 0);
        $discountPrice = (float)($product['discount_price'] ?? 0);
        $hasDiscount = ($discountPrice > 0 && $discountPrice < $regularPrice);
        $finalPrice = $hasDiscount ? $discountPrice : ($regularPrice > 0 ? $regularPrice : $discountPrice);
        $discountPercent = $hasDiscount && $regularPrice > 0 ? round((($regularPrice - $discountPrice) / $regularPrice) * 100) : 0;
        $imagePath = !empty($product['image']) ? './assets/images/products/' . htmlspecialchars($product['image'], ENT_QUOTES) : './assets/images/hero-banner.avif';
    ?>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="./shop.php" class="text-decoration-none text-dark">Shop</a></li>
                <li class="breadcrumb-item"><a href="./shop.php?category=<?= (int)$product['category_id'] ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($categoryName) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>

        <!-- Product Main Details -->
        <div class="row g-5 align-items-center mb-5">
            <!-- Product Image -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm overflow-hidden rounded-4">
                    <img src="<?= $imagePath ?>" class="img-fluid object-fit-cover w-100" style="max-height: 480px;" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <span class="badge bg-secondary mb-2 px-3 py-2 text-uppercase"><?= htmlspecialchars($categoryName) ?></span>
                <h1 class="fw-bold display-5 mb-3"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="d-flex align-items-center gap-3 mb-4">
                    <h2 class="fw-bold text-dark mb-0">$<?= number_format($finalPrice, 2) ?></h2>
                    <?php if ($hasDiscount) { ?>
                        <span class="fs-5 text-muted text-decoration-line-through">$<?= number_format($regularPrice, 2) ?></span>
                        <span class="badge bg-danger px-2 py-1"><?= $discountPercent ?>% OFF</span>
                    <?php } ?>
                </div>

                <p class="text-muted mb-4 lead">
                    Experience top tier quality with <?= htmlspecialchars($product['name']) ?>. Perfectly designed for comfort, style, and everyday functionality.
                </p>

                <!-- Actions -->
                <form action="./addCart.php" method="GET" class="d-flex gap-3 mb-4 align-items-center">
                    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                    <button type="submit" class="btn btn-dark btn-lg px-5 py-3 rounded-3 d-flex align-items-center gap-2">
                        <span>Add to Cart</span>
                    </button>
                    <a href="./checkout.php" class="btn btn-outline-dark btn-lg px-4 py-3 rounded-3">Buy Now</a>
                </form>

                <hr class="my-4">

                <!-- Feature Highlights -->
                <div class="row g-3 text-muted small">
                    <div class="col-6 d-flex align-items-center gap-2">
                        <span>In Stock & Ready to Ship</span>
                    </div>
                    <div class="col-6 d-flex align-items-center gap-2">
                        <span>Free Standard Delivery</span>
                    </div>
                    <div class="col-6 d-flex align-items-center gap-2">
                        <span>30-Day Money Back Guarantee</span>
                    </div>
                    <div class="col-6 d-flex align-items-center gap-2">
                        <span>Secure Checkout</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        <?php
        $relatedStmt = $conn->prepare("SELECT p.*, c.name AS category_name FROM `products` p LEFT JOIN `categories` c ON c.id = p.category_id WHERE p.id != ? ORDER BY p.id DESC LIMIT 4");
        if ($relatedStmt) {
            $relatedStmt->bind_param("i", $id);
            $relatedStmt->execute();
            $relatedResult = $relatedStmt->get_result();
            $relatedProducts = $relatedResult ? $relatedResult->fetch_all(MYSQLI_ASSOC) : [];
            $relatedStmt->close();

            if (!empty($relatedProducts)) {
        ?>
            <div class="pt-5 border-top">
                <h3 class="fw-bold mb-4">You May Also Like</h3>
                <div class="row g-4">
                    <?php foreach ($relatedProducts as $relProduct) { 
                        $relRegPrice = (float)($relProduct['regular_price'] ?? 0);
                        $relDiscPrice = (float)($relProduct['discount_price'] ?? 0);
                        $relHasDisc = ($relDiscPrice > 0 && $relDiscPrice < $relRegPrice);
                        $relFinalPrice = $relHasDisc ? $relDiscPrice : ($relRegPrice > 0 ? $relRegPrice : $relDiscPrice);
                        $relImg = !empty($relProduct['image']) ? './assets/images/products/' . htmlspecialchars($relProduct['image'], ENT_QUOTES) : './assets/images/hero-banner.avif';
                    ?>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <a href="./product-details.php?id=<?= (int)$relProduct['id'] ?>" class="text-decoration-none text-dark">
                                    <img src="<?= $relImg ?>" class="card-img-top object-fit-cover" style="height: 220px;" alt="<?= htmlspecialchars($relProduct['name']) ?>">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <span class="text-uppercase text-muted small fw-semibold"><?= htmlspecialchars($relProduct['category_name'] ?? 'Uncategorized') ?></span>
                                    <h6 class="card-title mt-2">
                                        <a href="./product-details.php?id=<?= (int)$relProduct['id'] ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($relProduct['name']) ?>
                                        </a>
                                    </h6>
                                    <div class="mt-auto pt-3">
                                        <p class="mb-3">
                                            <?php if ($relHasDisc) { ?>
                                                <span class="text-decoration-line-through text-muted me-2 small">$<?= number_format($relRegPrice, 2) ?></span>
                                            <?php } ?>
                                            <span class="fw-bold text-dark">$<?= number_format($relFinalPrice, 2) ?></span>
                                        </p>
                                        <a href="./addCart.php?id=<?= (int)$relProduct['id'] ?>" class="btn btn-outline-dark btn-sm w-100">Add to cart</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php 
            }
        }
        ?>
    <?php } ?>
</div>

<?php require_once("./footer.php"); ?>
