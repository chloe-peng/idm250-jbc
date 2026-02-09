<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db_connect.php';
check_api_key($env);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    $query = "SELECT p.id, p.name, p.base_price FROM products p";

    if (isset($_GET['category'])) {
        $category = $connection->real_escape_string($_GET['category']);

        $query .= "
            JOIN product_categories pc ON p.id = pc.product_id
            JOIN categories c ON pc.category_id = c.id
            WHERE c.name = '$category'
        ";
    }

    $result = $connection->query($query);
    $products = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $products
    ]);

} elseif ($method === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name']) || !isset($data['base_price'])) {
        echo json_encode([
            'error' => 'Bad Request',
            'details' => 'Missing required fields'
        ]);
        exit;
    }

    $name  = $connection->real_escape_string($data['name']);
    $price = floatval($data['base_price']);

    $query = "INSERT INTO products (name, base_price) VALUES ('$name', $price)";
    $result = $connection->query($query);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $connection->error
        ]);
    }
}
