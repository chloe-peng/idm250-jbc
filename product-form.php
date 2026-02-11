<?php
require 'db_connect.php';
require './lib/cms.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $id ? get_product($id) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($id) {
        update_product($id, $_POST);
    } else {
        create_product($_POST);
    }

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
</head>
<body>

<h1><?php echo $id ? 'Edit' : 'Create'; ?> Product</h1>

<form method="POST">

    <div class="form-control">
        <label for="sku">SKU</label>
        <input type="text" name="sku" value="<?= $product['sku'] ?? '' ?>" required>
    </div>

    <div class="form-control">
        <label for="description">Description</label>
        <textarea name="description" required><?= $product['description'] ?? '' ?></textarea>
    </div>

    <button type="submit">
        <?= $id ? 'Update' : 'Create' ?>
    </button>

</form>

</body>
</html>