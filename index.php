<?php 
session_start();
require_once("vendor/autoload.php");
use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

require_once ("functions.php");

require_once ("site.php");

require_once ("admin.php");

require_once ("adminUsers.php");

require_once ("adminCategoria.php");

require_once ("adminEvento.php");

$app->run();

 ?>