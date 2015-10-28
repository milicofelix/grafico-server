<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:45
 */

namespace tresrisolution\Classes;


class Corretoras
{
    private $conn;

    public function __construct(){

    }

    public function getListaCorretoras(Request $request)
    {

        if( !$this->conn )
        {
            echo "Nao pude recuperar Lista de Corretoras por nao conseguir conexao com o banco COTACOES";

            return "";
        }

        $xml = "";

        $statementCotacoes = null;
        $rs = null;

        try
        {
            $datas = array();

            $sql = "SELECT cd_corretora, nm_corretora FROM corretora ORDER BY nm_corretora ASC ";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchObject();

            $xml .="<corretoras>";

            foreach($result as $rs)
            {
                $xml .= "<corretora>";
                $xml .= $rs->nm_corretora . " (" . $rs->cd_corretora . ")";
                $xml .= "</corretora>";
            }

            $xml .= "</corretoras>";

        }
        catch(\PDOException $e)
        {
            echo "Erro ao tentar recuperar lista de corretoras.";
        }

        return $xml;
    }

}