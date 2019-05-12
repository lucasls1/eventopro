<?php 

use \EventoPro\Page;
use \EventoPro\Model\Category;
use \EventoPro\Model\Product;

$app->get('/', function() {

    $evento = Product::listAll();
	$page = new Page();
	$page->setTpl("index",[
		'evento'=>Product::checList($evento)
	]);
});

$app->get("/categories/:idcategory",function($idcategory){
	 
	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();
	$page->setTpl("category",[
		'category'=>$category->getValues(),
		'products' =>Product::checList($category->getEventos())
	]);

});

 ?>