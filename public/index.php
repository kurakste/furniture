<?php
ini_set('error_reporting', E_ALL);
session_start();
require_once __DIR__."/../vendor/autoload.php";

use App\App;

App::getInstance();
$out= App::doRouting();
echo $out;
