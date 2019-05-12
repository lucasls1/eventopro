<?php 

namespace EventoPro\Model;

use \EventoPro\Model;
use \EventoPro\DB\Sql;

class Category extends Model {



	public static function listAll(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categoria ORDER BY nme_categoria");


	}
	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_categoria_save(:pk_categoria, :nme_categoria)",array(
				":pk_categoria"  =>   $this->getpk_categoria(),
				":nme_categoria"  =>    $this->getnme_categoria()
			));
			
		$this->setData($results[0]);
		Category::updateFile();
	}

	public function get($idcategoria){

		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_categoria WHERE pk_categoria = :pk_categoria",array(

			":pk_categoria"=>$idcategoria
		));

		$this->setData($results[0]);

	}
	public function delete(){

		$sql = new Sql();
		$sql->query("DELETE FROM tb_categoria WHERE pk_categoria = :pk_categoria",array(
			":pk_categoria"=> $this->getpk_categoria()
		));
		Category::updateFile();

	}

	public static function updateFile(){
		$categories =Category::listAll();
		$html = [];
		foreach ($categories as $row ) {
			# code...
			array_push($html,'<li><a href="/categories/'.$row['pk_categoria'].'">'.$row['nme_categoria'].'</a></li>');
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categoryes-menu.html", implode("", $html));

	}
	public function getEventos($related = true){

	$sql = new Sql();
	if($related === true){
		return $sql->select("	SELECT * FROM tb_evento WHERE pk_evento IN(
					      SELECT a.pk_evento 
					    FROM tb_evento a 
					    INNER JOIN tb_eventocategoria b ON a.pk_evento = b.pk_evento
					    WHERE b.pk_categoria = :pk_categoria
    
    )",[

    		':pk_categoria'=>$this->getpk_categoria()
    ]);
	}	
	else {
		return $sql->select("	SELECT * FROM tb_evento WHERE pk_evento NOT IN(
					      SELECT a.pk_evento 
					    FROM tb_evento a 
					    INNER JOIN tb_eventocategoria b ON a.pk_evento = b.pk_evento
					    WHERE b.pk_categoria = :pk_categoria
    
    )",[

    		':pk_categoria'=>$this->getpk_categoria()
    ]);

	}
	}

	public function addEvento(Product $evento){
		$sql = new Sql();

		$sql->query("INSERT INTO tb_eventocategoria (pk_categoria,pk_evento) VALUES(:pk_categoria,:pk_evento) ",[

			':pk_categoria'=>$this->getpk_categoria(),
			':pk_evento'=>$evento->getpk_evento()
		]);


	}

	public function removeEvento(Product $evento)
	{
		$sql = new Sql();
		$sql->query("DELETE FROM tb_eventocategoria WHERE pk_categoria = :pk_categoria AND pk_evento = :pk_evento", [
			':pk_categoria'=>$this->getpk_categoria(),
			':pk_evento'=>$evento->getpk_evento()
		]);

		
	}

	}

 ?>