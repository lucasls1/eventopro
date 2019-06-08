<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 18/05/2019
 * Time: 13:43
 */


use \EventoPro\PageAdmin;
use \EventoPro\Model\User;


$app->get("/admin/usuario/:pk_pessoa/password",function ($pk_pessoa){
    User::verifyLogin();
    $user = new User();

    $user->get((int)$pk_pessoa);

    $page = new PageAdmin();

    $page->setTpl("users-password",[
        'user'=>$user->getValues(),
        'msgError'=>User::getError(),
        'msgSuccess'=>User::getSuccess()
    ]);
});

$app->post("/admin/usuario/:pk_pessoa/password",function ($pk_pessoa){
    User::verifyLogin();
    if (!isset($_POST['pwd_senha']) || $_POST['pwd_senha']==='') {
        User::setError("Preencha a nova senha.");
        header("Location: /admin/usuario/$pk_pessoa/password");
        exit;
    }
    if (!isset($_POST['pwd_senha-confirm']) || $_POST['pwd_senha-confirm']==='') {
        User::setError("Preencha a confirmação da nova senha.");
        header("Location: /admin/usuario/$pk_pessoa/password");
        exit;
    }
    if ($_POST['pwd_senha'] !== $_POST['pwd_senha-confirm']) {
        User::setError("Confirme corretamente as senhas.");
        header("Location: /admin/usuario/$pk_pessoa/password");
        exit;
    }

    $user = new User();

    $user->get((int)$pk_pessoa);

    $user->setSenha(User::getPasswordHash($_POST['pwd_senha']));
    User::setSuccess("Senha alterada com sucesso.");
    header("Location: /admin/usuario/$pk_pessoa/password");
    exit;

});

//-----------Lista Uusarios--------------
$app->get("/admin/usuario",function(){
    User::verifyLogin();
    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '')
    {
        $pagination = User::getPageSearch($search,$page,1);
    }else{

        $pagination = User::getPage($page);
    }
    $pages = [];

    for ($x=0; $x<$pagination['pages']; $x++)
    {
        array_push($pages,[
            'href'=>'/admin/usuario?'.http_build_query([
                'page'=>$x+1,
                    'search'=>$search
                ]),
            'text'=>$x+1
        ]);
    }
    $page = new PageAdmin();
    $page->setTpl("users",array(
        "users"=>$pagination['data'],
        'search'=>$search,
        'pages'=>$pages
    ));


});

$app->get("/admin/usuario/create",function (){
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("users-create");

});
$app->get("/admin/usuario/:pk_pessoa/delete",function ($pk_pessoa){
    User::verifyLogin();
    $users = new User();

    $users->get((int)$pk_pessoa);
    $users->delete();
    header("Location: /admin/usuario");
    exit;
});
$app->get("/admin/usuario/:pk_pessoa",function ($pk_pessoa){
    User::verifyLogin();
    $user = new User();
    $user->get((int)$pk_pessoa);
    $page = new PageAdmin();
    $page->setTpl("users-update",array(
        "user"=>$user->getValues()
    ));

});
$app->post("/admin/usuario/create", function () {

    User::verifyLogin();

    $user = new User();

    $_POST["adm_inadmin"] = (isset($_POST["adm_inadmin"])) ? 1 : 0;

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/usuario");
    exit;

});
$app->post("/admin/usuario/:pk_pessoa",function ($pk_pessoa){
    User::verifyLogin();
    $users = new User();
    $_POST["adm_inadmin"] = (isset($_POST["adm_inadmin"])) ? 1 : 0;
    $users->get((int)$pk_pessoa);
    $users->setData($_POST);
     $users->update();
    header("Location: /admin/usuario");
    exit;
});


?>