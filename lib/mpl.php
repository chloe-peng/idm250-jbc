<?php

// get all MPLS
function get_all_mpls() {
    global $connection;
    
    $stmt = $connection->prepare("SELECT * FROM mpls ORDER BY created_at DESC");
    if($stmt->execute()) {
        $result = $stmt->get_result();
        $mpls = $result->fetch_all(MYSQLI_ASSOC);
        return $mpls;
    } else {
        return false;   
    }
}

// get single MPL
function get_mpl($id) {
    global $connection;

    $stmt = $connection->prepare("SELECT * FROM mpls WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $mpl = $result->fetch_assoc();
        return $mpl;
    } else {
        return null;
    }
}

// get items with SKU details (unit ID, SKU code, description) the query joins three tables
function get_mpl_items($mpl_id) {
    global $connection;

    $stmt = $connection->prepare(
        "SELECT mi.*, i.sku_id, s.sku, s.description
         FROM mpl_items mi
         JOIN inventory i ON mi.unit_id = i.unit_id
         JOIN cms_products s ON i.sku_id = s.id
         WHERE mi.id = ?"
    );
    $stmt->bind_param('i', $mpl_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        return $items;
    } else {
        return [];
    }
}

// this counts how many inventory units are in a specific MPL
function get_mpl_item_count($mpl_id) {
    global $connection;
    
    $stmt = $connection->prepare("SELECT COUNT(*) as count FROM mpl_items WHERE id = ?");
    $stmt->bind_param('i', $mpl_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'];
}

// this counts how many MPLs exist in total
function get_mpl_count() {
    global $connection;
    
    $result = $connection->query("SELECT COUNT(*) as count FROM mpls");
    $row = $result->fetch_assoc();
    
    return $row['count'];
}

// creates the header and insert items
function create_mpl($data, $unit_ids) {
    global $connection;

    $stmt = $connection->prepare(
        "INSERT INTO mpls (reference_number, trailer_number, expected_arrival, status)
         VALUES (?, ?, ?, 'draft')"
    );
    
    $stmt->bind_param('sss', 
        $data['reference_number'], 
        $data['trailer_number'], 
        $data['expected_arrival']
    );
    
    if (!$stmt->execute()) {
        return false;
    }
    
    $mpl_id = $connection->insert_id;
    
    if (!empty($unit_ids)) {
        $stmt = $connection->prepare("INSERT INTO mpl_items (id, unit_id) VALUES (?, ?)");
        
        foreach ($unit_ids as $unit_id) {
            $stmt->bind_param('is', $mpl_id, $unit_id);
            $stmt->execute();
        }
    }
    
    return $mpl_id;
}

// only allows to update draft MPL
function update_mpl($id, $data, $unit_ids) {
    global $connection;

    $check = $connection->prepare("SELECT id FROM mpls WHERE id = ? AND status = 'draft'");
    $check->bind_param('i', $id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $stmt = $connection->prepare(
        "UPDATE mpls 
         SET reference_number = ?, trailer_number = ?, expected_arrival = ? 
         WHERE id = ? LIMIT 1"
    );
    
    $stmt->bind_param('sssi', 
        $data['reference_number'], 
        $data['trailer_number'], 
        $data['expected_arrival'],
        $id
    );
    
    if (!$stmt->execute()) {
        return false;
    }
    
    $stmt = $connection->prepare("DELETE FROM mpl_items WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    if (!empty($unit_ids)) {
        $stmt = $connection->prepare("INSERT INTO mpl_items (mpl_id, unit_id) VALUES (?, ?)");
        
        foreach ($unit_ids as $unit_id) {
            $stmt->bind_param('is', $mpl_id, $unit_id);
            $stmt->execute();
        }
    }
    
    return true;
}

// only allows to delete draft MPL
function delete_mpl($id) {
    global $connection;
    
    $check = $connection->prepare("SELECT id FROM mpls WHERE id = ? AND status = 'draft'");
    $check->bind_param('i', $id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $stmt = $connection->prepare("DELETE FROM mpl_items WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $stmt = $connection->prepare("DELETE FROM mpls WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    return $stmt->execute();
}

// this changes the status (draft, sent, confirmed)
function update_mpl_status($id, $status) {
    global $connection;
    
    // validates status
    $allowed_statuses = ['draft', 'sent', 'confirmed'];
    if (!in_array($status, $allowed_statuses)) {
        return false;
    }
    
    $stmt = $connection->prepare("UPDATE mpls SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    
    return $stmt->execute();
}
?>