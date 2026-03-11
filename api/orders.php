<?php 
ob_start();

define('API_REQUEST', true);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, x-api-key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../db_connect.php';
require_once '../lib/auth.php';
require_once '../lib/mpl.php'; 

ob_end_clean();

check_api_key($env);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);

    $action       = $data['action']       ?? '';
    $order_number = $data['order_number'] ?? '';
    $shipped_at   = $data['shipped_at']   ?? '';
 
    if ($action !== 'ship') {
        http_response_code(400);
        echo json_encode([
            'error'   => 'Bad Request',
            'details' => "Unknown action: $action"
        ]);
        exit;
    }

    // Validate shipped_at is a real date
    // if (!strtotime($shipped_at)) {
    //     http_response_code(400);
    //     echo json_encode([
    //         'error'   => 'Bad Request',
    //         'details' => "Invalid shipped_at date: $shipped_at"
    //     ]);
    //     exit;
    // }

    $order = get_order($order_number);

    if (!$order) {
        http_response_code(404);
        echo json_encode([
            'error'   => 'Not Found',
            'details' => "Order not found: $order_number"
        ]);
        exit;
    }

    // checking if order is already confirmed
    if ($order['status'] === 'confirmed') {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'details' => 'Order already confirmed'
        ]);
        exit;
    }

    $order_id = $order['id'];

    // get all the units tied to this order
    $unit_ids = array_column(get_order_items($order_number), 'unit_id');
    if (empty($unit_ids)) {
        http_response_code(422);
        echo json_encode([
            'error'   => 'Unprocessable',
            'details' => 'No units found for this order'
        ]);
        exit;
    }
    // update the order status to confirmed
    $updated = update_order_status($order_id, 'confirmed', $shipped_at);

    //deleting the order items from inventory once it's shipped
    foreach ($unit_ids as $unit_id) {
        $stmt = $connection->prepare("DELETE FROM inventory WHERE unit_number = ?");
        $stmt->bind_param("s", $unit_id);
        $stmt->execute();
    }
    $units_deleted = count($unit_ids);

    http_response_code(200);
    echo json_encode([
        'success'       => true,
        'order_number'  => $order_number,
        'shipped_at'    => $shipped_at,
        'confirmed_at'  => date('Y-m-d H:i:s'),
        'units_deleted' => $units_deleted
    ]);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

?>