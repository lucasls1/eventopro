<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use EventoPro\Page;
use EventoPro\PageAdmin;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();
	$page->setTpl("index");
});

$app->get('/admin', function() {
    
	$page = new PageAdmin();
	$page->setTpl("index");
});
$app->run();

 ?>