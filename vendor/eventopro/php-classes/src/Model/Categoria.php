<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 18/05/2019
 * Time: 11:58
 */
namespace EventoPro\model;
use \EventoPro\DB\Sql;
use \EventoPro\Model;

class  Categoria extends  Model{
    public static function  listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_categoria ORDER BY nme_categoria");

    }
    public function  save()
    {
        $sql = new Sql();
        $result = $sql->select("CALL sp_categoria_save(:pk_categoria,:nme_categoria)",array(
            ":pk_categoria"=> $this->getpk_categoria(),
            ":nme_categoria"=> $this->getnme_categoria()

        ));

        $this->setData($result[0]);
        Categoria::updateFile();
    }
    public  function  get($pk_categoria)
    {
        $sql = new Sql();

        $results=  $sql->select("SELECT * FROM tb_categoria WHERE pk_categoria = :pk_categoria",[
            ":pk_categoria"=>$pk_categoria
        ]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_categoria WHERE pk_categoria=:pk_categoria",[
            ":pk_categoria"=>$this->getpk_categoria()
        ]);
        Categoria::updateFile();
    }

    public  static  function  updateFile()
    {
        $categoria = Categoria::listAll();
        $html =[];

        foreach ($categoria as $row) {
            array_push($html,'<li><a href="/categoria/'.$row['pk_categoria'].'">'.$row['nme_categoria'].'</a></li>');
        }
        file_put_contents($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."categoria-menu.html",implode('',$html));
    }

    public  function  getEventos($related= true)
    {
        $sql = new Sql();
        if ($related === true)
        {
         return  $sql->select("SELECT * FROM tb_evento WHERE  pk_evento IN(
                                    SELECT a.pk_evento 
                                    FROM tb_evento a 
                                    INNER JOIN tb_eventocategoria b ON a.pk_evento = b.pk_evento
                                    WHERE b.pk_categoria = :pk_categoria
                                    )",[
                                        ':pk_categoria'=>$this->getpk_categoria()
            ]);
        }else{
                  return  $sql->select("SELECT * FROM tb_evento WHERE  pk_evento NOT IN(
                                            SELECT a.pk_evento 
                                            FROM tb_evento a 
                                            INNER JOIN tb_eventocategoria b ON a.pk_evento = b.pk_evento
                                            WHERE b.pk_categoria = :pk_categoria
                                            )",[
                        ':pk_categoria'=>$this->getpk_categoria()
                    ]);
        }
    }
    public  function getEventoPage($page = 1 ,$itensPerPage =8)
    {
        $start = ($page - 1) * $itensPerPage;
        $sql = new Sql();
        $results = $sql->select("SELECT SQL_CALC_FOUND_ROWS * 
                                FROM tb_evento a
                                INNER JOIN tb_eventocategoria b ON a.pk_evento = b.pk_evento
                                INNER JOIN tb_categoria c ON c.pk_categoria = b.pk_categoria
                                WHERE c.pk_categoria = :pk_categoria
                                LIMIT $start,$itensPerPage
                                ",[
                                    ":pk_categoria"=>$this->getpk_categoria()
        ]);
       $resultsTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

       return [
           'data'=>Evento::checkList($results),
           'total'=>(int)$resultsTotal[0]["nrtotal"],
           'pages'=>ceil($resultsTotal[0]["nrtotal"] / $itensPerPage)

       ];
    }
    public function addEvento(Evento $evento)
    {
        $sql = new Sql();
        $sql->query("INSERT INTO tb_eventocategoria (pk_categoria,pk_evento) VALUES(:pk_categoria,:pk_evento)",[
            ":pk_categoria"=>$this->getpk_categoria(),
            ":pk_evento"=> $evento->getpk_evento()
        ]);
    }
    public function removeEvento(Evento $evento)
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_eventocategoria WHERE pk_categoria = :pk_categoria AND pk_evento = :pk_evento", [
            ':pk_categoria'=>$this->getpk_categoria(),
            ':pk_evento'=>$evento->getpk_evento()
        ]);
    }

    public static  function getPage($page = 1 ,$itensPerPage =8)
    {
        $start = ($page - 1) * $itensPerPage;
        $sql = new Sql();
        $results = $sql->select("SELECT SQL_CALC_FOUND_ROWS * 
                                FROM tb_categoria
                                ORDER BY nme_categoria 
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
        $results = $sql->select("SELECT SQL_CALC_FOUND_ROWS * 
                               FROM tb_categoria
                                WHERE nme_categoria LIKE :search
                                ORDER BY nme_categoria 
                                LIMIT $start,$itensPerPage
                                ",[
            ':search'=>'%'.$search.'%'
        ]);
        $resultsTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal");

        return [
            'data'=>$results,
            'total'=>(int)$resultsTotal[0]["nrtotal"],
            'pages'=>ceil($resultsTotal[0]["nrtotal"] / $itensPerPage)

        ];
    }

}
