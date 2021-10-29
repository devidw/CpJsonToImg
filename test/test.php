<?php

require_once(dirname(__DIR__).'/CpJsonToImg.php');

$cpJsonToImg = new CpJsonToImg(
    __DIR__.'/dr',
    __DIR__.'/dr/out'
);

$cpJsonToImg->convert();
// print_r($cpJsonToImg);