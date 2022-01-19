<?php

include 'helper.php';
require 'vendor/autoload.php';

use Aws\Rekognition\RekognitionClient;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$newCoords = [];
if(isset($_GET['name'])) {
    $name = $_GET['name'];
    $coords = detectFaces($name);
    $newCoords = getFaceValues($coords);
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($newCoords);

