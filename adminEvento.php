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
    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '')
    {
        $pagination = Evento::getPageSearch($search,$page,1);
    }else{

        $pagination = Evento::getPage($page);
    }
    $pages = [];

    for ($x=0; $x<$pagination['pages']; $x++)
    {
        array_push($pages,[
            'href'=>'/admin/evento?'.http_build_query([
                    'page'=>$x+1,
                    'search'=>$search
                ]),
            'text'=>$x+1
        ]);
    }

    $page =new PageAdmin();

    $page->setTpl("eventos",[
        "eventos"=>$pagination['data'],
        'search'=>$search,
        'pages'=>$pages
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

