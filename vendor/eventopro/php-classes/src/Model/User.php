<?php
/**
 * Created by PhpStorm.
 * User: XLS
 * Date: 16/05/2019
 * Time: 22:34
 */
namespace EventoPro\Model;
use \EventoPro\DB\Sql;
use \EventoPro\Model;
use \EventoPro\Mailer;
class  User extends  Model{
    const SESSION = "User";
    const SECRETKEY = "Evento_Pro_Secret";
    const ERROR = "UserError";
    const ERROR_REGISTER = "UserErrorRegister";
    const SUCCESS = "UsersSuccess";

    public static function getFromSession()
    {
        $user = new User();
        if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['pk_usuario'] > 0) {
            $user->setData($_SESSION[User::SESSION]);
        }
        return $user;
    }
    public static function checkLogin($inadmin = true)
    {
        if (
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["pk_usuario"] > 0
        ) {
            //Não está logado
            return false;
        } else {
            if ($inadmin === true && (bool)$_SESSION[User::SESSION]['adm_inadim'] === true) {
                return true;
            } else if ($inadmin === false) {
                return true;
            } else {
                return false;
            }
        }

    }
    public static  function login($login,$password){

        $sql = new Sql();

       $results = $sql->select("SELECT * FROM tb_usuario a INNER JOIN tb_pessoa b ON a.fk_pessoa = b.pk_pessoa
                  WHERE usr_login = :LOGIN",array(
            ":LOGIN"=>$login

        ));

        if (count($results) === 0){
            throw new \Exception("usuario inexistente ou senha invalida.");
        }
        $data = $results[0];

        if (password_verify($password,$data["pwd_senha"])===true){

            $user = new User();
            $user->setData($data);
            $_SESSION[User::SESSION] = $user->getValues();
            return $user;
        }else{
            throw new \Exception("usuario inexistente ou senha invalida.");
        }

    }
    public static function verifyLogin($inadmin = true)
    {

        if (!User::checkLogin(($inadmin))) {
            if($inadmin)
            {
                header("Location: /admin/login");
            }else
            {
                header("Location: /login");
            }
            exit;

        }

    }
    public  static function  logout()
    {
        $_SESSION[User::SESSION] = NULL;
       // session_destroy();
    }
    public static function  listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tb_usuario a INNER JOIN tb_pessoa b  ON a.fk_pessoa = b.pk_pessoa ORDER BY b.nme_pessoa");

    }
    public  function  save()
    {

        $sql = new Sql();
       $result = $sql->select("CALL sp_usuario_save(:nme_pessoa,:usr_login,:pwd_senha,:eml_email,:nrphone,:adm_inadim)",array(
           ":nme_pessoa"=>$this->getnme_pessoa(),
           ":usr_login"=> $this->getusr_login(),
           ":pwd_senha"=>User::getPasswordHash($this->getpwd_senha()),
           ":eml_email"=>$this->geteml_email(),
           ":nrphone"=>$this->getnrgone(),
           "adm_inadim"=>$this->getadm_inadim()

        ));
       $this->setData($result[0]);
    }
    public  function  get($pk_usuario)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_usuario a INNER JOIN tb_pessoa b ON a.fk_pessoa = b.pk_pessoa WHERE a.pk_usuario = :pk_usuario",array(
            ":pk_usuario"=>$pk_usuario
        ));
        $this->setData($results[0]);
    }
    public  function update()
    {
        $sql = new Sql();
        $result = $sql->select("CALL sp_updateusuario_save(:pk_usuario,:nme_pessoa,:usr_login,:pwd_senha,:eml_email,:nrphone,:adm_inadim)",array(
            ":pk_usuario"=>$this->getpk_usuario(),
            ":nme_pessoa"=>$this->getnme_pessoa(),
            ":usr_login"=> $this->getusr_login(),
            ":pwd_senha"=>User::getPasswordHash($this->getpwd_senha()),
            ":eml_email"=>$this->geteml_email(),
            ":nrphone"=>$this->getnrgone(),
            "adm_inadim"=>$this->getadm_inadim()

        ));
        $this->setData($result[0]);
    }

    public function delete()
    {
        $sql = new Sql();

        $sql->query("CALL sp_usuario_delete(:pk_usuario)",array(
            ":pk_usuario"=>$this->getpk_usuario()
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
    public static  function getForgot($email, $inadmin = true)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM 
                    tb_pessoa a
                    INNER JOIN tb_usuario b 
                    ON a.pk_pessoa = b.fk_pessoa
                    where a.eml_email = :eml_email",array(
                        ":eml_email"=>$email
        ));
        if (count($results)  === 0)
        {
            throw new \Exception("Não Foi possivel recuperar a senha.");
        }else
        {
            $data = $results[0];
           $results2 =  $sql->select("CALL sp_usuariorecuperasenha_create(:fk_usuario, :dsc_ip)",array(
                ":fk_usuario"=>$data["pk_usuario"],
                ":dsc_ip"=>$_SERVER["REMOTE_ADDR"]
            ));
           if (count($results2)===0)
           {
               throw new \Exception("Não Foi possivel recuperar a senha.");
           }else
           {
               $dataRecovery = $results2[0];
               $code = User::encrypt_decrypt('encrypt', $dataRecovery["pk_recuperacao"]);
               if ($inadmin === true)
               {

               $link = "http://www.eventopro.com.br/admin/forgot/reset?code=$code";
               }else
               {
                   $link = "http://www.eventopro.com.br/forgot/reset?code=$code";
               }
               $mailer = new Mailer($data["eml_email"],$data["nme_pessoa"],"Redefinir senha","forgot",array(
                   "name"=>$data["nme_pessoa"],
                   "link"=>$link

               ));
               $mailer->send();

               return $data;


           }
        }
    }
    public  function  validForgotDecrypt($code)
    {
        $idrecovery = User::encrypt_decrypt('decrypt', $code);

        $sql = new Sql();
        $results = $sql->select("
	          SELECT *
              FROM tb_logrecuperacaosenhausuario a
	         INNER JOIN tb_usuario b ON a.fk_usuario = b.pk_usuario
	         INNER JOIN tb_pessoa c ON b.fk_pessoa = c.pk_pessoa
	         WHERE
	         a.pk_recuperacao = :pk_recuperacao
	         AND
	         a.dti_recuperacao IS NULL
	         AND
	         DATE_ADD(a.dti_solicitacao, INTERVAL 1 HOUR) >= NOW();",array(

            ":pk_recuperacao"=>$idrecovery
        ));


        if(count($results)===0){
            throw new \Exception("Não foi possivel recuperar a senha.");

        }
        else {
            return $results[0];

        }

    }
    public  static  function  setForgotUsed($pk_recuperacao)
    {
        $sql = new Sql();
        $sql->query("UPDATE tb_logrecuperacaosenhausuario SET dti_recuperacao = NOW() WHERE pk_recuperacao =:pk_recuperacao",
            array(
                ":pk_recuperacao"=>$pk_recuperacao
            ));
    }
    public  function  setSenha($pwd_senha)
    {
        $sql = new Sql();
        $sql->query("UPDATE tb_usuario SET pwd_senha = :pwd_senha WHERE pk_usuario =:pk_usuario",array(
            ":pwd_senha"=>$pwd_senha,
            ":pk_usuario"=>$this->getpk_usuario()
        ));
    }

    public static function setError($msg)
    {
        $_SESSION[User::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';
        User::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[User::ERROR] = NULL;
    }
    public static function setSuccess($msg)
    {
        $_SESSION[User::SUCCESS] = $msg;
    }
    public static function getSuccess()
    {
        $msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS]) ? $_SESSION[User::SUCCESS] : '';
        User::clearSuccess();
        return $msg;
    }
    public static function clearSuccess()
    {
        $_SESSION[User::SUCCESS] = NULL;
    }
    public static function setErrorRegister($msg)
    {
        $_SESSION[User::ERROR_REGISTER] = $msg;
    }
    public static function getErrorRegister()
    {
        $msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : '';
        User::clearErrorRegister();
        return $msg;
    }
    public static function clearErrorRegister()
    {
        $_SESSION[User::ERROR_REGISTER] = NULL;
    }
    public static function checkLoginExist($login)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_usuaurio WHERE usr_login = :deslogin", [
            ':deslogin'=>$login
        ]);
        return (count($results) > 0);
    }
    public static function getPasswordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, [
            'cost'=>12
        ]);
    }


}

?>