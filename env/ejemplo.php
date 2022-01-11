<?php
//PARA VER LOS ERRORES EN TIEMPO DE EJECUCCION
ini_set('display_errors',1);
ini_set('display_status_errors',1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;
use Aws\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\Rekognition\RekognitionClient;
//Hay que importar todas las clases


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


uploadFileToBucket('foto.jpg','nombreQueSeGuardaEnElBucket2');

$coords = detectFaces('nombreQueSeGuardaEnElBucket2');
$newCoords = getFaceValues($coords);
//echo '<pre>' . var_export($coords,true) . '</pre>';
//para obtener los datos del objeto que devuelve es decir, las caras. cada una es un array dentro de uno mayor. 
//Asi tenemos tanto las posiciones de las caras como la edad para despues taparlas si son menores.
//La cara esta en BoundingBox
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
        //echo $value['BoundingBox']['Width']. ' - ';
        //echo $value['BoundingBox']['Height']. ' - ';
        //echo $value['BoundingBox']['Left']. ' - ';
        //echo $value['BoundingBox']['Top']. ' - ';
        //echo $value['AgeRange']['Low']. ' - ';
        //echo $value['AgeRange']['High']. ' - ';
        //echo $value['Gender']['Value'];
        //echo '<br>';
        $faces[] = $face; //Asi se mete todo en la ultima posicion en un array en php
    }
}

function uploadFileToBucket($file, $key) {
    $result = false;
    try {
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1', //depends on the value of your region
            'credentials' => [
                'key'    => $_ENV['aws_access_key_id'], //aqui va lo que va en el fichero .env
                'secret' => $_ENV['aws_secret_access_key'],
                'token'  => $_ENV['aws_session_token']
            ]
        ]);
        $uploader = new MultipartUploader($s3, $file, [
            'bucket' => $_ENV['bucket'],
            'key'    => $key,
        ]);
        $result = $uploader->upload();
        echo "Funciona";
    } catch(MultipartUploadException $e) {
        //to see the message: $e->getMessage()
    } catch (S3Exception $e) {
        //to see the message: $e->getMessage()
    }
    return $result; //DEVUELVE TRUE SI FUNCIONA
}
function detectFaces($name) {
    try{
        $rekognition = new RekognitionClient([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'credentials' => [
                'key'    => $_ENV['aws_access_key_id'], //aqui va lo que va en el fichero .env
                'secret' => $_ENV['aws_secret_access_key'],
                'token'  => $_ENV['aws_session_token']
            ]
        ]);
        $result = $rekognition->DetectFaces(array(
            'Image' => [
                'S3Object' => [
                    'Bucket' =>  $_ENV['bucket'],
                    'Name' => $name, //nombre que tiene el objeto en el bucket
                ],
            ],
           'Attributes' => ['ALL']
           )
        );
    } catch(Exception $e) {
        echo "no lo reconoce";
        $result = false;
    }
    return $result; //SI FUNCIONA ES UN ARRAY DE ARRAYS
}