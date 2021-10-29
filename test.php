<?php

require_once(dirname(__DIR__).DIRECTORY_SEPARATOR.'CpJsonToImg.php');

$cpJsonToImg = new CpJsonToImg(
    __DIR__.DIRECTORY_SEPARATOR.'dr',
    __DIR__.DIRECTORY_SEPARATOR.'out'
);

$cpJsonToImg->convert();
// print_r($cpJsonToImg);