<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:42
 */

namespace tresrisolution\Classes;

use \PDO;

class ConexoesDB
{
    private $instance;

//    private function __construct() {}

    public function getInstance($ambiente)
    {
        try {
            $this->instance = new PDO("pgsql:".$this->ambienteInit($ambiente));
            $this->instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->instance->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
//                echo "Conectou no ".$ambiente;
        }catch (\PDOException $e){
            echo "Erro com a conexão: ".$e->getTraceAsString();exit();
        }

        return $this->instance;
    }
    public function ambienteInit($ambiente){

        switch($ambiente){
            case 'local':
                return $this->getcfg('localhost.cfg');
                break;

            case 'cotacoes':
                return $this->getcfg('cotacoes_brt.cfg');
                break;

            case 'intranet':
                return $this->getcfg('intranet_brt.cfg');
                break;

            case 'intranetremoto':
                return $this->getcfg('intranet_remoto.cfg');
                break;
        }
    }

    public function getcfg($nome)
    {
        if (($hf=fopen(ROOT.DS.'Config'.DS.$nome, 'r'))!=FALSE)
        {
            $linha=fgets($hf);
            fclose($hf);
            return $linha;
        }
        return '';
    }

}