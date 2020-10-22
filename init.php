<?php
$env = parse_ini_file('env.ini');
date_default_timezone_set('Europe/Istanbul');
ini_set('default_charset', 'utf-8');
if (!isset($is_debugging)) $is_debugging = false;
if ($is_debugging) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    header('Content-type: application/json; charset=utf-8');
}
include("requiredFiles/keysStatus.php");
include("requiredFiles/nameOfKey.php");
include("FileMaker.php");

if (count($env) > 0) {
    $fm = new FileMaker($env['db_name'], null, $env['db_username'], $env['db_password']);
} else {
    $fm = new FileMaker('', null, $env['db_username'], $env['db_password']);
}

