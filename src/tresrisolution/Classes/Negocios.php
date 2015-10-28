<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:53
 */

namespace tresrisolution\Classes;


class Negocios
{
    private $conn;

    public function __construct(){

    }

    public function getNegocios(Request $request)
    {
        //Connection connectionCotacoes = conexaoBancoCotacoes();

        if( !$this->conn )
        {
            echo "Nao pude recuperar Negócios por nao conseguir conexao com o banco COTACOES";

            return "";
        }

        $xml = "";
        $statementCotacoes = null;
        $rs = null;

        try
        {
            //if( connectionCotacoes != null && !connectionCotacoes.isClosed() /*&& statementCotacoes != null*/ )
            {
                $ativo = strtoupper($request->getParameter("ativo"));
                $bolsa = $request->getParameter("bolsa");
                $delay = $request->getParameter("delay");
                $data  = $request->getParameter("data");

                $sql = "SELECT dh, preco, qtd, cd_corr_compra, cd_corr_venda, seq, vft " .
                "FROM " . ($bolsa == "2") ? "NEGOCIO_BMF" : "NEGOCIO_BOVESPA" . " " .
                "WHERE codigo = ? AND dh::date = ? " .
                ($delay == "1") ? "AND dh < now() - interval '00:15:00' " : "" .
                    "ORDER BY seq DESC";

                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(1,$ativo);
                $stmt->bindValue(2,$data);
                $stmt->execute();

                if( !$rs = $stmt->nextRowset() )
                {
                    $sql = "SELECT dh, preco, qtd, cd_corr_compra, cd_corr_venda, seq, vft " .
                    "FROM " . ($bolsa == "2") ? "NEGOCIO_BMF_PERIODO" : "NEGOCIO_BOVESPA_PERIODO" . "".
                    "WHERE codigo = '" . $ativo .
                    "' AND " ."dh::date = '" . $data . "' " .
                    ($delay == "1") ? "and dh < now() - interval '00:15:00' " : "" .
                        "ORDER BY seq DESC";

                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindValue(1,$ativo);
                    $stmt->bindValue(2,$data);
                    $stmt->execute();
                }

                else
                    $rs->beforeFirst();///Verifica no PHP a forma de aplicar essa função########################################################################################################################

                $xml .= "<negocios>";

                while( $rs->next() )
                {
                    $xml .= "<negocio>";
                    $xml .= "<d>" . $rs->dh . "</d>";
                    $xml .= "<p>" . $rs->preco . "</p>";
                    $xml .= "<q>" . $rs->qtd . "</q>";
                    $xml .= "<c>" . $rs->cd_corr_compra . "</c>";
                    $xml .= "<v>" . $rs->cd_corr_venda  . "</v>";
                    $xml .= "<s>" . $rs->seq . "</s>";
                    $xml .= "<t>" . $rs->vft . "</t>";
                    $xml .= "</negocio>";
                }

                $xml .= "</negocios>";

            }

        }
        catch(\PDOException $e)
        {
            //e.printStackTrace();
            echo"Erro ao tentar recuperar negocios";
        }

        return $xml;
    }

    public function getdatasNegocios(Request $request)
    {
        //Connection connectionCotacoes = conexaoBancoCotacoes();

        if( !$this->conn )
        {
            echo "Nao pude recuperar Negócios por nao conseguir conexao com o banco COTACOES";
            return "";
        }

        $xml = "";

        $statementCotacoes = null;
        $rs = null;

        try
        {
            //if( connectionCotacoes != null && !connectionCotacoes.isClosed() /*&& statementCotacoes != null*/ )
            {
                $datas = new \ArrayObject();//Set datas = new TreeSet<String>(); Verificar como implementar no PHP###########################################################

                $sql = "SELECT data FROM negocio_data ORDER BY data DESC ";

                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchObject();

                foreach($result as $rs)
                    $datas->add($rs->data);

                $sql = "SELECT dh::date AS data FROM negocio_bovespa ORDER BY data DESC ";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchObject();

                foreach($result as $rs)
                    $datas->add($rs->data);

                $xml .= "<$datas>";

                for( $i = $datas->iterator(); $i->hasNext(); )
                {
                    $xml .= "<data>";
                    $xml .= $i->next();
                    $xml .= "</data>";
                }

                $xml .= "</datas>";
            }
            //else
            //	echo"Não pude recuperar $datas de negócios por não estar conectado ao banco Cotações");
        }
        catch(\PDOException $e)
        {
            //e.printStackTrace();
            echo "Erro ao tentar recuperar datas de negocios";
        }

        return $xml;
    }

}