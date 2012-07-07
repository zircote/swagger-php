#!/usr/bin/env php
<?php
print_r($argv);
$shortopts  = null;
$shortopts .= "f:";  // Required value
$shortopts .= "v::"; // Optional value
$shortopts .= "abc"; // These options do not accept values

$longopts  = array(
    "filepath:",     // Required value
    "optional::",    // Optional value
    "option",        // No value
    "opt",           // No value
);
require_once dirname(__DIR__) . '/vendor/autoload.php';

 $swagger = \Swagger\Swagger::discover($projectPath);

 print_r($swagger);
