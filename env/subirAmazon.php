<?php

ini_set('display_errors',1);
ini_set('display_status_errors',1);
error_reporting(E_ALL);


include 'helper.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

uploadFileToBucket('foto.jpg','nombreQueSeGuardaEnElBucket3');
$coords = detectFaces('nombreQueSeGuardaEnElBucket3');
$newCoords = getFaceValues($coords);
