<?php 

use \EventoPro\PageAdmin;
use \EventoPro\Model\User;

//-----------Lista Uusarios--------------
$app->get("/admin/users",function(){
	 User::verifyLogin();
	$users = User::listAll();
	$page = new PageAdmin();

	$page->setTpl("users",array(
		"users"=>$users
	));


});
//----------Formulario de Cadastro-------------- 
$app->get("/admin/users/create",function(){
	 User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");


});
//-----------Apaga Usuario------------------------------
$app->get("/admin/users/:pk_usuario/delete",function($idusuario){

	User::verifyLogin();
	$user = new User();
	$user->get((int)$idusuario);
	$user->delete();
	header("Location: /admin/users");
	exit;

});
//------------Atualiza Usuario--------------------------
$app->get("/admin/users/:pk_usuario",function($idusuario){
	 User::verifyLogin();
	 $user = new User();
	 $user->get((int)$idusuario);
	$page = new PageAdmin();

	$page->setTpl("users-update",array(
		"user"=>$user->getValues()
	));


});

//---------------Grava o Cadastro no banco----------------
$app->post("/admin/users/create", function () {

 	User::verifyLogin();

	$user = new User();

 	$_POST["adm_inadim"] = (isset($_POST["adm_inadim"])) ? 1 : 0;

 	$_POST['pwd_senha'] = password_hash($_POST["pwd_senha"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);

 	$user->setData($_POST);
         
	$user->save();

	header("Location: /admin/users");
 	exit;

});
//---------atualizar no banco----------------
$app->post("/admin/users/:pk_usuario",function($idusuario){

	User::verifyLogin();
	$user = new User();
	$_POST["adm_inadim"] = (isset($_POST["adm_inadim"])) ? 1 : 0;
	$user->get((int)$idusuario);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;

});

 ?>