<?php

include 'helper.php';

var_dump($_FILES)['archivo'];

$llega = isUploadedFile('archivo');

echo '<br>Valor de llegada' . $llega . '<br>';
var_dump ($llega);

uploadCloud('archivo');

header('Location: https://informatica.ieszaidinvergeles.org:10058/pia/ReconocimietoFacial/env/uploadFileBucket.php?file=' . $file . '&name=' . $name);
?>