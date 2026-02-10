<?php 
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db_connect.php';
require_once '../auth.php';

check_api_key($env);

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_SERVER['PATH_INFO']) ? intval(ltrim($_server['path_info'], '/')) : 0;


if ($method === 'GET') :
<<<<<<< Updated upstream
	if ($id > 0) :
	$product = $get_product($id);
		if ($product) {
			echo json_encode(['sucess' => true, 'product' => $product]);
		}
		else {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
		}
endif;
	
	// add new information
	elseif ($method === 'POST') :
		$data = json_decode(file_get_contents('php://input'), true):
		$data_keys = ['name', 'description', 'sku', 'base price'];
		// this will get the data sent to us
		
		foreach ($data_keys as $key) {
			if(!isset($key) {
				http_response_code: 400;
				echo json_encode(['error']);
			}
		}
		$new_id = create_product($data);
		if($new_id) {
			http_response_code(200);
			echo json_encode(['success' ==> true, 'id' ==> $new_id]);
		
			else {
				http_repsonse_code(500);
				echo json_encode(['error' ==> 'Server Error']);
			}
		}
	
	elseif($method === 'PUT') {
	$data = json_decode(file_get_contents('php://input'), true):
		$data_keys = ['name', 'description', 'sku', 'base price'];
		// this will get the data sent to us
		
		foreach ($data_keys as $key) {
			if(!isset($key) {
				http_response_code: 400;
				echo json_encode(['error' => 'bad reequest', 'details']);
			}
		}
		
		if(update_product($id, $data) {
		echo json_encode(['success']);
		else {
		
	}
		
endif;
?>
=======
	$product_list = $get_products();
    if ($product_list) {
        echo json_encode(['sucess' => true, 'Product List' => $product_list]);
    }
    else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
endif;
>>>>>>> Stashed changes
