<?php 

namespace EventoPro\Model;

use \EventoPro\Model;
use \EventoPro\DB\Sql;
use \EventoPro\Mailer;

class User extends Model {

	const SESSION = "User";
	const SECRETKEY = "#";

public static function login($login, $password):User
	{

		$db = new Sql();

		$results = $db->select("SELECT * FROM tb_usuario WHERE usr_login = :LOGIN", array(
			":LOGIN"=>$login
		));

		if (count($results) === 0) {
			throw new \Exception("Não foi possível fazer login.");
		}

		$data = $results[0];

		if (password_verify($password, $data["pwd_senha"])) {

			$user = new User();
			$user->setData($data);
			
			$_SESSION[User::SESSION] = $user->getValues();
			
			return $user;

		} else {

			throw new \Exception("Não foi possível fazer login.");

		}

	}

	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}

	public static function verifyLogin($inadmin = true)
	{

		if (
			!isset($_SESSION[User::SESSION])
			|| 
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["pk_usuario"] > 0
			||
			(bool)$_SESSION[User::SESSION]["pk_usuario"] !== $inadmin
		) {
			
			header("Location: /admin/login");
			exit;

		}

	}

	public static function listAll(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_usuario a INNER JOIN tb_pessoa b ON a.fk_pessoa = b.pk_pessoa ORDER BY b.nme_pessoa");

	}

	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_usuario_save(:nme_pessoa, :usr_login, :pwd_senha, :eml_email, :nrphone, :adm_inadim)",array(
				":nme_pessoa"  =>   $this->getnme_pessoa(),
				":usr_login"  =>    $this->getusr_login(),
				":pwd_senha"  =>    $this->getpwd_senha(),
				":eml_email"  =>    $this->geteml_email(),
				":nrphone"  =>     $this->getnrphone(),
				":adm_inadim"  =>     $this->getadm_inadim()
			));
		
		$this->setData($results[0]);
	}

	public function get($idusuario){

		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_usuario a INNER JOIN tb_pessoa b ON a.fk_pessoa = b.pk_pessoa WHERE a.pk_usuario = :pk_usuario",
			array(
				":pk_usuario"=>$idusuario
			));
		$this->setData($results[0]);
	}

	public function update(){

			$sql = new Sql();

		$results = $sql->select("CALL sp_updateusuario_save(:pk_usuario, :nme_pessoa, :usr_login, :pwd_senha, :eml_email, :nrphone, :adm_inadim)",array(
				":pk_usuario" => $this->getpk_usuario(),
				":nme_pessoa"  =>   $this->getnme_pessoa(),
				":usr_login"  =>    $this->getusr_login(),
				":pwd_senha"  => $this->getpwd_senha(),
				":eml_email"  =>    $this->geteml_email(),
				":nrphone"  =>     $this->getnrphone(),
				":adm_inadim"  =>     $this->getadm_inadim()
			));
		
		$this->setData($results[0]);
	}

	public function delete(){
		$sql = new Sql();

		$sql->query("CALL sp_usuario_delete(:pk_usuario)",array(
			":pk_usuario" =>$this->getpk_usuario()

		));
	}

	public static function encrypt_decrypt($action, $string) {
        $output         = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key     =  User::SECRETKEY;
        $secret_iv      = User::SECRETKEY;
        // hash
        $key = hash('sha256', $secret_key);
 
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

	public static function getForgot($email){

		$sql = new Sql();

			$results = $sql->select("SELECT * 
				FROM  tb_pessoa a
				INNER JOIN tb_usuario b USING(idpessoa)
				where a.desemail = :email",array(
					":email"=>$email
				));
				if(count($results) === 0){
					throw new \Exception("Não foi possivel recuperar a senha.");
					
				}
				else{
					$data = $results[0];
				$results2 = $sql->select("CALL sp_usuariorecuperasenha_create(:idusuario,:desip)",array(
					":idusuario"=>$data["idusuario"],
					":desip"=>$_SERVER["REMOTE_ADDR"]
				));
				if(count($results2)===0){

					throw new \Exception("Não foi posssivel recuperar a senha");
					
				}
				else{
					$dataRecovery = $results2[0];
					 $code = User::encrypt_decrypt('encrypt', $dataRecovery["idrecovery"]);
					 $link = "http://www.eventopro.com.br/admin/forgot/reset?code=$code";
					 $mailer = new Mailer($data["desemail"],$data["desperson"],"Redefinir senha","forgot",array(
						"name"=>$data["desperson"],
						"link"=>$link

					));
					$mailer->send();

					return $data;

				}
				}

	}

	public static function validForgotDecrypt($code){
		
		 $idrecovery = User::encrypt_decrypt('decrypt', $code);
		
	     	$sql = new Sql();
	     	$results = $sql->select("
	         SELECT *
	         FROM tb_logrecuperacaosenhausuario a
	         INNER JOIN tb_usuario b USING(idusuario)
	         INNER JOIN tb_pessoa c USING(idpessoa)
	         WHERE
	         a.idrecovery = :idrecuperacao
	         AND
	         a.dtrecovery IS NULL
	         AND
	         DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW()",array(

	         	":idrecuperacao"=>$idrecovery
	         ));
				
  
    	    		if(count($results)===0){
    	    			throw new \Exception("Não foi possivel recuperar a senha.");
    	    			
    	    		}
    	    		else {
    	    			return $results[0];

    	    		}


	}
	public function setForgotUsed($idrecuperacao){

		$sql = new Sql();

		$sql->query("UPDATE tb_logrecuperacaosenhausuario SET dtrecuperacao = NOW() WHERE idrecuperacao = :idrecuperacao",array(
			":idrecuperacao"=> $idrecuperacao

		));


	}
	public function setPassword($senha){

		$sql = new Sql();
		$sql->query("UPDATE tb_usuario SET dessenha = :senha WHERE idusuario = :idusuario",array(

			":senha"=>$senha,
			":idusuario" => $this->getidusuario()
		));
	}

}

 ?>