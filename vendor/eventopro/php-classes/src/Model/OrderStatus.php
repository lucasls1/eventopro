<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 23/05/2019
 * Time: 20:42
 */
namespace EventoPro\Model;
use EventoPro\DB\Sql;
use EventoPro\Model;

class OrderStatus extends  Model{

        const EM_ABERTO = 1;
        const AGUARDANDO_PAGAMENTO = 2;
        const PAGO = 3;

        public static function listAll()
        {
            $sql = new Sql();

           return $sql->select("SELECT * FROM tb_ordemstatus ORDER BY sts_status");
        }

}
?>