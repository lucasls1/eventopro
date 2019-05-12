<?php 

use \EventoPro\PageAdmin;
use \EventoPro\Model\Category;
use \EventoPro\Model\User;
use \EventoPro\Model\Product;

$app->get("/admin/categories",function(){
	 User::verifyLogin();
	$categories = Category::listAll();
	$page = new PageAdmin();
	$page->setTpl("categories",array(

		"categoria"=>$categories

	));

});

	$app->get("/admin/categories/create",function(){
	 User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("categories-create");


});

	$app->post("/admin/categories/create",function(){
		 User::verifyLogin();
		$categories = new Category();
	
		$categories->setData($_POST);

		$categories->save();

		header("Location: /admin/categories");
		exit;

});

  $app->get("/admin/categories/:idcategoria/delete",function($idcategoria){
		 User::verifyLogin();
  	$category = new Category();
  	$category->get((int)$idcategoria);
  	$category->delete();

  	header("Location: /admin/categories");
  	exit;

  });

  $app->get("/admin/categories/:pk_categoria",function($idcategoria){
			 User::verifyLogin();
  		$category = new Category();
  		$category->get((int)$idcategoria);
		$page = new PageAdmin();
		$page->setTpl("categories-update",[
			'category'=>$category->getValues()
		]);


  });

    $app->post("/admin/categories/:pk_categoria",function($idcategoria){
		 User::verifyLogin();
  		$category = new Category();
  		$category->get((int)$idcategoria);
		
		$category->setData($_POST);
		
		$category->save();

		header("Location: /admin/categories");
		exit;


  });

$app->get("/admin/categoria/:idcategoria/evento",function($idcategoria){
	 User::verifyLogin();
	 $category = new Category();

	$category->get((int)$idcategoria);

	$page = new PageAdmin();
	$page->setTpl("categories-products",[
		'category'=>$category->getValues(),
		'productsRelated' =>$category->getEventos(),
		'productsNotRelated'=>$category->getEventos(false)
	]);
	
});
			
$app->get("/admin/categoria/:idcategoria/evento/:idevento/add",function($idcategoria,$idevento){
	 User::verifyLogin();
	 $category = new Category();

	$category->get((int)$idcategoria);
	$evento = new Product();
	$evento->get((int)$idevento);
	$category->addEvento($evento);
	header("Location: /admin/categoria/".$idcategoria."/evento");
	exit;
	
});
$app->get("/admin/categoria/:idcategoria/evento/:idevento/remove",function($idcategoria,$idevento){
	  User::verifyLogin();
	 $category = new Category();
	 $evento = new Product();
	$category->get((int)$idcategoria);
	$evento->get((int)$idevento);
	$category->removeEvento($evento);

	header("Location: /admin/categoria/".$idcategoria."/evento");
	exit;
	
});



 ?>