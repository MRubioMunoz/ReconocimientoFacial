<?php
if(isset($_GET['file']) && isset($_GET['name'])){
    $file = $_GET['file'];
    $name = $_GET['name'];

} else{
    header('Location: https://informatica.ieszaidinvergeles.org:10058/pia/ReconocimietoFacial/env');
}


?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
    </head>
    <body>
        <img src="<?= $file ?>" alt="imagen subida">
        <script src="service.js"></script>
    </body>
</html>