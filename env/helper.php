<?php

ini_set('display_errors',1);
ini_set('display_status_errors',1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;
use Aws\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\Rekognition\RekognitionClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function uploadCloud($name){
    $result = uploadFile($name);
    if($result){
        echo 'todo bien';
    } else {
        header('location' . $server['HTTP_REFERER']);
        echo "todo mal";
    }
}

function isUploadedFile($name) {
    return isset($_FILES[$name]);
}

function isValidUploadedFile($file) {
    $result = true;
    $error = $file['error'];
    $name = $file['name'];
    $size = $file['size'];
    $tmp_name = $file['tmp_name'];
    $type = $file['type'];
    if($error != 0 || $name == '' || $size == 0 || strpos($type, 'image/') === false || !is_uploaded_file($tmp_name)) {
        $result = false;
    } else {
        $mcType = mime_content_type($tmp_name);
        if(strpos($mcType, 'image/') === false) {
            $result = false;
        }
    }
    return $result;
}

function uploadFile($paramName) {
    $result = false;
    if(!isUploadedFile($paramName)) {
        return false;
    }
    $file = $_FILES[$paramName];
    if(!isValidUploadedFile($file)) {
        return false;
    }
    return moveFile($file);
}

function moveFile($file) {
    $target = 'upload';
    $uniqueName = uniqid('image_');
    $name = $file['name'];
    $extension = pathinfo($name, PATHINFO_EXTENSION);
    $tmp_name = $file['tmp_name'];
    $uploadedFile = $target . '/' . $uniqueName . '.' . $extension;
    if(move_uploaded_file($tmp_name, $uploadedFile)) {
        return [$uploadedFile, $uniqueName . '.' . $extension, $uniqueName, $extension,];
    }
    echo 'ha fallado';
    return false;
}


function uploadFileToBucket($file, $key) {
    $result = false;
    try {
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $_ENV['region'], 
            'credentials' => [
                'key'    => $_ENV['aws_access_key_id'],
                'secret' => $_ENV['aws_secret_access_key'],
                'token'  => $_ENV['aws_session_token']
            ]
        ]);
        $uploader = new MultipartUploader($s3, $file, [
            'bucket' => $_ENV['bucket'],
            'key'    => $key,
        ]);
        $result = $uploader->upload();
    } catch(MultipartUploadException $e) {
        //to see the message: $e->getMessage()
    } catch (S3Exception $e) {
        //to see the message: $e->getMessage()
    }
    echo $result;
    return $result;
}


function detectFaces($name) {
    try{
        $rekognition = new RekognitionClient([
            'version'     => 'latest',
            'region'      => $_ENV['region'],
            'credentials' => [
                'key'    => $_ENV['aws_access_key_id'],
                'secret' => $_ENV['aws_secret_access_key'],
                'token'  => $_ENV['aws_session_token']
            ]
        ]);
        $result = $rekognition->DetectFaces(array(
            'Image' => [
                'S3Object' => [
                    'Bucket' =>  $_ENV['bucket'],
                    'Name' => $name,
                ],
            ],
           'Attributes' => ['ALL']
           )
        );
    } catch(Exception $e) {
        echo "no lo reconoce";
        $result = false;
    }
    return $result;
}

function getFaceValues($data){
    $faces = [];
    foreach($data['FaceDetails'] as $index => $value){
        $face = [];
        $face['Width'] = $value['BoundingBox']['Width'];
        $face['Height'] = $value['BoundingBox']['Height'];
        $face['Left'] = $value['BoundingBox']['Left'];
        $face['Top'] = $value['BoundingBox']['Top'];
        $face['Low'] = $value['AgeRange']['Low'];
        $face['High'] = $value['AgeRange']['High'];
        $face['Gender'] = $value['Gender']['Value'];

        $faces[] = $face;
    }
    echo count($faces);
}

function getUnderAgeFaces($faces) {
    $result = [];
    foreach($faces['FaceDetails'] as $face) {
        if($face['AgeRange']['Low'] < 18) {
            $row = [];
            $row['left'] = $face['BoundingBox']['Left'];
            $row['top'] = $face['BoundingBox']['Top'];
            $row['width'] = $face['BoundingBox']['Width'];
            $row['height'] = $face['BoundingBox']['Height'];
            $result[] = $row;
        }
    }
    return $result;
}
?>