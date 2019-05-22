<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 18/05/2019
 * Time: 13:48
 */
use \EventoPro\Model\User;
use \EventoPro\PageAdmin;
use \EventoPro\Model\Evento;
$app->get("/admin/evento", function (){

    User::verifyLogin();
    $evento = Evento::listAll();
    $page =new PageAdmin();

    $page->setTpl("eventos",[
        "eventos"=>$evento
    ]);

});

$app->get("/admin/evento/create",function (){
    User::verifyLogin();
    $page =new PageAdmin();

    $page->setTpl("eventos-create");

});

$app->post("/admin/evento/create",function (){
    User::verifyLogin();

    $evento = new Evento();
    $evento->setData($_POST);
    $evento->save();

    header("Location: /admin/evento");
    exit;

});

$app->get("/admin/evento/:pk_evento/delete",function ($pk_evento){
    User::verifyLogin();

    $evento = new Evento();
    $evento->get((int)$pk_evento);
    $evento->delete();

    header("Location: /admin/evento");
    exit;
});

$app->get("/admin/evento/:pk_evento",function ($pk_evento){
    User::verifyLogin();
    $evento = new Evento();
    $evento->get((int)$pk_evento);
    $page =new PageAdmin();
    $page->setTpl("eventos-update",[
    "eventos"=>$evento->getValues()
    ]);
});

$app->post("/admin/evento/:pk_evento",function ($pk_evento){
    User::verifyLogin();
    $evento = new Evento();
    $evento->get((int)$pk_evento);
    $evento->setData($_POST);
    $evento->save();
    $evento->setPhoto($_FILES["file"]);
    header("Location: /admin/evento");
    exit;
});

?>

