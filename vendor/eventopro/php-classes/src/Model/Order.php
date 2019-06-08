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
use EventoPro\Model\Carrinho;


class Order extends  Model{
        const  SUCCESS = "Order-Success";
        const  ERROR = "Order-Error";
    public function  save()
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_order_save(:pk_order, :fk_carrinho, :fk_pessoa, :fk_status, :fk_endereco, :vlr_total)",[
            ':pk_order'=>$this->getpk_order(),
            ':fk_carrinho'=>$this->getpk_carrinho(),
            ':fk_pessoa'=>$this->getpk_pessoa(),
            ':fk_status'=>$this->getpk_status(),
            ':fk_endereco'=>$this->getpk_endereco(),
            ':vlr_total'=>$this->getvlr_total()
        ]);

        if (count($results) > 0)
        {
            $this->setData($results[0]);
        }

    }

    public  function get($pk_order)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT * 
    FROM tb_ordem a
    INNER JOIN tb_ordemstatus s ON a.fk_status = s.pk_status
    INNER JOIN tb_carrinho c ON  a.fk_carrinho = c.pk_carrinho
    INNER JOIN tb_pessoa d ON d.pk_pessoa = a.fk_pessoa
    INNER JOIN tb_endereco e ON e.pk_endereco = a.fk_endereco
    WHERE pk_order = :pidorder",[
        ':pidorder'=>$pk_order
        ]);

        if (count($results) > 0)
        {
            $this->setData($results[0]);
        }
    }

    public static function  listAll()
    {
        $sql = new Sql();

         return $sql->select("SELECT * 
                            FROM tb_ordem a
                            INNER JOIN tb_ordemstatus s ON a.fk_status = s.pk_status
                            INNER JOIN tb_carrinho c ON  a.fk_carrinho = c.pk_carrinho
                            INNER JOIN tb_pessoa d ON d.pk_pessoa = a.fk_pessoa
                            INNER JOIN tb_endereco e ON e.pk_endereco = a.fk_endereco 
                            ORDER BY a.dti_registro DESC");
    }

    public function delete()
    {
        $sql = new Sql();

        $sql->query("DELETE FROM tb_ordem WHERE pk_order = :pk_order",[
            ':pk_order'=>$this->getpk_order()
        ]);
    }

    public function getCarrinho():Carrinho
    {
        $carrinho = new Carrinho();

        $carrinho->get((int)$this->getpk_carrinho());
        return $carrinho;
    }

    public static function setError($msg)
    {
        $_SESSION[Order::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[Order::ERROR]) && $_SESSION[Order::ERROR]) ? $_SESSION[Order::ERROR] : '';
        Order::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[Order::ERROR] = NULL;
    }
    public static function setSuccess($msg)
    {
        $_SESSION[Order::SUCCESS] = $msg;
    }
    public static function getSuccess()
    {
        $msg = (isset($_SESSION[Order::SUCCESS]) && $_SESSION[Order::SUCCESS]) ? $_SESSION[Order::SUCCESS] : '';
        Order::clearSuccess();
        return $msg;
    }
    public static function clearSuccess()
    {
        $_SESSION[Order::SUCCESS] = NULL;
    }

    public static  function getPage($page = 1 ,$itensPerPage =8)
    {
        $start = ($page - 1) * $itensPerPage;
        $sql = new Sql();
        $results = $sql->select("SELECT * 
                            FROM tb_ordem a
                            INNER JOIN tb_ordemstatus s ON a.fk_status = s.pk_status
                            INNER JOIN tb_carrinho c ON  a.fk_carrinho = c.pk_carrinho
                            INNER JOIN tb_pessoa d ON d.pk_pessoa = a.fk_pessoa
                            INNER JOIN tb_endereco e ON e.pk_endereco = a.fk_endereco 
                            ORDER BY a.dti_registro DESC
                                LIMIT $start,$itensPerPage
                                ");
        $resultsTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

        return [
            'data'=>$results,
            'total'=>(int)$resultsTotal[0]["nrtotal"],
            'pages'=>ceil($resultsTotal[0]["nrtotal"] / $itensPerPage)

        ];
    }

    public static  function getPageSearch($search,$page = 1 ,$itensPerPage =8)
    {
        $start = ($page - 1) * $itensPerPage;
        $sql = new Sql();
        $results = $sql->select("SELECT * 
                            FROM tb_ordem a
                            INNER JOIN tb_ordemstatus s ON a.fk_status = s.pk_status
                            INNER JOIN tb_carrinho c ON  a.fk_carrinho = c.pk_carrinho
                            INNER JOIN tb_pessoa d ON d.pk_pessoa = a.fk_pessoa
                            INNER JOIN tb_endereco e ON e.pk_endereco = a.fk_endereco 
                            WHERE a.pk_order = :id OR d.nme_pessoa LIKE :search
                            ORDER BY a.dti_registro DESC
                                LIMIT $start,$itensPerPage
                                ",[
            ':search'=>'%'.$search.'%',
            ':id'=>$search
        ]);
        $resultsTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

        return [
            'data'=>$results,
            'total'=>(int)$resultsTotal[0]["nrtotal"],
            'pages'=>ceil($resultsTotal[0]["nrtotal"] / $itensPerPage)

        ];
    }




}
?>