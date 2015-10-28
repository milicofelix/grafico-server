<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:54
 */

namespace tresrisolution\Classes;


class Noticia
{
    private $conn;

    public function __construct(){
    }

    public function getNoticias(Request $request)
    {
        if( !$this->conn )
        {
            echo "Nao pude recuperar Noticias por nao conseguir conexao com o banco COTACOES";
            return "";
        }

        $xml = "";

        $statementCotacoes = null;
        $rs = null;

        try
        {
            $sql = "SELECT replace(manchete,'&','eComercial') AS manchete, cd_feeder, dh_noticia
                      FROM noticia
                      WHERE cd_feeder=6
                      AND dh_noticia::date >= now()::date-2 ORDER BY dh_noticia ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchObject();

            foreach($result as $rs)
            {
                $xml .= "<noticia>";
                try
                {
                    $xml .= "<manchete>" . utf8_decode($rs->manchete) . "</manchete>";
                }
                catch(\PDOException $e) {
                    $e->getTraceAsString();
                }
                $xml .= "<cd_feeder>" . $rs->cd_feeder . "</cd_feeder>";
                $xml .= "<dh_noticia>" . $rs->dh_noticia . "</dh_noticia>";
                $xml .= "</noticia>";
            }

        }
        catch(\PDOException $e)
        {

            echo "Erro ao tentar recuperar Noticias";
        }

        return $xml;
    }

}