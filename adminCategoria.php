<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 18/05/2019
 * Time: 13:46
 */


use \EventoPro\PageAdmin;
use \EventoPro\Model\User;
use \EventoPro\Model\Categoria;
use \EventoPro\Model\Evento;

$app->get("/admin/categoria",function (){
    User::verifyLogin();
    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '')
    {
        $pagination = Categoria::getPageSearch($search,$page,1);
    }else{

        $pagination = Categoria::getPage($page);
    }
    $pages = [];

    for ($x=0; $x<$pagination['pages']; $x++)
    {
        array_push($pages,[
            'href'=>'/admin/categoria?'.http_build_query([
                    'page'=>$x+1,
                    'search'=>$search
                ]),
            'text'=>$x+1
        ]);
    }
   // $categoria = Categoria::listAll();
    $page = new PageAdmin();

    $page->setTpl("categories",[
        "categoria"=>$pagination['data'],
        'search'=>$search,
        'pages'=>$pages
    ]);

});
$app->get("/admin/categoria/create",function (){
    User::verifyLogin();
    $page = new PageAdmin();

    $page->setTpl("categories-create");
});
$app->post("/admin/categoria/create",function (){
    User::verifyLogin();
    $categoria = new Categoria();

    $categoria->setData($_POST);

    $categoria->save();
    header("Location: /admin/categoria");
    exit;
});
$app->get("/admin/categoria/:pk_categoria/delete",function ($pk_categoria){
    User::verifyLogin();
    $categoria = new Categoria();

    $categoria->get((int)$pk_categoria);
    $categoria->delete();
    header("Location: /admin/categoria");
    exit;

});


$app->get("/admin/categoria/:pk_categoria",function ($pk_categoria){
    User::verifyLogin();
    $cateria = new Categoria();

    $cateria->get((int)$pk_categoria);


    $page = new PageAdmin();

    $page->setTpl("categories-update",[
        "categoria"=>$cateria->getValues()
    ]);

});
$app->post("/admin/categoria/:pk_categoria",function ($pk_categoria){
    User::verifyLogin();
    $cateria = new Categoria();

    $cateria->get((int)$pk_categoria);
    $cateria->setData($_POST);
    $cateria->save();

    header("Location: /admin/categoria");
    exit;


});

$app->get("/admin/categoria/:pk_categoria/evento", function ($pk_categoria){
    User::verifyLogin();
    $cateria = new Categoria();
    $cateria->get((int)$pk_categoria);
    $page = new PageAdmin();

    $page->setTpl("categories-eventos",[
        "categoria"=>$cateria->getValues(),
        "productsRelated"=>$cateria->getEventos(),
        "productsNotRelated"=>$cateria->getEventos(false)
    ]);
});
$app->get("/admin/categoria/:pk_categoria/evento/:pk_evento/add", function ($pk_categoria,$pk_evento){
    User::verifyLogin();
    $cateria = new Categoria();
    $cateria->get((int)$pk_categoria);
    $evento = new Evento();
    $evento->get((int)$pk_evento);
    $cateria->addEvento($evento);
    header("Location: /admin/categoria/".$pk_categoria."/evento");
    exit;
});

$app->get("/admin/categoria/:pk_categoria/evento/:pk_evento/remove", function ($pk_categoria,$pk_evento){
    User::verifyLogin();
    $cateria = new Categoria();
    $cateria->get((int)$pk_categoria);
    $evento = new Evento();
    $evento->get((int)$pk_evento);
    $cateria->removeEvento($evento);
    header("Location: /admin/categoria/".$pk_categoria."/evento");
    exit;
});
?>