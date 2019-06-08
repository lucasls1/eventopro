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
use \EventoPro\Model\OrderStatus;
use \EventoPro\Model\Order;


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

    if(isset($_GET['cep_cep']))
    {
        $endereco->loadFromCEP($_GET['cep_cep']);
       // $carrinho->save();
       // $carrinho->getCalculateTotal();
    }

    if (!$endereco->getend_endereco()) $endereco->setend_endereco('');
    if (!$endereco->getcpt_complemento()) $endereco->setcpt_complemento('');
    if (!$endereco->getcid_cidade()) $endereco->setcid_cidade('');
    if (!$endereco->getest_estado()) $endereco->setest_estado('');
    if (!$endereco->getpais_pais()) $endereco->setpais_pais('');
    if (!$endereco->getnr_numero()) $endereco->setnr_numero('');
    if (!$endereco->getcep_cep()) $endereco->setcep_cep('');
    if (!$endereco->getdes_distrito()) $endereco->setdes_distrito('');

    $page = new Page();
        $page->setTpl("checkout",[
             'cart'=>$carrinho->getValues(),
            'endereco'=>$endereco->getValues(),
            'evento'=>$carrinho->getEventos(),
            'error'=>$endereco->getMsgError()

        ]);
});
$app->post("/checkout",function (){

        if (!isset($_POST['cep_cep']) || $_POST['cep_cep'] ==='')
    {
        Endereco::setMsgError("Informe o CEP.");
        header("Location: /checkout");
        exit;
    }
    if (!isset($_POST['end_endereco']) || $_POST['end_endereco'] ==='')
    {
        Endereco::setMsgError("Informe o Endereço.");
        header("Location: /checkout");
        exit;
    }
    if (!isset($_POST['nr_numero']) || $_POST['nr_numero'] ==='')
    {
        Endereco::setMsgError("Informe o Numero.");
        header("Location: /checkout");
        exit;
    }

    if (!isset($_POST['des_distrito']) || $_POST['des_distrito'] ==='')
    {
        Endereco::setMsgError("Informe o Bairro.");
        header("Location: /checkout");
        exit;
    }

    if (!isset($_POST['cid_cidade']) || $_POST['cid_cidade'] ==='')
    {
        Endereco::setMsgError("Informe a Cidade.");
        header("Location: /checkout");
        exit;
    }

    if (!isset($_POST['est_estado']) || $_POST['est_estado'] ==='')
    {
        Endereco::setMsgError("Informe o Estado.");
        header("Location: /checkout");
        exit;
    }

    if (!isset($_POST['pais_pais']) || $_POST['pais_pais'] ==='')
    {
        Endereco::setMsgError("Informe o Pais.");
        header("Location: /checkout");
        exit;
    }
        $endereco = new Endereco();
        $user = User::getFromSession();

         $_POST['pk_pessoa'] = $user->getpk_pessoa();

        $endereco->setData($_POST);

        $endereco->save();
        $carrinho = Carrinho::getFromSession();
        $carrinho->getCalculateTotal();

        $order = new Order();
        $order->setData([
            'pk_carrinho'=>$carrinho->getpk_carrinho(),
            'pk_endereco'=>$endereco->getpk_endereco(),
            'pk_pessoa'=>$user->getpk_pessoa(),
            'pk_status'=>OrderStatus::EM_ABERTO,
            'vlr_total'=>$carrinho->getvltotal()
        ]);
        $order->save();
    header("Location: /order/". $order->getpk_order());
    exit;
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
        header("Location: /login");
        exit;
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
        'adm_inadmin'=>0,
        'usr_login'=>$_POST['eml_email'],
        'nme_pessoa'=>$_POST['nme_pessoa'],
        'eml_email'=>$_POST['eml_email'],
        'pwd_senha'=>$_POST['pwd_senha'],
        'nrphone'=> $_POST['nrphone']
    ]);

    $user->save();
    User::login($_POST['eml_email'],$_POST['pwd_senha']);

    header("Location: /");
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

$app->get("/order/:pk_order",function ($pk_order){
    User::verifyLogin(false);
    $order = new Order();
    $order->get((int)$pk_order);

    $page = new Page();

    $page->setTpl("payment",[
        'order'=>$order->getValues()
    ]);
});


$app->get("/order/:pk_order",function ($pk_order){

    User::verifyLogin(false);
    $order = new Order();
    $order->get((int)$pk_order);
    $page = new Page();
    $page->setTpl("payment",[
        'order'=>$order->getValues()
    ]);
});

$app->get("/boleto/:pk_order",function ($pk_order){

    User::verifyLogin(false);
    $order = new Order();

    $order->get((int)$pk_order);


// DADOS DO BOLETO PARA O SEU CLIENTE
    $dias_de_prazo_para_pagamento = 10;
    $taxa_boleto = 5.00;
    $data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006";
    $valor_cobrado = formatPrice($order->getvlr_total()); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
    $valor_cobrado = str_replace(",", ".",$valor_cobrado);
    $valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

    $dadosboleto["nosso_numero"] = $order->getpk_order();  // Nosso numero - REGRA: Máximo de 8 caracteres!
    $dadosboleto["numero_documento"] = $order->getpk_order();	// Num do pedido ou nosso numero
    $dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
    $dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
    $dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
    $dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
    $dadosboleto["sacado"] = $order->getnme_pessoa();
    $dadosboleto["endereco1"] = $order->getend_endereco()." ".$order->getdes_distrito();
    $dadosboleto["endereco2"] = $order->getcid_cidade()." - ". $order->getest_estado()." - "." - CEP:".$order->getcep_cep();

// INFORMACOES PARA O CLIENTE
    $dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja EventoPro";
    $dadosboleto["demonstrativo2"] = "Taxa bancária - R$ 0,00";
    $dadosboleto["demonstrativo3"] = "";
    $dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
    $dadosboleto["instrucoes2"] = "- Receber até 10 dias após o vencimento";
    $dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: suporte@eventopro.com.br";
    $dadosboleto["instrucoes4"] = "&nbsp; Emitido pelo sistema  Loja EventoPro - www.eventopro.tk";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
    $dadosboleto["quantidade"] = "";
    $dadosboleto["valor_unitario"] = "";
    $dadosboleto["aceite"] = "";
    $dadosboleto["especie"] = "R$";
    $dadosboleto["especie_doc"] = "";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - ITAÚ
    $dadosboleto["agencia"] = "1690"; // Num da agencia, sem digito
    $dadosboleto["conta"] = "48781";	// Num da conta, sem digito
    $dadosboleto["conta_dv"] = "2"; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - ITAÚ
    $dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157

// SEUS DADOS
    $dadosboleto["identificacao"] = "EventoPro";
    $dadosboleto["cpf_cnpj"] = "24.700.731/0001-08";
    $dadosboleto["endereco"] = "Avenida das Araucárias, Rua 214 Lote 1/17, QS 1, 87455-120";
    $dadosboleto["cidade_uf"] = "Taguatinga, Brasília - DF";
    $dadosboleto["cedente"] = "EventoPro";

// NÃO ALTERAR!
    $path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "res" . DIRECTORY_SEPARATOR . "boletophp" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR;
    require_once($path . "funcoes_itau.php");
    require_once($path . "layout_itau.php");

});

$app->get("/profile/orders",function (){
    User::verifyLogin(false);
    $user = User::getFromSession();
    $page = new Page();

    $page->setTpl("profile-orders",[
        'orders'=>$user->getOrder()
    ]);
});

$app->get("/profile/orders/:pk_order", function ($pk_order){
    User::verifyLogin(false);
    $order = new Order();
    $order->get((int)$pk_order);
    $carrinho = new Carrinho();
    $carrinho->get((int)$order->getpk_carrinho());
    $page = new Page();

    $page->setTpl("profile-orders-detail",[
        'order'=>$order->getValues(),
        'carrinho'=>$carrinho->getValues(),
        'evento'=>$carrinho->getEventos()
    ]);
});

$app->get("/profile/change-password",function (){
    User::verifyLogin(false);
    $page = new Page();

    $page->setTpl("profile-change-password",[
        'changePassError'=>User::getError(),
        'changePassSuccess'=>User::getSuccess()
    ]);
});

$app->post("/profile/change-password",function (){
    User::verifyLogin(false);

        if(!isset($_POST['current_pass']) || $_POST['current_pass'] === '')
    {
        User::setError("Digite a senha atual.");
        header("Location: /profile/change-password");
        exit;
    }

    if(!isset($_POST['new_pass']) || $_POST['new_pass'] === '')
    {
        User::setError("Digite a nova senha.");
        header("Location: /profile/change-password");
        exit;
    }

    if(!isset($_POST['new_pass_confirm']) || $_POST['new_pass_confirm'] === '')
    {
        User::setError("Confirme a nova senha.");
        header("Location: /profile/change-password");
        exit;
    }
    if($_POST['current_pass'] === $_POST['new_pass'])
    {
        User::setError("A sua Senha deve ser diferente da atual.");
        header("Location: /profile/change-password");
        exit;
    }

    $user = User::getFromSession();

    if(!password_verify($_POST['current_pass'], $user->getpwd_senha()))
    {
        User::setError("A Senha está inválida.");
        header("Location: /profile/change-password");
        exit;
    }
    $user->setpwd_senha($_POST['new_pass']);
    $user->update();
    User::setSuccess("Senha Alterada com Sucesso.");
    header("Location: /profile/change-password");
    exit;

});
$app->get("/profile/deleteconta",function (){
    User::verifyLogin(false);

    $page = new Page();

    $page->setTpl("delete-conta");

});

$app->post("/profile/delete-conta",function (){
    User::verifyLogin(false);
    $users = User::getFromSession();

    $users->delete();
    User::logout();
    header("Location: /");
    exit;

});

?>