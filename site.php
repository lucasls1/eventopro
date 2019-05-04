<?php 

use \EventoPro\Page;

$app->get('/', function() {
    
	$page = new Page();
	$page->setTpl("index");
});

 ?>