<?php 
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db_connect.php';
require_once '../auth.php';

check_api_key($env);

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_SERVER['PATH_INFO']) ? intval(ltrim($_server['path_info'], '/')) : 0;


if ($method === 'GET') :
	$product_list = $get_products();
    if ($product_list) {
        echo json_encode(['sucess' => true, 'Product List' => $product_list]);
    }
    else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
endif;