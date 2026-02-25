<?php 
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db_connect.php';
require_once '../auth.php';

check_api_key($env);

$method = $_SERVER['REQUEST_METHOD'];

?>