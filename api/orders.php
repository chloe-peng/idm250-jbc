<?php 
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db_connect.php';
<<<<<<< Updated upstream
require_once '../auth.php';
=======
require_once '../lib/auth.php';
require_once '../lib/mpl.php'; 
require_once '../lib/orders.php'; 
>>>>>>> Stashed changes

check_api_key($env);

$method = $_SERVER['REQUEST_METHOD'];

?>