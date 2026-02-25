<?php
require 'db_connect.php';
require './lib/inventory.php';
require './lib/auth.php';

$warehouse_inventory = get_inventory('warehouse');
$inventory_amount = $warehouse_inventory['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Inventory Template</title>
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/sku.css">
    <link rel="stylesheet" href="./css/normalize.css">
</head>

<body>
    <!-- header -->
    <div class="header-bar">
        <h2>JBC Manufacturing CMS</h2>

        <div class="header-bar-right">
            <h5>yourname@gmail.com</h5>
            <h5>Logout</h5>
        </div>
    </div>

    <!-- page wrapper: sidebar + main content -->
    <div class="page-wrapper">
        <!-- sidebar -->
        <div class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item"><h5>Dashboard</h5></li>
                <li class="nav-item"><h5>SKU Management</h5></li>
                <li class="nav-item nav-item--active"><h5>Internal Inventory</h5></li>
                <li class="nav-item"><h5>Warehouse Inventory</h5></li>
                <li class="nav-item"><h5>MPI Records</h5></li>
                <li class="nav-item"><h5>Order Records</h5></li>
            </ul>
        </div>

        <!-- main content -->
        <div class="main-content">
            <h1 class="color-text-primary">Warehouse Inventory</h1>

            <div class="internal-inventory-action-card">
                <h3 class="color-text-primary">
                    Total # of Warehouse Inventory: <?php echo htmlspecialchars($inventory_amount) ?>
                </h3>
            </div>

            <div class="sku-table-container">
                <table class="sku-table">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Unit Number</th>
                            <th>Ficha</th>
                            <th>SKU</th>
                            <th>UOM</th>
                            <th>Description</th>
                            <th>Quantity Shipped</th>
                            <th>Footage Quantity</th>
                            <th>Ship Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($warehouse_inventory['inventory'] as $unit): ?>
                            <tr>
                                <td><?= htmlspecialchars($unit['order_number']) ?></td>
                                <td><?= htmlspecialchars($unit['unit_number']) ?></td>
                                <td><?= htmlspecialchars($unit['ficha']) ?></td>
                                <td><?= htmlspecialchars($unit['sku']) ?></td>
                                <td><?= htmlspecialchars($unit['uom_primary']) ?></td>
                                <td><?= htmlspecialchars($unit['description']) ?></td>
                                <td><?= htmlspecialchars($unit['quantity_shipped']) ?></td>
                                <td><?= htmlspecialchars($unit['footage_quantity']) ?></td>
                                <td><?= htmlspecialchars($unit['ship_date']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
