<?php

//para visualizar cualquier error en tiempo de ejecución
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'helper.php';
require 'vendor/autoload.php';

//paquetes -> espacios de nombres
use Aws\Exception\MultipartUploadException;
use Aws\Exception\S3Exception;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$file = $_COOKIE["TestCookie"];
$name = $_COOKIE["TestCookie"];
uploadFileToBucket($file, $name);

header('Location: https://informatica.ieszaidinvergeles.org:10058/pia/ReconocimietoFacial/env/showFile.php?file=' . $file . '&name=' . $name);
exit;