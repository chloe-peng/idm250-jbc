<?php
require 'db_connect.php';
require './lib/auth.php';
require './lib/mpl.php';

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mpl = $id ? get_mpl($id) : [];

// check if editing a non-draft MPL
if ($id && $mpl && $mpl['status'] !== 'draft') {
    $_SESSION['error'] = 'Only draft MPLs can be edited.';
    header('Location: mpl-records.php');
    exit;
}

// handles form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'reference_number' => $_POST['reference_number'],
        'trailer_number' => $_POST['trailer_number'],
        'expected_arrival' => $_POST['expected_arrival']
    ];
    
    $unit_ids = isset($_POST['unit_ids']) ? $_POST['unit_ids'] : [];
    
    if ($id) {
        $result = update_mpl($id, $data, $unit_ids);
    } else {
        $result = create_mpl($data, $unit_ids);
    }
    
    if ($result) {
        $_SESSION['success'] = $id ? 'MPL updated successfully.' : 'MPL created successfully.';
        header('Location: mpl-records.php');
        exit;
    } else {
        $error = $id ? 'Failed to update MPL.' : 'Failed to create MPL.';
    }
}

// get inventory units for selection
$inventory_query = "SELECT i.unit_number, i.ship_date, i.ficha, i.description, s.sku
                    FROM inventory i 
                    LEFT JOIN cms_products s ON i.ficha = s.ficha
                    WHERE i.location = 'internal'
                    ORDER BY i.ship_date DESC";
$inventory_result = $connection->query($inventory_query);
$available_units = $inventory_result->fetch_all(MYSQLI_ASSOC);

// get currently selected unit IDs if editing
$selected_unit_ids = [];
if ($id) {
    $selected_items = get_mpl_items($id);
    foreach ($selected_items as $item) {
        $selected_unit_ids[] = $item['unit_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Create'; ?> MPL - JBC Manufacturing CMS</title>
    <link rel="stylesheet" href="./css/global.css">
    <link rel="stylesheet" href="./css/sku.css">
    <link rel="stylesheet" href="./css/normalize.css">
<style>
    .mpl-form-header {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .form-field {
        display: flex;
        flex-direction: column;
    }
    
    .form-field label {
        font-weight: 600;
        margin-bottom: 16px;
        color: #323232;
    }
    
    .form-field input {
        height: 66px;
        padding: 22px;
        border: 1px solid #EBEBEB;
        border-radius: 8px;
        font-size: 16px;
    }
    
    .form-field input::placeholder {
        color: #999;
    }
    
    .units-section {
        margin-top: 40px;
        border: 
    }
    
    .units-section h4 {
        margin-bottom: 20px;
        color: #2C77A0;
    }
    
    .units-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .units-table thead {
        background-color: #F5FAFF;
    }
    
    .units-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #323232;
        border-bottom: 1px solid #EBEBEB;
    }
    
    .units-table td {
        padding: 16px;
        border-bottom: 1px solid #EBEBEB;
    }
    
    .units-table tbody tr:hover {
        background-color: #FAFAFA;
    }
    
    .checkbox-cell {
        width: 60px;
    }
    
    .checkbox-cell input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    
    .select-all-row {
        background-color: #F5FAFF;
        font-weight: 600;
    }
    
    .form-buttons {
        display: flex;
        gap: 12px;
        margin-top: 12px;
        padding-top: 20px;
    }
    
    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary {
        background-color: #CFEFFF;
        color: #323232;
        border: 1px solid #EBEBEB;
    }
    
    .btn-primary:hover {
        background-color: #b8e5ff;
    }
    
    .btn-cancel {
        background-color: #FF6B6B;
        color: #ffffff;
    }
    
    .btn-cancel:hover {
        background-color: #ff5252;
    }
    
    .back-link {
        position: absolute;
        right: 48px;
        top: 48px;
        background-color: #CFEFFF;
        color: #323232;
        padding: 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 16px;
        font-weight: 500;
    }
    
    .back-link:hover {
        background-color: #b8e5ff;
    }
</style>
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
                <li class="nav-item nav-item--active">
                    <a style="text-decoration: none; color: inherit;" href="mpl-records.php"><h5>MPL Records</h5></a>
                </li>
                <li class="nav-item">
                    <a style="text-decoration: none; color: inherit;" href="order-records.php"><h5>Order Records</h5></a>
                </li>
            </ul>
        </div>

        <!-- main content -->
        <div class="main-content" style="position: relative;">
            <a href="mpl-records.php" class="back-link">Back to List</a>
            
            <h1 class="color-text-primary" style="margin-bottom: 30px;">MPL</h1>

            <?php if (isset($error)): ?>
                <div style="background-color: #ffebee; color: #c62828; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <h4 style="margin-bottom: 20px; color: #2C77A0;">
                    <?php echo $id ? 'Edit MPL' : 'Create an MPL'; ?>
                </h4>

                <div class="mpl-form-header">
                    <div class="form-field">
                        <label for="reference_number">Reference Number</label>
                        <input 
                            type="text" 
                            id="reference_number" 
                            name="reference_number" 
                            placeholder="Fill reference number"
                            value="<?= htmlspecialchars($mpl['reference_number'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="form-field">
                        <label for="trailer_number">Trailer Number</label>
                        <input 
                            type="text" 
                            id="trailer_number" 
                            name="trailer_number" 
                            placeholder="Fill trailer number"
                            value="<?= htmlspecialchars($mpl['trailer_number'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="form-field">
                        <label for="expected_arrival">Expected Arrival</label>
                        <input 
                            type="date" 
                            id="expected_arrival" 
                            name="expected_arrival" 
                            placeholder="Fill expected arrival"
                            value="<?= htmlspecialchars($mpl['expected_arrival'] ?? '') ?>"
                            required
                        >
                    </div>
                </div>

                <div class="units-section">
                    <h4>Select Units to Transfer</h4>

                    <table class="units-table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell"></th>
                                <th>Unit ID</th>
                                <th>SKU</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- select all row -->
                            <tr class="select-all-row">
                                <td class="checkbox-cell">
                                    <input type="checkbox" id="select-all" onclick="toggleAll(this)">
                                </td>
                                <td colspan="3">Select All</td>
                            </tr>

                            <!-- inventory units -->
                            <?php foreach ($available_units as $unit): ?>
                            <tr>
                                <td class="checkbox-cell">
                                    <input 
                                        type="checkbox" 
                                        name="unit_ids[]" 
                                        value="<?= htmlspecialchars($unit['unit_number']) ?>"
                                        class="unit-checkbox"
                                        <?= in_array($unit['unit_number'], $selected_unit_ids) ? 'checked' : '' ?>
                                    >
                                </td>
                                <td><?= htmlspecialchars($unit['unit_number']) ?></td>
                                <td><?= htmlspecialchars($unit['sku']) ?></td>
                                <td><?= htmlspecialchars($unit['description']) ?></td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($available_units)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #999;">
                                    No inventory units available
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">
                        <?= $id ? 'Update MPL' : 'Create MPL' ?>
                    </button>
                    <a href="mpl-records.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // toggle all checkboxes
        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('.unit-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
        }

        // update "Select All" checkbox based on individual checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const unitCheckboxes = document.querySelectorAll('.unit-checkbox');
            
            unitCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(unitCheckboxes).every(cb => cb.checked);
                    selectAll.checked = allChecked;
                });
            });
        });
    </script>
</body>
</html>
<?php
$connection->close();
?>