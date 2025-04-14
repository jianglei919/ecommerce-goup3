<?php
require_once '../config/Database.php';
require_once '../classes/Product.php';

$db = (new Database())->getConnection();
$product = new Product($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
    if (isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $deleted = $product->deleteProduct($id);

        if ($deleted) {
            // Redirigir después de la eliminación
            header("Location: products_admin.php?message=Product deleted successfully");
            exit;
        } else {
            // Si no se pudo eliminar, mostrar un mensaje de error
            echo "Failed to delete product.";
        }
    } else {
        echo "Product ID is missing.";
    }
} else {
    echo "Invalid request method.";
}
