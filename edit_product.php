<?php
require 'db_connect.php';

if (!isset($_GET['id']) || $_GET['id'] > 0) {
    die("Invalid product ID");
}

$id = (int)$_GET['id'];

// 2️⃣ If form submitted → UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $required = ['name', 'description', 'sku', 'base_price'];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Missing required field: $field");
        }
    }

    $data = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'sku' => $_POST['sku'],
        'base_price' => $_POST['base_price']
    ];

    if (update_product($id, $data)) {
        $success = "Product updated successfully!";
    } else {
        $error = "Update failed.";
    }
}

// 3️⃣ Fetch product (for page load OR after update)
$stmt = $connection->prepare(
    "SELECT name, description, sku, base_price FROM products WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found");
}
?>