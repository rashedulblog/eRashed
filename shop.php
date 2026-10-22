<?php
require_once("./header.php");
require_once("./db.php");

$conn = db::conn();

$search = isset($_GET['search']) ? safeData($_GET['search']) : "";
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : "newest";
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$itemsPerPage = 8;
$offset = ($currentPage - 1) * $itemsPerPage;

$categoryQuery = "SELECT * FROM `categories` ORDER BY `name` ASC";
$categoryResult = $conn->query($categoryQuery);
$categories = $categoryResult ? $categoryResult->fetch_all(MYSQLI_ASSOC) : [];

$baseProductQuery = "SELECT p.*, c.name AS category_name FROM `products` p LEFT JOIN `categories` c ON c.id = p.category_id WHERE 1=1";
$params = [];

if ($search !== "") {
    $escapedSearch = mysqli_real_escape_string($conn, $search);
    $baseProductQuery .= " AND p.name LIKE '%$escapedSearch%'";
}

if ($categoryId > 0) {
    $baseProductQuery .= " AND p.category_id = $categoryId";
}

switch ($sort) {
    case 'price_low':
        $baseProductQuery .= " ORDER BY COALESCE(NULLIF(p.discount_price, 0), p.regular_price) ASC";
        break;
    case 'price_high':
        $baseProductQuery .= " ORDER BY COALESCE(NULLIF(p.discount_price, 0), p.regular_price) DESC";
        break;
    case 'name_az':
        $baseProductQuery .= " ORDER BY p.name ASC";
        break;
    case 'name_za':
        $baseProductQuery .= " ORDER BY p.name DESC";
        break;
    default:
        $baseProductQuery .= " ORDER BY p.id DESC";
        break;
}

$countQuery = "SELECT COUNT(*) AS total FROM `products` p WHERE 1=1";
if ($search !== "") {
    $countQuery .= " AND p.name LIKE '%$escapedSearch%'";
}
if ($categoryId > 0) {
    $countQuery .= " AND p.category_id = $categoryId";
}

$totalProducts = 0;
$countResult = $conn->query($countQuery);
if ($countResult && $countResult->num_rows > 0) {
    $totalProducts = (int) $countResult->fetch_assoc()['total'];
}

$totalPages = max(1, (int) ceil($totalProducts / $itemsPerPage));
if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $itemsPerPage;
}

$productQuery = $baseProductQuery . " LIMIT $itemsPerPage OFFSET $offset";
$productResult = $conn->query($productQuery);
$products = $productResult ? $productResult->fetch_all(MYSQLI_ASSOC) : [];
?>
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="fw-bold mb-1">Shop</h1>
            <p class="text-muted mb-0">Discover quality products curated for your lifestyle.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-dark rounded-pill px-3 py-2"><?php echo $totalProducts; ?> items</span>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="./shop.php" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Search</label>
                    <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>" placeholder="Search products...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="category" class="form-select">
                        <option value="0">All categories</option>
                        <?php foreach ($categories as $category) { ?>
                            <option value="<?php echo (int) $category['id']; ?>" <?php echo $categoryId === (int) $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                        <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name_az" <?php echo $sort === 'name_az' ? 'selected' : ''; ?>>Name: A-Z</option>
                        <option value="name_za" <?php echo $sort === 'name_za' ? 'selected' : ''; ?>>Name: Z-A</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-dark">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($products)) { ?>
        <div class="text-center py-5">
            <div class="display-6 mb-3">No products found</div>
            <p class="text-muted">Try adjusting your search or selecting another category.</p>
            <a href="./shop.php" class="btn btn-outline-dark">Reset filters</a>
        </div>
    <?php } else { ?>
        <div class="row g-4">
            <?php foreach ($products as $product) {
                $regularPrice = isset($product['regular_price']) ? (float) $product['regular_price'] : 0;
                $discountPrice = isset($product['discount_price']) && $product['discount_price'] !== null && $product['discount_price'] !== '' ? (float) $product['discount_price'] : $regularPrice;
                $productImage = !empty($product['image']) ? './assets/images/products/' . htmlspecialchars($product['image'], ENT_QUOTES) : './assets/images/hero-banner.avif';
            ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <a href="./product-details.php?id=<?php echo (int)$product['id']; ?>">
                            <img src="<?php echo $productImage; ?>" class="card-img-top object-fit-cover" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>" style="height: 260px;">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <span class="text-uppercase text-muted small fw-semibold"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span>
                            <h5 class="card-title mt-2">
                                <a href="./product-details.php?id=<?php echo (int)$product['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h5>
                            <div class="mt-auto">
                                <p class="mb-3">
                                    <?php if ($regularPrice > $discountPrice) { ?>
                                        <span class="text-decoration-line-through text-muted me-2">$<?php echo number_format($regularPrice, 2); ?></span>
                                    <?php } ?>
                                    <span class="fw-bold text-dark">$<?php echo number_format($discountPrice, 2); ?></span>
                                </p>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-outline-dark flex-grow-1" href="./product-details.php?id=<?php echo (int)$product['id']; ?>">View Details</a>
                                    <a class="btn btn-dark flex-grow-1" href="./addCart.php?id=<?php echo (int)$product['id']; ?>">Add to cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php if ($totalPages > 1) { ?>
            <nav class="mt-5" aria-label="Shop pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $currentPage === 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="./shop.php?search=<?php echo urlencode($search); ?>&category=<?php echo $categoryId; ?>&sort=<?php echo urlencode($sort); ?>&page=<?php echo max(1, $currentPage - 1); ?>">Previous</a>
                    </li>

                    <?php for ($page = 1; $page <= $totalPages; $page++) { ?>
                        <li class="page-item <?php echo $page === $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="./shop.php?search=<?php echo urlencode($search); ?>&category=<?php echo $categoryId; ?>&sort=<?php echo urlencode($sort); ?>&page=<?php echo $page; ?>"><?php echo $page; ?></a>
                        </li>
                    <?php } ?>

                    <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="./shop.php?search=<?php echo urlencode($search); ?>&category=<?php echo $categoryId; ?>&sort=<?php echo urlencode($sort); ?>&page=<?php echo min($totalPages, $currentPage + 1); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php } ?>
    <?php } ?>
</div>

<?php require_once("./footer.php"); ?>
    

    