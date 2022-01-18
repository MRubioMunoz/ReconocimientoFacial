<?php

include 'helper.php';

var_dump($_FILES)['archivo'];

$llega = isUploadedFile('archivo');

echo '<br>Valor de llegada' . $llega . '<br>';
var_dump ($llega);

uploadCloud('archivo');