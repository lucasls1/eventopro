<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 28/05/2019
 * Time: 19:30
 */

use  \EventoPro\PageAdmin;
use \EventoPro\Model\User;
use  \EventoPro\Model\Order;
use \EventoPro\Model\OrderStatus;

$app->get("/admin/orders/:pk_order/status",function ($pk_order){
    User::verifyLogin();
    $order = new Order();

    $order->get((int)$pk_order);

    $page = new PageAdmin();

    $page->setTpl("order-status",[
        'order'=>$order->getValues(),
       'status'=>OrderStatus::listAll(),
        'msgSuccess'=>Order::getSuccess(),
        'msgError'=>Order::getError()
    ]);

});

$app->post("/admin/orders/:pk_order/status",function ($pk_order){
    User::verifyLogin();

    if (!isset($_POST['pk_status']) || !(int)$_POST['pk_status'] > 0){

        Order::setError("Informe o status Atual.");
        header("Location: /admin/orders/".$pk_order."/status");
        exit;
    }
    $order = new Order();

    $order->get((int)$pk_order);

    $order->setpk_status((int)$_POST['pk_status']);
    $order->save();
    Order::setSuccess("Status Atualizado.");
    header("Location: /admin/orders/".$pk_order."/status");
    exit;
});


$app->get("/admin/orders/:pk_order/delete",function ($pk_order){
    User::verifyLogin();
    $order = new Order();

    $order->get((int)$pk_order);

    $order->delete();

    header("Location: /admin/orders");
    exit;

});

$app->get("/admin/orders/:pk_order",function ($pk_order){
    User::verifyLogin();
    $order= new Order();

    $order->get((int)$pk_order);
    $carrinho = $order->getCarrinho();
    $page = new PageAdmin();

    $page->setTpl("order",[
        'order'=>$order->getValues(),
        'cart'=>$carrinho->getValues(),
        'evento'=>$carrinho->getEventos()
    ]);

});

$app->get("/admin/orders",function (){
    User::verifyLogin();
    $search = (isset($_GET['search'])) ? $_GET['search'] : "";
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    if ($search != '')
    {
        $pagination = Order::getPageSearch($search,$page,1);
    }else{

        $pagination = Order::getPage($page);
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
    $page = new PageAdmin();

    $page->setTpl("orders",[
        "orders"=>$pagination['data'],
        'search'=>$search,
        'pages'=>$pages
    ]);

});

?>