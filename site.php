<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 18/05/2019
 * Time: 13:39
 */
use \EventoPro\Page;
use \EventoPro\Model\Carrinho;
use \EventoPro\Model\Categoria;
use \EventoPro\Model\Evento;
use \EventoPro\Model\Endereco;
use \EventoPro\Model\User;

$app->get('/', function() {
    $eventos =  Evento::listAll();

    $page =new Page();

    $page->setTpl("index",[
        "eventos"=>Evento::checkList($eventos)
    ]);

});

$app->get("/categoria/:pk_categoria",function($pk_categoria){

    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    $categoria = new Categoria();

    $categoria->get((int)$pk_categoria);

    $pagination = $categoria->getEventoPage($page);

    $pages = [];

    for($i=1; $i<=$pagination['pages']; $i++){
            array_push($pages,[
                'link'=>'/categoria/'.$categoria->getpk_categoria().'?page='.$i,
                'page'=>$i
            ]);
    }

    $page =new Page();

    $page->setTpl("category",[
        "category"=>$categoria->getValues(),
        "evento"=>$pagination["data"],
        'pages'=>$pages
    ]);
});

$app->get("/eventos/:url_url",function ($url_url){
    $evento = new Evento();
    $evento->getFromURL($url_url);

    $page = new Page();

    $page->setTpl("eventos-detalhes",[
        'evento' => $evento->getValues(),
        'categoria'=>$evento->getCategoria()
    ]);
});

$app->get("/carrinho",function (){
    $carrinho = Carrinho::getFromSession();
    $page = new Page();

    $page->setTpl("carrinho",[
        'cart'=>$carrinho->getValues(),
        'eventos'=>$carrinho->getEventos()
    ]);
});

$app->get("/carrinho/:pk_evento/add",function ($pk_evento){
    $evento = new Evento();

    $evento->get((int)$pk_evento);

    $carrinho = Carrinho::getFromSession();
    $qtd = (isset($_GET['qtd'])) ? (int)$_GET['qtd'] : 1;
    for($i = 0; $i < $qtd; $i++){

        $carrinho->addEvento($evento);
    }

    header("Location: /carrinho");
    exit;

});

$app->get("/carrinho/:pk_evento/minus",function ($pk_evento){
    $evento = new Evento();

    $evento->get((int)$pk_evento);

    $carrinho = Carrinho::getFromSession();

    $carrinho->removeEvento($evento);

    header("Location: /carrinho");
    exit;

});

$app->get("/carrinho/:pk_evento/remove",function ($pk_evento){
    $evento = new Evento();

    $evento->get((int)$pk_evento);

    $carrinho = Carrinho::getFromSession();

    $carrinho->removeEvento($evento,true);

    header("Location: /carrinho");
    exit;

});

$app->get("/checkout",function (){
    User::verifyLogin(false);
    $endereco = new Endereco();
    $carrinho = Carrinho::getFromSession();
    $page = new Page();
        $page->setTpl("checkout",[
             'cart'=>$carrinho->getValues(),
            'endereco'=>$endereco->getValues()

        ]);
});

$app->get("/login",function (){


    $page = new Page();
    $page->setTpl("login",[
        'erro'=>User::getError(),
        'errorRegister'=>User::getErrorRegister(),
        'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : ['nme_pesosa'=>'',
            'eml_email'=>'','nrphone'=>'']
    ]);
});

$app->post("/login",function (){
    try{
        User::login($_POST['login'],$_POST['password']);
    }catch (Exception $e){
        User::setError($e->getMessage());

    }

    header("Location: /");
    exit;
});
$app->get("/logout",function (){

    User::logout();
    header("Location: /");
    exit;

});
$app->post("/register", function (){
        $_SESSION['registerValues'] =$_POST;
    if (!isset($_POST['nme_pessoa']) || $_POST['nme_pessoa'] == '') {
        User::setErrorRegister("Preencha o seu nome.");
        header("Location: /login");
        exit;
    }
    if (!isset($_POST['eml_email']) || $_POST['eml_email'] == '') {
        User::setErrorRegister("Preencha o seu e-mail.");
        header("Location: /login");
        exit;
    }
    if (!isset($_POST['pwd_senha']) || $_POST['pwd_senha'] == '') {
        User::setErrorRegister("Preencha a senha.");
        header("Location: /login");
        exit;
    }

    if (User::checkLoginExist($_POST['eml_email']) === true)
    {
        User::setErrorRegister("Este endereço de e-mail já está sendo usado por outro usuário.");
        header("Location: /login");
        exit;
    }

    $user = new User();
    $user->setData([
        'adm_inadim'=>0,
        'usr_login'=>$_POST['eml_email'],
        'nme_pessoa'=>$_POST['nme_pessoa'],
        'eml_email'=>$_POST['eml_email'],
        'pwd_senha'=>$_POST['pwd_senha'],
        'nrphone'=> $_POST['nrphone']
    ]);

    $user->save();
    User::login($_POST['eml_email'],$_POST['pwd_senha']);

    header("Location: /checkout");
    exit;
});

$app->get("/forgot",function (){
    $page = new Page();
    $page->setTpl("forgot");

});

$app->post("/forgot",function (){


    $user = User::getForgot($_POST["email"] ,false);
    header("Location: /forgot/sent");
    exit;
});
$app->get("/forgot/sent",function (){
    $page = new Page();
    $page->setTpl("forgot-sent");
});
$app->get("/forgot/reset",function (){
    $user = User::validForgotDecrypt($_GET["code"]);
    $page = new Page();

    $page->setTpl("forgot-reset",array(
        "name"=>$user["nme_pessoa"],
        "code"=>$_GET["code"]
    ));
});
$app->post("/forgot/reset",function (){
    $forgot = User::validForgotDecrypt($_POST["code"]);
    User::setForgotUsed($forgot["pk_recuperacao"]);
    $user = new User();

    $user->get((int)$forgot["pk_usuario"]);
    $senha = password_hash($_POST["pwd_senha"], PASSWORD_DEFAULT, [

        "cost"=>12

    ]);
    $user->setSenha($senha);

    $page = new Page();

    $page->setTpl("forgot-reset-success");

});

$app->get("/profile",function (){
    User::verifyLogin(false);

    $user = User::getFromSession();

    $page = new Page();

    $page->setTpl("profile",[
        'user'=> $user->getValues(),
        'profileMsg'=>User::getSuccess(),
        'profileError'=>User::getError()
    ]);
});
$app->post("/profile", function(){
    User::verifyLogin(false);
    if (!isset($_POST['nme_pessoa']) || $_POST['nme_pessoa'] === '') {
        User::setError("Preencha o seu nome.");
        header('Location: /profile');
        exit;
    }
    if (!isset($_POST['eml_email']) || $_POST['eml_email'] === '') {
        User::setError("Preencha o seu e-mail.");
        header('Location: /profile');
        exit;
    }
    $user = User::getFromSession();
    if ($_POST['eml_email'] !== $user->geteml_email()) {
        if (User::checkLoginExists($_POST['eml_email']) === true) {
            User::setError("Este endereço de e-mail já está cadastrado.");
            header('Location: /profile');
            exit;
        }
    }
    $_POST['adm_inadim'] = $user->getadm_inadim();
    $_POST['pwd_senha'] = $user->getpwd_senha();
    $_POST['urs_login'] = $_POST['eml_email'];
    $user->setData($_POST);
    $user->update();
    User::setSuccess("Dados alterados com sucesso!");
    header('Location: /profile');
    exit;
});




?>