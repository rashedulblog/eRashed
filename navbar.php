<?php
$page = basename($_SERVER["PHP_SELF"]);
require_once("./db.php");
?>
<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="#">ECom.</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $page === "index.php" ? "active" : null ?>" aria-current="page" href="./">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $page === "shop.php" ? "active" : null ?>" href="./shop.php">Shop</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#offcanvasCart" data-bs-toggle="offcanvas">Cart</a>
        </li>
        <?php if (!isset($_SESSION['user'])) { ?>
          <li class="nav-item">
            <a class="nav-link <?= $page === "log-in.php" ? "active" : null ?>" href="./log-in.php">Log in</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $page === "register.php" ? "active" : null ?>" href="./register.php">Register</a>
          </li>
        <?php } else { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?= $page === "profile.php" || $page === "change-password.php" || $page === "order-history.php" ? "active" : null ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?= $_SESSION['user']->name ?? null ?>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="./profile.php">Profile</a></li>
              <li><a class="dropdown-item" href="./change-password.php">Change Password</a></li>
              <li><a class="dropdown-item" href="./order-history.php">Order History</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="./logout.php">Logout</a></li>
            </ul>
          </li>
        <?php } ?>
      </ul>
      <form action="./search.php" method="post" class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasExampleCart">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasExampleCart">My Cart</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <?php
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
      $cartData = $_SESSION['cart'];
    ?>

      <?php
      foreach ($cartData as $pId => $count):
        $pData = db::conn()->query("SELECT * FROM `products` WHERE `id` = $pId")->fetch_assoc();
      ?>
        <div class="card mb-3" id="cart-item-<?= $pData['id'] ?>">
          <h5 class="card-header d-flex justify-content-between align-items-center">
            <?= htmlspecialchars($pData['name'] ?? '') ?>
            <button class="btn btn-close" type="button" aria-label="Remove" onclick="confirmDeleteCart(<?= $pData['id'] ?>)"></button>
          </h5>
          <div class="card-body">
            <p><?= htmlspecialchars($pData['discount_price'] ?? '') ?></p>
            <div class="d-flex justify-content-start align-items-center gap-3">
              Count :
              <button class="btn btn-sm btn-dark" onclick="incrementCart(<?= $pData['id'] ?>)">+</button>
              <span id="count<?= $pData['id'] ?>"><?= $count ?></span>
              <button class="btn btn-sm btn-dark" onclick="decrementCart(<?= $pData['id'] ?>)">-</button>
            </div>
          </div>
        </div>
      <?php endforeach ?>
      <a href="./checkout.php">Checkout</a>
    <?php } else { ?>
      <div>
        No product Found
      </div>
    <?php } ?>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCartModal" tabindex="-1" aria-labelledby="deleteCartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteCartModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to remove this product from your cart?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" onclick="executeCartDelete()">Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
  let productToDeleteId = null;

  const incrementCart = (id) => {
    const count = document.getElementById("count" + id);
    let preInNum = +count.textContent;
    count.textContent = ++preInNum;
    fetch(`./api/cart/updateCart.php?id=${id}&count=${preInNum}`);
  };

  const decrementCart = (id) => {
    const count = document.getElementById("count" + id);
    let preDeNum = +count.textContent;
    if (preDeNum == 1) {
      return;
    }
    count.textContent = --preDeNum;
    fetch(`./api/cart/updateCart.php?id=${id}&count=${preDeNum}`);
  };

  const confirmDeleteCart = (id) => {
    productToDeleteId = id;
    const modalElement = document.getElementById('deleteCartModal');
    const deleteModal = new bootstrap.Modal(modalElement);
    deleteModal.show();
  };

  const executeCartDelete = () => {
    if (!productToDeleteId) return;

    fetch(`./api/cart/deleteCart.php?id=${productToDeleteId}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          const itemCard = document.getElementById("cart-item-" + productToDeleteId);
          if (itemCard) {
            itemCard.remove();
          }
          const remainingItems = document.querySelectorAll("#offcanvasCart .card");
          if (remainingItems.length === 0) {
            document.querySelector("#offcanvasCart .offcanvas-body").innerHTML = `
              <div>No product Found</div>
            `;
          }
        }
        const modalElement = document.getElementById('deleteCartModal');
        const deleteModal = bootstrap.Modal.getInstance(modalElement);
        if (deleteModal) {
          deleteModal.hide();
        }
        productToDeleteId = null;
      })
      .catch((err) => console.error("Error deleting item from cart:", err));
  };
</script>