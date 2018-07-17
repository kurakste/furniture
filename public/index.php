<?php
session_start();
require_once __DIR__."/../vendor/autoload.php";

use App\App;

App::getInstance();
$out= App::doRouting();
echo $out;
