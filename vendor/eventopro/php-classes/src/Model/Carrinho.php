<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 18/05/2019
 * Time: 11:58
 */
namespace EventoPro\Model;
use \EventoPro\DB\Sql;
use \EventoPro\Model;
use \EventoPro\Model\User;

class  Carrinho extends  Model{
    const SESSION = "carrinho";

    public static function getFromSession()
    {
        $carrinho = new Carrinho();
        if (isset($_SESSION[Carrinho::SESSION]) && (int)$_SESSION[Carrinho::SESSION]['pk_carrinho'] >0 ){
            $carrinho->get((int)$_SESSION[Carrinho::SESSION]['pk_carrinho']);
        }else{
            $carrinho->getFromSessionID();
            if (!(int)$carrinho->getpk_carrinho() > 0){
                $data =[
                    'ids_identificacaosessao'=>session_id()
                ];
                    if (User::checkLogin(false))
                    {
                        $user = User::getFromSession();
                        $data['pk_usuario'] = $user->getpk_usuario();
                    }
                    $carrinho->setData($data);
                    $carrinho->save();
                    $carrinho->setToSession();

            }

        }
        return $carrinho;
    }
    public function setToSession()
    {
            $_SESSION[Carrinho::SESSION] = $this->getValues();
    }

    public function getFromSessionID()
    {
        $sql = new Sql();

        $result = $sql->select("SELECT * FROM tb_carrinho WHERE ids_identificacaosessao = :ids_identificacaosessao",[
            ':ids_identificacaosessao'=>session_id()
        ]);
        if (count($result) > 0) {
            $this->setData($result[0]);
        }
    }
    public function get(int $pk_carrinho)
    {
        $sql = new Sql();

        $result = $sql->select("SELECT * FROM tb_carrinho WHERE pk_carrinho = :pk_carrinho",[
            ':pk_carrinho'=>$pk_carrinho
        ]);
        if (count($result) > 0) {
            $this->setData($result[0]);
        }
    }
   public function save()
   {
       $sql = new Sql();
       $results =$sql->select("CALL sp_carrinho_save(:pk_carrinho,:ids_identificacaosessao,:fk_usuario)",[
           ':pk_carrinho' => $this->getpk_carrinho(),
           ':ids_identificacaosessao'=>$this->getids_identificacaosessao(),
           ':fk_usuario'=> $this->getfk_usuario()
       ]);

       $this->setData($results[0]);
   }
   public  function addEvento(Evento $evento)
   {
       $sql = new Sql();

       $sql->query("INSERT INTO tb_carrinhoevento (fk_carrinho, fk_evento) VALUES (:fk_carrinho,:fk_evento)",[
           'fk_carrinho'=>$this->getpk_carrinho(),
           ':fk_evento'=>$evento->getpk_evento()
       ]);
       $this->getCalculateTotal();
   }
   public  function removeEvento(Evento $evento, $all =false)
   {
       $sql = new Sql();
       if($all)
       {
           $sql->query("UPDATE tb_carrinhoevento SET dti_removido = NOW() WHERE fk_carrinho = :fk_carrinho AND fk_evento = :fk_evento AND 
            dti_removido IS NULL",[
                ':fk_carrinho'=>$this->getpk_carrinho(),
               ':fk_evento'=>$evento->getpk_evento()
           ]);
       }else
       {
           $sql->query("UPDATE tb_carrinhoevento SET dti_removido = NOW() WHERE fk_carrinho = :fk_carrinho AND fk_evento = :fk_evento AND 
            dti_removido IS NULL LIMIT 1",[
               ':fk_carrinho'=>$this->getpk_carrinho(),
               ':fk_evento'=>$evento->getpk_evento()
           ]);
       }
       $this->getCalculateTotal();
   }
   public function getEventos()
   {
       $sql = new Sql();
       $rows = $sql->select("
       SELECT b.pk_evento,b.nme_evento,b.vlr_inteiro,b.des_descricao,b.url_url,COUNT(*) AS nrqtd, SUM(b.vlr_inteiro) AS vlr_total
        FROM tb_carrinhoevento a 
       INNER JOIN tb_evento b ON a.fk_evento = b.pk_evento
       WHERE a.fk_carrinho = :fk_carrinho AND a.dti_removido IS NULL
       GROUP BY b.pk_evento,b.nme_evento,b.vlr_inteiro,b.des_descricao,b.url_url
       ORDER BY b.nme_evento
       ",[
           ':fk_carrinho'=>$this->getpk_carrinho()
       ]);
       return Evento::checkList($rows);
   }
   public  function getEventoTotal()
   {
       $sql = new Sql();
       $result = $sql->select("
                         SELECT SUM(vlr_inteiro) AS vlr_total,COUNT(*) AS nrqtd
                         FROM tb_evento a
                         INNER JOIN tb_carrinhoevento b ON a.pk_evento = b.fk_evento
                         WHERE b.fk_carrinho = :pk_carrinho AND dti_removido IS NULL
       ",[
           ':pk_carrinho'=>$this->getpk_carrinho()
       ]);
       if(count($result) > 0)
       {
           return $result[0];
       }else
       {
           return[];
       }
   }

   public  function getValues()
   {
       $this->getCalculateTotal();
       return parent::getValues();
   }
   public  function getCalculateTotal()
   {
       $total = $this->getEventoTotal();
       $this->setvlsubtotal($total['vlr_total']);
       $this->setvltotal($total['vlr_total']);
   }


}
