<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 18/05/2019
 * Time: 13:43
 */


use \EventoPro\PageAdmin;
use \EventoPro\Model\User;


//-----------Lista Uusarios--------------
$app->get("/admin/usuario",function(){
    User::verifyLogin();
    $users = User::listAll();
    $page = new PageAdmin();

    $page->setTpl("users",array(
        "users"=>$users
    ));


});

$app->get("/admin/usuario/create",function (){
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("users-create");

});
$app->get("/admin/usuario/:pk_usuario/delete",function ($pk_usuario){
    User::verifyLogin();
    $users = new User();

    $users->get((int)$pk_usuario);
    $users->delete();
    header("Location: /admin/usuario");
    exit;
});
$app->get("/admin/usuario/:pk_usuario",function ($pk_usuario){
    User::verifyLogin();
    $user = new User();
    $user->get((int)$pk_usuario);
    $page = new PageAdmin();
    $page->setTpl("users-update",array(
        "user"=>$user->getValues()
    ));

});
$app->post("/admin/usuario/create", function () {

    User::verifyLogin();

    $user = new User();

    $_POST["adm_inadim"] = (isset($_POST["adm_inadim"])) ? 1 : 0;

   // $_POST['pwd_senha'] = password_hash($_POST["pwd_senha"], PASSWORD_DEFAULT, [

    //    "cost"=>12

  //  ]);

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/usuario");
    exit;

});
$app->post("/admin/usuario/:pk_usuario",function ($pk_usuario){
    User::verifyLogin();
    $users = new User();
    $_POST["adm_inadim"] = (isset($_POST["adm_inadim"])) ? 1 : 0;
    $users->get((int)$pk_usuario);
    $users->setData($_POST);
    $users->update();
    header("Location: /admin/usuario");
    exit;
});


?>