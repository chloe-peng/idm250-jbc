<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1><?php echo isset($_GET['id']) ? 'Edit' : 'Create'; ?> Products</h1>
    <?php
    require '../lib/cms.php';
        $id = isset($_GET['id'] ? intval($_GET['id']) : 0)
        $product = $id ? get_product('id') : [];

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if($id) { 
            update_product($id, $_POST);
        }
        else { 
            create_product($_POST);
        }
        header('Location: index.php');
        exit;
    }
    ?>

    <form method='POST'>
        <div class='form-control'>
            <label for='sku'>SKU</lable>
            <input type='text' name='sku' value='<?php $product['sku'] ?? '' ?>' required>

    </div>
    <div class='form-control'>
            <label for='descrip'>Description</lable>
            <textarea name='description' value='<?php $product['description'] ?? '' ?>' required>
            
    </div>
    <button type='submit'></button>

</body>
</html>