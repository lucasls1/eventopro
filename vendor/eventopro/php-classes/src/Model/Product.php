<?php 

namespace EventoPro\Model;

use \EventoPro\Model;
use \EventoPro\DB\Sql;

class Product extends Model {



	public static function listAll(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_evento ORDER BY nme_evento");

	}
	public static function checList($list){

		foreach ($list as &$row) {
			# code...
			$p = new Product();
			$p->setData($row);
			$row  = $p->getValues();

		}
		return $list;

	}

	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_evento_save(:pk_evento, :nme_evento, :vlr_inteiro,:vlr_meia,:url_url)",array(
				":pk_evento"  =>   $this->getpk_evento(),
				":nme_evento"  =>    $this->getnme_evento(),
				":vlr_inteiro"  =>    $this->getvlr_inteiro(),
				":vlr_meia"  =>    $this->getvlr_meia(),
				":url_url"  =>    $this->geturl_url()
			));
			$this->setData($results[0]);
		
	}

	public function get($idevento){

		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_evento WHERE pk_evento = :pk_evento",array(

			":pk_evento"=>$idevento
		));

		$this->setData($results[0]);

	}
	public function delete(){

		$sql = new Sql();
		$sql->query("DELETE FROM tb_evento WHERE pk_evento = :pk_evento",array(
			":pk_evento"=> $this->getpk_evento()
		));
		

	}

	public function checkPhoto(){

		if (file_exists(
			$_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR . 
			"img" . DIRECTORY_SEPARATOR . 
			"evento" . DIRECTORY_SEPARATOR . 
			$this->getidevento() . ".jpg"
			)) {
			
			$url = "/res/site/img/evento/" . $this->getidevento() . ".jpg";
			

			} else{
			
				$url  = "/res/site/img/default.jpg";
				//$url = "/res/site/img/evento/" . $this->getidproduct() . ".jpg";
				echo "NAO DEVERIA TA AQUI";
			
			}

			return $this->setdesphoto($url);
	}

	public function getValues(){

		$this->checkPhoto();

		$values = parent::getValues();

		return $values;

	}

	public function setPhoto($file){

		$extension = explode('.', $file['name']);
		$extension = end($extension);
		switch ($extension) {
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
		
		$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR . 
			"img" . DIRECTORY_SEPARATOR . 
			"evento".DIRECTORY_SEPARATOR.$this->getidevento().".jpg";

		imagejpeg($image, $dist);
		imagedestroy($image);
		$this->checkPhoto();

	}

	

	}

 ?>