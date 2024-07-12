<?php
$location = realpath(dirname(__FILE__));
require_once $location . '/function.php';
$filePath = "{$location}/temporary/input.wav";
$return = wavFileToIntArrays($filePath);
//$leftAudio = implode(PHP_EOL, $return[0]);
//file_put_contents("{$location}/temporary/output-left.txt", $leftAudio);
//$rightAudio = implode(PHP_EOL, $return[1]);
//file_put_contents("{$location}/temporary/output-right.txt", $rightAudio);
var_dump($return);