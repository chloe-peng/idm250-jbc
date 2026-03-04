<?php

// get all orders
function get_all_orders() {
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM orders ORDER BY created_at DESC");
    if($stmt->execute()) {
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        return $orders;
    } else {
        return false;   
    }
}

// get single order
function get_order($id) {
    global $connection;

    $stmt = $connection->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        return $order;
    } else {
        return null;
    }
}

// get items with SKU details (unit ID, SKU code, description) the query joins three tables
function get_order_items($order_id) {
    global $connection;

    $stmt = $connection->prepare(
        "SELECT oi.*, i.ficha, s.*
        FROM order_items oi
        JOIN inventory i ON oi.unit_id = i.unit_number
        LEFT JOIN cms_products s ON i.ficha = s.ficha
        WHERE oi.order_id = ?"
    );
    $stmt->bind_param('i', $order_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        return $items;
    } else {
        return [];
    }
}

// this counts how many inventory units are in a specific order
function get_order_item_count($order_id) {
    global $connection;
    
    $stmt = $connection->prepare("SELECT COUNT(*) as count FROM order_items WHERE order_id = ?");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'];
}

// this counts how many orders exist in total
function get_order_count() {
    global $connection;
    
    $result = $connection->query("SELECT COUNT(*) as count FROM orders");
    $row = $result->fetch_assoc();
    
    return $row['count'];
}

// creates the header and inserts items
function create_order($data, $unit_ids) {
    global $connection;

    $stmt = $connection->prepare(
        "INSERT INTO orders (order_number, ship_to_company, ship_to_street, ship_to_city, ship_to_state, ship_to_zip, status)
         VALUES (?, ?, ?, ?, ?, ?, 'draft')"
    );
    
    $stmt->bind_param('ssssss', 
        $data['order_number'], 
        $data['ship_to_company'], 
        $data['ship_to_street'],
        $data['ship_to_city'],
        $data['ship_to_state'],
        $data['ship_to_zip']
    );
    
    if (!$stmt->execute()) {
        return false;
    }
    
    $order_id = $connection->insert_id;
    
    if (!empty($unit_ids)) {
        $stmt = $connection->prepare("INSERT INTO order_items (order_id, unit_id) VALUES (?, ?)");
        
        foreach ($unit_ids as $unit_id) {
            $stmt->bind_param('ii', $order_id, $unit_id);
            $stmt->execute();
        }
    }
    
    return $order_id;
}

// only allows updating draft orders
function update_order($id, $data, $unit_ids) {
    global $connection;

    $check = $connection->prepare("SELECT id FROM orders WHERE id = ? AND status = 'draft'");
    $check->bind_param('i', $id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $stmt = $connection->prepare(
        "UPDATE orders 
         SET order_number = ?, ship_to_company = ?, ship_to_street = ?, ship_to_city = ?, ship_to_state = ?, ship_to_zip = ?
         WHERE id = ? LIMIT 1"
    );
    
    $stmt->bind_param('ssssssi', 
        $data['order_number'], 
        $data['ship_to_company'], 
        $data['ship_to_street'],
        $data['ship_to_city'],
        $data['ship_to_state'],
        $data['ship_to_zip'],
        $id
    );
    
    if (!$stmt->execute()) {
        return false;
    }
    
    $stmt = $connection->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    if (!empty($unit_ids)) {
        $stmt = $connection->prepare("INSERT INTO order_items (order_id, unit_id) VALUES (?, ?)");
        
        foreach ($unit_ids as $unit_id) {
            $stmt->bind_param('ii', $id, $unit_id);
            $stmt->execute();
        }
    }
    
    return true;
}

// only allows deleting draft orders
function delete_order($id) {
    global $connection;
    
    $check = $connection->prepare("SELECT id FROM orders WHERE id = ? AND status = 'draft'");
    $check->bind_param('i', $id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $stmt = $connection->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $stmt = $connection->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    return $stmt->execute();
}

// this changes the status (draft, sent, confirmed) with optional shipped_at date
function update_order_status($id, $status, $shipped_at = null) {
    global $connection;
    
    // validates status
    $allowed_statuses = ['draft', 'sent', 'confirmed'];
    if (!in_array($status, $allowed_statuses)) {
        return false;
    }
    
    if ($shipped_at) {
        $stmt = $connection->prepare("UPDATE orders SET status = ?, shipped_at = ? WHERE id = ?");
        $stmt->bind_param('ssi', $status, $shipped_at, $id);
    } else {
        $stmt = $connection->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
    }
    
    return $stmt->execute();
}

?>