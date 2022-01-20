<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$image1 = imagecreatefromjpeg($_POST['file']); 
$image2 = imagecreatefromjpeg($_POST['file']);


for($i = 0; $i<50; $i++){
    imagefilter($image1, IMG_FILTER_GAUSSIAN_BLUR);
}

foreach($_POST['x'] as $index => $x){
  $y = $_POST['y'][$index];
  $w = $_POST['w'][$index];
  $h = $_POST['h'][$index];
  echo $x . $y . $w . $h .'<br>';
  imagecopy($image2, $image1, $x, $y, $x, $y, $w, $h);
}


imagejpeg($image2, 'blur' . $_POST['file']);
imagejpeg($image2, 'blur' . $_POST['file']);
imagedestroy($image1);
imagedestroy($image2);


header('Location: blur' . $_POST['file']);