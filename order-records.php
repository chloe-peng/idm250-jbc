<?php
require 'db_connect.php';
require './lib/auth.php';
//require './lib/order.php';

require_login();

// get all orders
//$orders = get_all_orders();
//$order_count = get_order_count();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Records</title>
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/sku.css">
    <link rel="stylesheet" href="./css/normalize.css">
</head>

<body>
    <!-- header -->
    <div class="header-bar">
        <h2>JBC Manufacturing CMS</h2>

        <div class="header-bar-right">
            <h5><?php echo htmlspecialchars($_SESSION['user_email']); ?></h5>
            <a href="logout.php" style="text-decoration: none; color: inherit;"><h5>Logout</h5></a>
        </div>
    </div>

    <!-- page wrapper: sidebar + main content -->
    <div class="page-wrapper">
    <!-- sidebar -->
        <div class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a style="text-decoration: none; color: inherit;" href="sku-management.php"><h5>SKU Management</h5></a>
                </li>
                <li class="nav-item">
                    <a style="text-decoration: none; color: inherit;" href="internal-inventory.php"><h5>Internal Inventory</h5></a>
                </li>
                <li class="nav-item">
                    <a style="text-decoration: none; color: inherit;" href="warehouse-inventory.php"><h5>Warehouse Inventory</h5></a>
                </li>
                <li class="nav-item">
                    <a style="text-decoration: none; color: inherit;" href="mpl-records.php"><h5>MPL Records</h5></a>
                </li>
                <li class="nav-item nav-item--active">
                    <a style="text-decoration: none; color: inherit;" href="order-records.php"><h5>Order Records</h5></a>
                </li>
            </ul>
        </div>

    <!-- main content -->
    <div class="main-content">
        <h1 class="color-text-primary">Order Records</h1>

        <div class="sku-table-container">
        <table class="sku-table">
            <thead>
                <tr>
                <th>Unit Number</th>
                <th>Shipped Address</th>
                <th>Status</th>
                <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                        <?php
                        if ($orders && count($orders) > 0) {
                            foreach ($orders as $order) {
                                $order_id = $order['id'];
                                $shipped_address = $order['address'];
                                $status = $order['status'];
                                $item_count = get_order_record_item_count($order_id);
                                
                                // status badge class
                                $status_class = 'status-' . $status;
                                $status_display = ucfirst($status);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reference); ?></td>
                            <td><?php echo htmlspecialchars($trailer); ?></td>
                            <td><?php echo htmlspecialchars($arrival); ?></td>
                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_display; ?></span></td>
                            <td>
                                <?php if ($status === 'draft'): ?>
                                    <a href="order-form.php?id=<?= $mpl_id ?>">Edit</a>
                                    <a href="delete-order.php?id=<?= $mpl_id ?>" onclick="return confirm('Are you sure you want to delete this MPL?');">Delete</a>
                                <?php elseif ($status === 'sent'): ?>
                                    <a href="mpl-confirm.php?id=<?= $mpl_id ?>&action=confirm">Confirm</a>
                                <?php else: 
                                    // confirmed ?>
                                    <a href="mpl-view.php?id=<?= $mpl_id ?>">View</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align: center;'>No Order Records found.</td></tr>";
                        }
                        ?>
                    </tbody>
        </table>
    </div>
    </div>
</div>
</body>
</html>

<?php
$connection->close();
?>