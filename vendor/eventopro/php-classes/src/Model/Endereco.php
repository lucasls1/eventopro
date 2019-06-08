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


class  Endereco extends  Model{
    protected $fields = [
        "pk_endereco", "fk_pessoa", "end_endereco", 'cpt_complemento', 'des_distrito', 'cid_cidade', 'est_estado',
        'pais_pai', 'nr_numero', 'cep_cep'
    ];
    const SESSION_ERROR = "AddressError";

public static function getCEP($nrcep)
{
    $nrcep = str_replace("-","",$nrcep);

    $ch = curl_init();

    curl_setopt($ch,CURLOPT_URL,"http://viacep.com.br/ws/$nrcep/json/");

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);

    $data = json_decode(curl_exec($ch),true);

    curl_close($ch);
    return $data;
}

public  function loadFromCEP($nrcep)
{
    $data = Endereco::getCEP($nrcep);

    if(isset($data['logradouro']) && $data['logradouro'])
    {
        $this->setend_endereco($data['logradouro']);
        $this->setcpt_complemento($data['complemento']);
        $this->setdes_distrito($data['bairro']);
        $this->setcid_cidade($data['localidade']);
        $this->setest_estado($data['uf']);
        $this->setpais_pais('Brasil');
        $this->setcep_cep($nrcep);

    }
}

public function save()
{
    $sql = new Sql();

    $results = $sql->select("CALL sp_endereco_save(:pk_endereco,:fk_pessoa,:end_endereco,:nr_numero,:cpt_complemento,
                                        :cid_cidade,:est_estado,:pais_pais,:cep_cep,:des_distrito)",[
                                            ':pk_endereco'=>     $this->getpk_endereco(),
                                            ':fk_pessoa'=>       $this->getpk_pessoa(),
                                            ':end_endereco'=>    $this->getend_endereco(),
                                            ':nr_numero'=>       $this->getnr_numero(),
                                            ':cpt_complemento'=> $this->getcpt_complemento(),
                                            ':cid_cidade'=>      $this->getcid_cidade(),
                                            ':est_estado'=>      $this->getest_estado(),
                                            ':pais_pais'=>       $this->getpais_pais(),
                                            ':cep_cep'=>         $this->getcep_cep(),
                                            ':des_distrito'=>    $this->getdes_distrito()
    ]);

    if(count($results) > 0)
    {
        $this->setData($results[0]);
    }


}

    public static function setMsgError($msg)
    {
        $_SESSION[Endereco::SESSION_ERROR] = $msg;
    }
    public static function getMsgError()
    {
        $msg = (isset($_SESSION[Endereco::SESSION_ERROR])) ? $_SESSION[Endereco::SESSION_ERROR] : "";
        Endereco::clearMsgError();
        return $msg;
    }
    public static function clearMsgError()
    {
        $_SESSION[Endereco::SESSION_ERROR] = NULL;
    }





}
