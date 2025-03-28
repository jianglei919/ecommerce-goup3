<?php
session_start();
require_once 'config/Database.php';
require_once 'classes/Product.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->getConnection();
$product = new Product($db);
$cart = $_SESSION['cart'] ?? [];
$total = 0;

foreach ($cart as $id => $qty) {
    $p = $product->getProduct($id);
    $total += $p['price'] * $qty;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal = trim($_POST['postal'] ?? '');
    $payment = $_POST['payment'] ?? '';
    $card = preg_replace('/\\D/', '', $_POST['card'] ?? '');
    $expiry = trim($_POST['expiry'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');
    $note = trim($_POST['note'] ?? '');

    if (!preg_match('/^\\d{10,15}$/', $phone)) {
        $errors[] = "Invalid phone number.";
    }
    if (!preg_match('/^\\d{12,19}$/', $card)) {
        $errors[] = "Invalid card number.";
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\\/(\\d{2})$/', $expiry)) {
        $errors[] = "Invalid expiry date.";
    }
    if (!preg_match('/^\\d{3,4}$/', $cvv)) {
        $errors[] = "Invalid CVV.";
    }

    if (empty($errors)) {
        $db->begin_transaction();

        try {
            $userId = $_SESSION['user']['id'];

            // Insert into orders_payment
            $stmt = $db->prepare("INSERT INTO orders_payment (user_id, fullname, phone, address, city, postal, payment, card, expiry, cvv, note, total, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssssssssds", $userId, $fullname, $phone, $address, $city, $postal, $payment, $card, $expiry, $cvv, $note, $total);
            $stmt->execute();

            // Insert into orders
            $stmt = $db->prepare("INSERT INTO orders (user_id, total, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("id", $userId, $total);
            $stmt->execute();
            $order_id = $stmt->insert_id;

            // Insert into order_items
            foreach ($cart as $product_id => $quantity) {
                $productData = $product->getProduct($product_id);
                $price = $productData['price'];
                $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
                $stmt->execute();
            }

            $db->commit();

            require_once 'fpdf/fpdf.php';

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Customer: ' . $fullname, 0, 1);
            $pdf->Cell(0, 10, 'Phone: ' . $phone, 0, 1);
            $pdf->Cell(0, 10, 'Address: ' . $address . ', ' . $city . ', ' . $postal, 0, 1);
            $pdf->Cell(0, 10, 'Payment: ' . $payment . ' (****' . substr($card, -4) . ')', 0, 1);
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(60, 10, 'Product', 1);
            $pdf->Cell(30, 10, 'Qty', 1);
            $pdf->Cell(30, 10, 'Price', 1);
            $pdf->Cell(40, 10, 'Subtotal', 1);
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 12);
            foreach ($cart as $product_id => $quantity) {
                $productData = $product->getProduct($product_id);
                $price = $productData['price'];
                $subtotal = $price * $quantity;

                $pdf->Cell(60, 10, $productData['name'], 1);
                $pdf->Cell(30, 10, $quantity, 1, 0, 'C');
                $pdf->Cell(30, 10, '$' . number_format($price, 2), 1, 0, 'R');
                $pdf->Cell(40, 10, '$' . number_format($subtotal, 2), 1, 0, 'R');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(120, 10, 'Total', 1);
            $pdf->Cell(40, 10, '$' . number_format($total, 2), 1, 0, 'R');
            $pdf->Ln(20);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Thank you for your order!', 0, 1);

            unset($_SESSION['cart']);
            $pdf->Output("I", "invoice.pdf");
            exit();
        } catch (Exception $e) {
            $db->rollback();
            $errors[] = "Order failed: " . $e->getMessage();
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-4" style="max-width: 600px;">
    <h2>Checkout</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="fullname" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="fullname" id="fullname" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" class="form-control" name="phone" id="phone" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Shipping Address</label>
            <textarea class="form-control" name="address" id="address" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="city" class="form-label">City</label>
            <input type="text" class="form-control" name="city" id="city" required>
        </div>
        <div class="mb-3">
            <label for="postal" class="form-label">Postal Code</label>
            <input type="text" class="form-control" name="postal" id="postal" required>
        </div>
        <div class="mb-3">
            <label for="payment" class="form-label">Payment Method</label>
            <select class="form-select" name="payment" id="payment" required>
                <option value="credit">Credit Card</option>
                <option value="debit">Debit Card</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="card" class="form-label">Card Number</label>
            <input type="text" class="form-control" name="card" id="card" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="expiry" class="form-label">Expiry Date</label>
                <input type="text" class="form-control" name="expiry" id="expiry" placeholder="MM/YY" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="cvv" class="form-label">CVV</label>
                <input type="text" class="form-control" name="cvv" id="cvv" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="note" class="form-label">Order Notes (optional)</label>
            <textarea class="form-control" name="note" id="note" rows="2"></textarea>
        </div>
        <div class="mb-3">
            <strong>Total: $<?= number_format($total, 2) ?></strong>
        </div>
        <button type="submit" class="btn btn-success w-100">Place Order</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>