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

class  Evento extends  Model{
    public static function  listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_evento ORDER BY nme_evento");

    }

    public  static  function  checkList($list)
    {
        foreach ($list as &$row) {
            # code...
            $p = new Evento();
            $p->setData($row);
            $row  = $p->getValues();

        }
        return $list;
    }
    public function  save()
    {
        $sql = new Sql();
        $result = $sql->select("CALL sp_evento_save(:pk_evento,:nme_evento,:vlr_inteiro,:des_descricao,:url_url)",array(
             ":pk_evento"=> $this->getpk_evento(),
             ":nme_evento"=> $this->getnme_evento(),
             ":vlr_inteiro"=> $this->getvlr_inteiro(),
             ":des_descricao"=> $this->getdes_descricao(),
             ":url_url"=> $this->geturl_url()

        ));
        $this->setData($result[0]);

    }
    public  function  get($pk_evento)
    {
        $sql = new Sql();

        $results=  $sql->select("SELECT * FROM tb_evento WHERE pk_evento = :pk_evento",[
            ":pk_evento"=>$pk_evento
        ]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query("DELETE FROM tb_evento WHERE pk_evento = :pk_evento",[
            ":pk_evento"=>$this->getpk_evento()
        ]);

    }
    public  function checkPhoto()
    {
        if (file_exists(
            $_SERVER["DOCUMENT_ROOT"]. DIRECTORY_SEPARATOR.
            "res".DIRECTORY_SEPARATOR.
            "site".DIRECTORY_SEPARATOR.
            "img".DIRECTORY_SEPARATOR.
            "eventos".DIRECTORY_SEPARATOR.
            $this->getpk_evento().".jpg"
        )){
            $url = "/res/site/img/eventos/".$this->getpk_evento().".jpg";
        }else
        {
            $url = "/res/site/img/default.jpg";
        }
        return $this->setdesphoto($url);
    }
    public  function getValues()
    {
        $this->checkPhoto();
        $values = parent::getValues();

        return $values;
    }

    public  function  setPhoto($file)
    {
        $extension = explode('.',$file['name']);
        $extension = end($extension);

        switch ($extension){

            case "jpg":
            case "jpeg":
                $image = imagecreatefromjpeg($file["tmp_name"]);
                break;
            case "gif":
                $image = imagecreatefromgif($file["tmp_name"]);
                break;
            case "png":
                $image = imagecreatefrompng($file["tmp_name"]);
                break;

        }
        $dist = $_SERVER["DOCUMENT_ROOT"]. DIRECTORY_SEPARATOR.
            "res".DIRECTORY_SEPARATOR.
            "site".DIRECTORY_SEPARATOR.
            "img".DIRECTORY_SEPARATOR.
            "eventos".DIRECTORY_SEPARATOR.
            $this->getpk_evento().".jpg";

        imagejpeg($image,$dist);
        imagedestroy($image);

        $this->checkPhoto();


    }

    public  function  getFromURL($url_url)
    {
        $sql = new Sql();
       $rows =  $sql->select("SELECT * 
                                FROM tb_evento WHERE url_url = :url_url LIMIT 1",[
                        ':url_url'=> $url_url
        ]);
       $this->setData($rows[0]);
    }

    public  function getCategoria()
    {
        $sql = new Sql();
        return  $sql->select("
        SELECT * FROM  tb_categoria a INNER JOIN tb_eventocategoria b ON a.pk_categoria = b.pk_categoria WHERE b.pk_evento = :pk_evento
        ",[
            ':pk_evento'=> $this->getpk_evento()
        ]);
    }


  }
  ?>