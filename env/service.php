<?php

require 'vendor/autoload.php';

use Aws\Rekognition\RekognitionClient;
use Dotenv\Dotenv;

ini_set('display_errors',1);
ini_set('display_status_errors',1);
error_reporting(E_ALL);

include 'helper.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$newCoords = [];
if(isset($_GET['name'])){
    $name = $_GET['name'];
    $coords = detectFaces($name);
    $newCoords = getFaceValues($coords);
}
echo "El newCoords";

header('Content-Type: application/json; charset=utf-8');
echo json_encode($newCoords);