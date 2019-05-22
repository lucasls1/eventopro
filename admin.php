<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 18/05/2019
 * Time: 13:41
 */

use \EventoPro\PageAdmin;
use \EventoPro\Model\User;


$app->get('/admin', function() {
    User::verifylogin();
    $page =new PageAdmin();

    $page->setTpl("index");

});

$app->get("/admin/login",function (){

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false

    ]);

    $page->setTpl("login");

});

$app->post("/admin/login",function (){

    User::login($_POST['usr_login'], $_POST['pwd_senha']);
    header("Location: /admin");
    exit;

});

$app->get("/admin/logout",function (){
    User::logout();

    header("Location: /admin/login");
    exit;
});


$app->get("/admin/forgot",function (){
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false

    ]);

    $page->setTpl("forgot");

});

$app->post("/admin/forgot",function (){


    $user = User::getForgot($_POST["email"]);
    header("Location: /admin/forgot/sent");
    exit;
});
$app->get("/admin/forgot/sent",function (){
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false

    ]);

    $page->setTpl("forgot-sent");
});
$app->get("/admin/forgot/reset",function (){
    $user = User::validForgotDecrypt($_GET["code"]);
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false

    ]);

    $page->setTpl("forgot-reset",array(
        "name"=>$user["nme_pessoa"],
        "code"=>$_GET["code"]
    ));
});
$app->post("/admin/forgot/reset",function (){
    $forgot = User::validForgotDecrypt($_POST["code"]);
    User::setForgotUsed($forgot["pk_recuperacao"]);
    $user = new User();

    $user->get((int)$forgot["pk_usuario"]);
    $senha = password_hash($_POST["pwd_senha"], PASSWORD_DEFAULT, [

        "cost"=>12

    ]);
    $user->setSenha($senha);

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false

    ]);

    $page->setTpl("forgot-reset-success");

});



?>