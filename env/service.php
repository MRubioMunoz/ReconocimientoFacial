<?php

ini_set('display_errors',1);
ini_set('display_status_errors',1);
error_reporting(E_ALL);


include 'helper.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$file = 'foto.jpg';
$name = 'foto.jpg';

uploadFileToBucket('foto.jpg','nombreQueSeGuardaEnElBucket5');
header('Location: https://informatica.ieszaidinvergeles.org:10058/pia/ReconocimietoFacial/env/ejemplo.php?file=' . $file . '&name=' . $name);
exit;

//ESTO ES EL SERVICE SOLO, LO DE ANTES LO TENGO QUE SACAR A OTRO FICHERO
$newCoords = [];
if(isset($_GET['name'])){
    $name = $_GET['name'];
    $coords = detectFaces($name);
    $newCoords = getFaceValues($coords);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($newCoords);