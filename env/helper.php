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


function uploadFileToBucket($file, $key) {
    $result = false;
    try {
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1', 
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
            'region'      => 'us-east-1',
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