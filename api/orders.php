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

$env = require dirname(dirname(__DIR__)) . '/.env.php';
check_api_key($env);

$method = $_SERVER['REQUEST_METHOD'];

if($method === 'POST') {

    
}


?>