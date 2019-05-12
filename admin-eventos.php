<?php 

use \EventoPro\PageAdmin;
use \EventoPro\Model\User;
use \EventoPro\Model\Product;

$app->get("/admin/eventos/",function(){

	User::verifyLogin();
	
	$product = Product::listAll();
	
	$page = new PageAdmin();
	
	$page->setTpl("products",[
		"products"=>$product
	]);
});

$app->get("/admin/eventos/create",function(){

	User::verifyLogin();
	$page = new PageAdmin();

	$page->setTpl("products-create");

});

$app->post("/admin/eventos/create",function(){

	User::verifyLogin();
	$product = new Product();
	$product->setData($_POST);
        
	$product->save();
	header("Location: /admin/eventos/");
	exit;


});

$app->get("/admin/eventos/:idevento",function($idevento){

	User::verifyLogin();
	$evento = new Product();
	$evento->get((int)$idevento);
	$page = new PageAdmin();

	$page->setTpl("products-update",[
		'evento' =>$evento->getValues()
	]);

});

$app->post("/admin/eventos/:idevento",function($idevento){

	User::verifyLogin();
	$evento = new Product();
	$evento->get((int)$idevento);
	$evento->setData($_POST);
	$evento->setPhoto($_FILES["file"]);
	$evento->save();
	

	header("Location: /admin/eventos");
	exit;
});



$app->get("/admin/eventos/:idevento/delete",function($idevento){

	User::verifyLogin();
	$product = new Product();
	$product->get((int)$idevento);
	$product->delete();
	header("Location: /admin/eventos/");
	exit;
});
 ?>