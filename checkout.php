<?php
session_start();
require_once 'config/Database.php';

// Removed direct Product class usage

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$db = (new Database())->getConnection();
$cart = $_SESSION['cart'] ?? [];
$total = 0;
$productData = [];

foreach ($cart as $id => $qty) {
    $response = file_get_contents("http://localhost/ecommerce-goup3/api/products.php?id=" . $id);
    $p = json_decode($response, true);
    if ($p) {
        $productData[$id] = $p;
        $total += $p['price'] * $qty;
    }
}
$hst = $total * 0.13;
$grand_total = $total + $hst;

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
        $userId = $_SESSION['user']['id'];
        $items = [];
        foreach ($cart as $product_id => $quantity) {
            $items[] = ['product_id' => $product_id, 'quantity' => $quantity];
        }

        $payload = json_encode([
            'user_id' => $userId,
            'fullname' => $fullname,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'postal' => $postal,
            'payment' => $payment,
            'card' => $card,
            'expiry' => $expiry,
            'cvv' => $cvv,
            'note' => $note,
            'total' => $grand_total,
            'items' => $items
        ]);

        $ch = curl_init('http://localhost/ecommerce-goup3/api/orders.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 || $httpCode === 201) {
            unset($_SESSION['cart']);
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
                $productInfo = $productData[$product_id];
                $price = $productInfo['price'];
                $subtotal = $price * $quantity;

                $pdf->Cell(60, 10, $productInfo['name'], 1);
                $pdf->Cell(30, 10, $quantity, 1, 0, 'C');
                $pdf->Cell(30, 10, '$' . number_format($price, 2), 1, 0, 'R');
                $pdf->Cell(40, 10, '$' . number_format($subtotal, 2), 1, 0, 'R');
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(120, 10, 'Subtotal', 1);
            $pdf->Cell(40, 10, '$' . number_format($total, 2), 1, 0, 'R');
            $pdf->Ln();
            $pdf->Cell(120, 10, 'HST (13%)', 1);
            $pdf->Cell(40, 10, '$' . number_format($hst, 2), 1, 0, 'R');
            $pdf->Ln();
            $pdf->Cell(120, 10, 'Total', 1);
            $pdf->Cell(40, 10, '$' . number_format($grand_total, 2), 1, 0, 'R');
            $pdf->Ln(20);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Thank you for your order!', 0, 1);
            $pdf->Cell(0, 10, 'You can return to our website to continue shopping.', 0, 1);

            $pdf->Output("I", "invoice.pdf");
            exit();
        } else {
            $errors[] = "Order failed. Please try again.";
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
            <strong>Subtotal: $<?= number_format($total, 2) ?></strong><br>
            <strong>HST (13%): $<?= number_format($hst, 2) ?></strong><br>
            <strong>Total: $<?= number_format($grand_total, 2) ?></strong>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-success w-100"><i class="bi bi-credit-card-2-back-fill"></i> Place Order</button>
            <a href="index.php" class="btn btn-outline-secondary mt-4"><i class="bi bi-arrow-left"></i> Back to Home</a>
        </div>
    </form>
</div>
<?php include 'includes/footer.php'; ?>