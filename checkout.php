<?php
require_once("./header.php");
require_once("./db.php");
require_once("./SSLCommerz-PHP/OrderTransaction.php");

$paidStatus = $_GET['paid'] ?? null;
$isPaidSuccess = ($paidStatus === 'true') || (isset($_POST['status']) && in_array($_POST['status'], ['VALID', 'VALIDATED']));
$isPaidFailed = ($paidStatus === 'false') || (isset($_POST['status']) && $_POST['status'] === 'FAILED');
$isPaidCancel = ($paidStatus === 'cancel') || (isset($_POST['status']) && $_POST['status'] === 'CANCELLED');

$transactionId = $_POST['tran_id'] ?? $_SESSION['last_tran_id'] ?? null;
$orderRecord = null;

if ($isPaidSuccess) {
    $ot = new OrderTransaction();
    if (!empty($_POST['tran_id'])) {
        $tranId = $_POST['tran_id'];
        $updateSql = $ot->updateTransactionQuery($tranId, 'Processing');
        db::conn()->query($updateSql);
        $transactionId = $tranId;
    }

    if ($transactionId) {
        $safeTid = db::conn()->real_escape_string($transactionId);
        $orderQuery = db::conn()->query("SELECT * FROM orders WHERE transaction_id = '$safeTid'");
        if ($orderQuery && $orderQuery->num_rows > 0) {
            $orderRecord = $orderQuery->fetch_assoc();
        }
    }

    // Remove everything from the cart session after successful payment
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    if (isset($_SESSION['last_order_id'])) {
        unset($_SESSION['last_order_id']);
    }
    if (isset($_SESSION['last_tran_id'])) {
        unset($_SESSION['last_tran_id']);
    }
} elseif ($isPaidFailed && !empty($_POST['tran_id'])) {
    $ot = new OrderTransaction();
    $updateSql = $ot->updateTransactionQuery($_POST['tran_id'], 'Failed');
    db::conn()->query($updateSql);
} elseif ($isPaidCancel && !empty($_POST['tran_id'])) {
    $ot = new OrderTransaction();
    $updateSql = $ot->updateTransactionQuery($_POST['tran_id'], 'Cancelled');
    db::conn()->query($updateSql);
}
?>

<div class="container my-5">
    <?php if ($isPaidSuccess) : ?>
        <!-- Success State View -->
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 680px; border-radius: 18px; overflow: hidden;">
            <div class="card-body text-center p-4 p-md-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle shadow-sm" style="width: 84px; height: 84px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>
                </div>
                <h2 class="text-success fw-bold mb-2">Payment Successful!</h2>
                <p class="text-muted mb-4 fs-6">Thank you for your purchase. Your payment was confirmed, your cart has been cleared, and your order is now being processed.</p>

                <?php if ($orderRecord || !empty($transactionId)) : ?>
                    <div class="bg-light rounded-3 p-3 p-md-4 text-start mb-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Order Information</h6>
                        <div class="row g-2 small">
                            <?php if ($transactionId) : ?>
                                <div class="col-sm-6"><strong class="text-secondary">Transaction ID:</strong> <?= htmlspecialchars($transactionId) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($orderRecord['id'])) : ?>
                                <div class="col-sm-6"><strong class="text-secondary">Order ID:</strong> #<?= htmlspecialchars($orderRecord['id']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($orderRecord['amount'])) : ?>
                                <div class="col-sm-6"><strong class="text-secondary">Amount Paid:</strong> BDT <?= htmlspecialchars($orderRecord['amount']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($orderRecord['status'])) : ?>
                                <div class="col-sm-6"><strong class="text-secondary">Status:</strong> <span class="badge bg-success"><?= htmlspecialchars($orderRecord['status']) ?></span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="./shop.php" class="btn btn-primary px-4 py-2 fw-semibold">Continue Shopping</a>
                    <a href="./index.php" class="btn btn-outline-secondary px-4 py-2 fw-semibold">Go to Home</a>
                </div>
            </div>
        </div>
    <?php else : ?>
        <?php if ($isPaidFailed) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Payment Failed!</strong> Your transaction could not be completed. Please check your payment details and try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($isPaidCancel) : ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Payment Cancelled!</strong> You cancelled the transaction. You can proceed with checkout whenever you are ready.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php
        $cartItems = $_SESSION['cart'] ?? [];
        $subtotal = 0;
        $shippingCharge = 60;
        $total = 0;
        ?>

        <form action="./SSLCommerz-PHP/checkout_hosted.php" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <!-- Billing Information -->
                    <?php $loggedInUser = $_SESSION['user'] ?? null; ?>
                    <div class="mb-4">
                        <h3>Billing Information</h3>
                        <div class="mb-3">
                            <label for="billing_customer_name">Full Name</label>
                            <input type="text" class="form-control" id="billing_customer_name" placeholder="Enter full name" name="customer_name" value="<?= htmlspecialchars($loggedInUser->name ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="billing_house_number">House Number | Apartment</label>
                            <input type="text" class="form-control" id="billing_house_number" placeholder="Enter house number" name="house_number">
                        </div>
                        <div class="mb-3">
                            <label for="billing_street_address">Street</label>
                            <input type="text" class="form-control" id="billing_street_address" placeholder="Street address" name="street_address" value="<?= htmlspecialchars($loggedInUser->address ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="billing_cus_city">City</label>
                            <input type="text" class="form-control" id="billing_cus_city" placeholder="Enter city" name="cus_city">
                        </div>
                        <div class="mb-3">
                            <label for="billing_cus_postcode">ZIP Code</label>
                            <input type="text" class="form-control" id="billing_cus_postcode" placeholder="Enter ZIP code" name="cus_postcode">
                        </div>
                        <div class="mb-3">
                            <label for="billing_customer_mobile">Mobile Number</label>
                            <input type="text" class="form-control" id="billing_customer_mobile" placeholder="Enter mobile number" name="customer_mobile" value="<?= htmlspecialchars($loggedInUser->mobile ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="billing_customer_email">Email Address</label>
                            <input type="email" class="form-control" id="billing_customer_email" placeholder="Enter email address" name="customer_email" value="<?= htmlspecialchars($loggedInUser->email ?? '') ?>" required>
                        </div>
                    </div>
                    <!-- Shipping Information -->
                    <div class="mb-3">
                        <h3>Shipping Information</h3>
                        <div class="mb-3">
                            <label for="sameAsBilling">
                                <input type="checkbox" class="form-check-input" id="sameAsBilling">
                                Same as billing address
                            </label>
                        </div>
                        <div id="shippingInfo">
                            <div class="mb-3">
                                <label for="shipping_name">Full Name</label>
                                <input type="text" class="form-control" id="shipping_name" placeholder="Enter full name" name="shipping_name">
                            </div>
                            <div class="mb-3">
                                <label for="shipping_house_number">House Number | Apartment</label>
                                <input type="text" class="form-control" id="shipping_house_number" placeholder="Enter house number" name="shipping_house_number">
                            </div>
                            <div class="mb-3">
                                <label for="shipping_street_address">Street</label>
                                <input type="text" class="form-control" id="shipping_street_address" placeholder="Street address" name="shipping_street_address">
                            </div>
                            <div class="mb-3">
                                <label for="shipping_city">City</label>
                                <input type="text" class="form-control" id="shipping_city" placeholder="Enter city" name="shipping_city">
                            </div>
                            <div class="mb-3">
                                <label for="shipping_zip_code">ZIP Code</label>
                                <input type="text" class="form-control" id="shipping_zip_code" placeholder="Enter ZIP code" name="shipping_zip_code">
                            </div>
                            <div class="mb-3">
                                <label for="shipping_mobile_number">Mobile Number</label>
                                <input type="text" class="form-control" id="shipping_mobile_number" placeholder="Enter mobile number" name="shipping_mobile_number">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Shipping Areas (inside Dhaka | Outside Dhaka) -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4>Shipping Area</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shipping_area" id="insideDhaka" value="inside_dhaka" checked>
                                <label class="form-check-label" for="insideDhaka">
                                    Inside Dhaka (BDT 60)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shipping_area" id="outsideDhaka" value="outside_dhaka">
                                <label class="form-check-label" for="outsideDhaka">
                                    Outside Dhaka (BDT 120)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4>Order Summary</h4>
                        </div>
                        <div class="card-body">
                            <?php
                            if (empty($cartItems)) {
                                echo "<p class='text-muted my-3'>Your cart is empty.</p>";
                                echo "<a href='./shop.php' class='btn btn-outline-primary w-100'>Go to Shop</a>";
                            } else {
                                foreach ($cartItems as $productId => $quantity) {
                                    $product = db::conn()->query("SELECT * FROM `products` WHERE `id` = $productId")->fetch_assoc();
                                    if ($product) {
                                        $itemPrice = !empty($product['discount_price']) ? $product['discount_price'] : ($product['regular_price'] ?? 0);
                                        $itemTotal = $itemPrice * $quantity;
                                        $subtotal += $itemTotal;
                                        echo "<div class='d-flex justify-content-between mb-2'>
                                                <input type='hidden' name='product_id[]' value='{$product['id']}'>
                                                <input type='hidden' name='quantity[]' value='{$quantity}'>
                                                <input type='hidden' name='item_total[]' value='{$itemTotal}'>
                                                <span>{$product['name']} (x{$quantity})</span>
                                                <span>BDT {$itemTotal}</span>
                                              </div>";
                                    }
                                }
                                $total = $subtotal + $shippingCharge;
                                echo "<hr>";
                                echo "<div class='d-flex justify-content-between mb-2'>
                                        <div>Shipping Charge:</div>
                                        <div>BDT <span id='shippingCharge'>{$shippingCharge}</span></div>
                                      </div>";
                                echo "<hr>";
                                echo "<div class='d-flex justify-content-between'>
                                        <strong>Total:</strong>
                                        <strong>BDT <span id='totalAmount'>{$total}</span></strong>
                                      </div>";
                            }
                            ?>
                            <input type="hidden" name="shipping_charge" id="hiddenShippingCharge" value="<?= $shippingCharge ?>">
                            <input type="hidden" name="amount" id="hiddenTotalAmount" value="<?= $total ?>">
                        </div>
                    </div>

                    <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']->id)) : ?>
                        <input type="hidden" name="user_id" value="<?= $_SESSION['user']->id ?>">
                    <?php endif; ?>

                    <?php if (!empty($cartItems)) : ?>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            Proceed to Payment
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <script>
            const sameAsBilling = document.getElementById('sameAsBilling');
            const shippingInfo = document.getElementById('shippingInfo');

            const billingFields = {
                customer_name: document.querySelector('input[name="customer_name"]'),
                house_number: document.querySelector('input[name="house_number"]'),
                street_address: document.querySelector('input[name="street_address"]'),
                cus_city: document.querySelector('input[name="cus_city"]'),
                cus_postcode: document.querySelector('input[name="cus_postcode"]'),
                customer_mobile: document.querySelector('input[name="customer_mobile"]')
            };

            const shippingFields = {
                shipping_name: document.querySelector('input[name="shipping_name"]'),
                shipping_house_number: document.querySelector('input[name="shipping_house_number"]'),
                shipping_street_address: document.querySelector('input[name="shipping_street_address"]'),
                shipping_city: document.querySelector('input[name="shipping_city"]'),
                shipping_zip_code: document.querySelector('input[name="shipping_zip_code"]'),
                shipping_mobile_number: document.querySelector('input[name="shipping_mobile_number"]')
            };

            function copyBillingToShipping() {
                if (shippingFields.shipping_name && billingFields.customer_name) {
                    shippingFields.shipping_name.value = billingFields.customer_name.value;
                }
                if (shippingFields.shipping_house_number && billingFields.house_number) {
                    shippingFields.shipping_house_number.value = billingFields.house_number.value;
                }
                if (shippingFields.shipping_street_address && billingFields.street_address) {
                    shippingFields.shipping_street_address.value = billingFields.street_address.value;
                }
                if (shippingFields.shipping_city && billingFields.cus_city) {
                    shippingFields.shipping_city.value = billingFields.cus_city.value;
                }
                if (shippingFields.shipping_zip_code && billingFields.cus_postcode) {
                    shippingFields.shipping_zip_code.value = billingFields.cus_postcode.value;
                }
                if (shippingFields.shipping_mobile_number && billingFields.customer_mobile) {
                    shippingFields.shipping_mobile_number.value = billingFields.customer_mobile.value;
                }
            }

            function toggleShippingInfo() {
                if (!sameAsBilling || !shippingInfo) {
                    return;
                }

                if (sameAsBilling.checked) {
                    shippingInfo.style.display = 'none';
                    copyBillingToShipping();
                } else {
                    shippingInfo.style.display = 'block';
                }
            }

            if (sameAsBilling && shippingInfo) {
                sameAsBilling.addEventListener('change', toggleShippingInfo);

                Object.values(billingFields).forEach(function(input) {
                    if (input) {
                        input.addEventListener('input', function() {
                            if (sameAsBilling.checked) {
                                copyBillingToShipping();
                            }
                        });
                    }
                });

                toggleShippingInfo();
            }

            const subtotal = <?= (float)$subtotal ?>;
            const insideDhaka = document.getElementById('insideDhaka');
            const outsideDhaka = document.getElementById('outsideDhaka');
            const shippingChargeEl = document.getElementById('shippingCharge');
            const totalAmountEl = document.getElementById('totalAmount');
            const hiddenShippingCharge = document.getElementById('hiddenShippingCharge');
            const hiddenTotalAmount = document.getElementById('hiddenTotalAmount');

            function updateShipping() {
                const shippingFee = (insideDhaka && insideDhaka.checked) ? 60 : 120;
                if (shippingChargeEl) {
                    shippingChargeEl.textContent = shippingFee;
                }
                if (hiddenShippingCharge) {
                    hiddenShippingCharge.value = shippingFee;
                }
                const grandTotal = subtotal + shippingFee;
                if (totalAmountEl) {
                    totalAmountEl.textContent = grandTotal;
                }
                if (hiddenTotalAmount) {
                    hiddenTotalAmount.value = grandTotal;
                }
            }

            if (insideDhaka && outsideDhaka) {
                insideDhaka.addEventListener('change', updateShipping);
                outsideDhaka.addEventListener('change', updateShipping);
            }
        </script>
    <?php endif; ?>
</div>

<?php
require_once("./footer.php");
?>