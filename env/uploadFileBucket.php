<?php

ini_set('display_errors',1);
ini_set('display_status_errors',1);
error_reporting(E_ALL);

include 'helper.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$file = 'foto.jpg';
$name = 'foto.jpg';

uploadFileToBucket('foto.jpg','nombreQueSeGuardaEnElBucket7');
header('Location: https://informatica.ieszaidinvergeles.org:10058/pia/ReconocimietoFacial/env/showFile.php?file=' . $file . '&name=' . $name);
exit;