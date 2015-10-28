<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:52
 */

namespace tresrisolution\Classes;


class ManipulaAlertas extends GraficoServer
{
    private $conn;
    /**
     *
     */
    public function __construct(){
        $this->listAlertas = new \ArrayObject(new Alerta());
    }

    public function getAlertasAbasGrafico(Request $request)
    {
        //if( !conexaoBancoIntranet() )
        //{
        //echo "Nao pude recuperar AlertasAbasGrafico por nao conseguir conexao com o banco INTRANET");
        //return "";
        //}

        $xml = "";
        $rs = null;

        try
        {
            $usuario = $request->getParameter("u");
            $empresa = $request->getParameter("e");
            $ativo   = strtoupper($request->getParameter("a"));
            $periodo = $request->getParameter("p");

            $sql = "select ativo, periodo, val1, tipo, alerta
                              FROM retas_java
                              WHERE usuario='" . $usuario . "' and cd_empresa=" . $empresa .
                " and tipo in (51,52,53,56) and acionado = 'f'";
            if( !strstr($ativo, ",") )
                $sql .= " and upper(ativo)='" . $ativo . "' and periodo=" . $periodo;
            else
            {
                $sql .= " and (";
                $vAtivo[]   = explode(',',$ativo);
                $vperiodo[] = explode($periodo,",");
                for( $i = 0; $i < count($vAtivo); $i++ )
                    $sql .= ($i > 0 ? " or" : "") . " (upper(ativo)='" . $vAtivo[$i] . "' and periodo=" . $vperiodo[$i] . ")";
                $sql .= " )";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchObject();

            foreach($result as $rs)
            {
                $xml .= "<reta>";
                $xml .= "<a>" . strtoupper($rs->ativo) . "</a>";
                $xml .= "<p>" . $rs->periodo . "</p>";
                $xml .= "<v>" . $rs->val1 . "</v>";
                $xml .= "<t>" . $rs->tipo . "</t>";
                $xml .= "<l>" . $rs->alerta . "</l>";
                $xml .= "</reta>";
            }
        }
        catch(SQLException $e)
        {
            $e->getTraceAsString();
            echo "Erro ao tentar recuperar AlertasAbasGrafico";
        }

        return $xml;
    }

    public function getAlertas()
    {
        $xml = "";
        $listAlertas = $this->listAlertas->getIterator();
        for($i=0; $i < count($listAlertas); $i++)
        {
            $xml .="<alerta>";
            $xml .="<u>" . $listAlertas->g.get(i).usuario . "</u>";
            $xml .="<e>" . $listAlertas->get($i)->empresa . "</e>";
            $xml .="<a>" . $listAlertas->get($i)->ativo  . "</a>";
            $xml .="<p>" . $listAlertas->get($i)->periodo . "</p>";
            $xml .="<dh>" . $listAlertas->get($i)->dh     . "</dh>";
            $xml .="<d1>" . $listAlertas->get($i)->data1  . "</d1>";
            $xml .="<d2>" . $listAlertas->get($i)->data2  . "</d2>";
            $xml .="<h1>" . $listAlertas->get($i)->hora1  . "</h1>";
            $xml .="<h2>" . $listAlertas->get($i)->hora2  . "</h2>";
            $xml .="<v1>" . $listAlertas->get($i)->valor1 . "</v1>";
            $xml .="<v2>" . $listAlertas->get($i)->valor2 . "</v2>";
            $xml .="<te>" . $listAlertas->get($i)->texto  . "</te>";
            $xml .="<ti>" . $listAlertas->get($i)->tipo   . "</ti>";
            $xml .="<al>" . $listAlertas->get($i)->alerta . "</al>";
            $xml .="<ac>" . ($listAlertas->get($i)->acionado ? 1 : 0) . "</ac>";
            $xml .="</alerta>";
        }

        return $xml;
    }

    public function procuraAlertas()
    {
        if( !$this->conn )
        {
            echo "Nao pude recuperar Alertas por nao conseguir conexao com o banco INTRANET";
        }

        $rs = null;

        try
        {
            if( $this->conn )
            {
                $listAlertasTmp[] = new Alerta();

                $sql = "SELECT usuario, cd_empresa, ativo, periodo, dh_criacao, data1, data2, hora1, hora2, val1, val2, texto, tipo, alerta
                          FROM retas_java
                          WHERE acionado = 'f' AND tipo >= 100 ORDER BY ativo ASC ";

                $rs = $statementIntranet->executeQuery($sql);

                while( $rs->next() )
                {
                    $alerta = new Alerta();

                    $alerta->usuario  = $rs->usuario;
                    $alerta->empresa  = $rs->cd_empresa;
                    $alerta->ativo    = $rs->ativo;
                    $alerta->periodo  = $rs->periodo;
                    $alerta->data1    = $rs->data1;
                    $alerta->data2    = $rs->data2;
                    $alerta->hora1    = $rs->hora1;
                    $alerta->hora2    = $rs->hora2;
                    $alerta->valor1   = $rs->val1;
                    $alerta->valor2   = $rs->val2;
                    $alerta->texto    = $rs->texto;
                    $alerta->tipo     = $rs->tipo;
                    $alerta->alerta   = $rs->alerta;
                    $alerta->acionado = false;

                    try
                    {
                        $alerta->dh = date("Y-m-d H:m:s", strtotime($rs->dh_criacao));
                    }
                    catch(\PDOException $e){ $e->getTraceAsString(); }

                    if( count($listAlertasTmp) == $this->tamanhoListAL ) // aumenta a capacidade em 10% se necessário
                    {
                        $this->tamanhoListAL .= ($this->tamanhoListAL / 10);
                        $listAlertasTmp = $this->tamanhoListAL;
                    }

                    if( count($listAlertasTmp) == $this->tamanhoListAL )
                        unset($listAlertasTmp[0]);

                    array_push($listAlertasTmp,$alerta);
                }

                if( $this->conn ) //conexaoBancoIntranetRemoto
                {
                    $rs = $statementIntranetRemoto->executeQuery($sql);

                    while( $rs->next() )
                    {
                        $alerta = new Alerta();

                        $alerta->usuario  = $rs->usuario;
                        $alerta->empresa  = $rs->cd_empresa;
                        $alerta->ativo    = $rs->ativo;
                        $alerta->periodo  = $rs->periodo;
                        $alerta->data1    = $rs->data1;
                        $alerta->data2    = $rs->data2;
                        $alerta->hora1    = $rs->hora1;
                        $alerta->hora2    = $rs->hora2;
                        $alerta->valor1   = $rs->val1;
                        $alerta->valor2   = $rs->val2;
                        $alerta->texto    = $rs->texto;
                        $alerta->tipo     = $rs->tipo;
                        $alerta->alerta   = $rs->alerta;
                        $alerta->acionado = false;

                        try
                        {
                            $alerta->dh = date("Y-m-d H:m:s", strtotime($rs->dh_criacao));
                        }
                        catch(\PDOException $e){ $e->getTraceAsString(); }

                        if( count($listAlertasTmp) == $this->tamanhoListAL ) // aumenta a capacidade em 10% se necessário
                        {
                            $this->tamanhoListAL .= ($this->tamanhoListAL / 10);
                            $listAlertasTmp = $this->tamanhoListAL;
                        }

                        if( count($listAlertasTmp) == $this->tamanhoListAL )
                            unset($listAlertasTmp[0]);

                        array_push($listAlertasTmp,$alerta);
                    }
                }


                $this->listAlertas    = $listAlertasTmp;
                $listAlertasTmp = null;

            }
            else
                echo "Não pude recuperar Alertas por não estar conectado ao banco Intranet";
        }
        catch(\PDOException $e)
        {
            //e.printStackTrace();
            echo "Erro ao tentar recuperar Alertas";
        }
    }

    public function excluiAlerta(Request $request)
    {
        if( !$this->conn ) //conexaoBancoIntranet
        {
            echo "Nao pude excluir Alerta por nao conseguir conexao com o banco INTRANET";
            return "";
        }

        $xml = "";

        try
        {
            if( $this->conn )
            {
                $sql = "";

                $usuario = $request->getParameter("u");
                $empresa = $request->getParameter("e");
                $ativo   = $request->getParameter("a");
                $periodo = $request->getParameter("p");
                $tipo    = $request->getParameter("t");
                $valor   = $request->getParameter("v");
                $analista = $request->getParameter("analista")	!= null && $request->getParameter("analista") === "1";


                $sql = "DELETE FROM retas_java WHERE usuario = '"   . $usuario . "'" .
                    " AND cd_empresa = " . $empresa .
                    " AND ativo = '"  . $ativo . "'" .
                    " AND periodo = " . $periodo .
                    " AND tipo = " . $tipo .
                    " AND val1 = '" . $valor . "'";

                $xml = "<retorno>" . $statementIntranet->executeUpdate($sql) . "</retorno>";

                if( $analista && conexaoBancoIntranetRemoto() )
                    try{ $statementIntranetRemoto->executeUpdate($sql); } catch(\PDOException $e){ $e->getTraceAsString(); }

                procuraAlertas();
            }
            else
                echo "Não pude excluir Alerta por não estar conectado ao banco Intranet";
        }
        catch(\PDOException $e)
        {
            echo "Erro ao tentar excluir Alerta";
        }

        return $xml;
    }

    public function desativaAlerta(Request $request)
    {
        if( !$this->conn ) //conexaoBancoIntranet
        {
            echo "Nao pude atualizar Alerta por nao conseguir conexao com o banco INTRANET";
            return "";
        }

        $xml = "";

        try
        {
            if( $this->conn )
            {
                $sql = "";

                $usuario = $request->getParameter("u");
                $empresa = $request->getParameter("e");
                $ativo   = $request->getParameter("a");
                $periodo = $request->getParameter("p");
                $tipo    = $request->getParameter("t");
                $valor   = $request->getParameter("v");

                $sql = "UPDATE retas_java SET acionado = 't' WHERE usuario = '"   . $usuario . "'" .
                    " AND cd_empresa = " . $empresa .
                    " AND ativo = '"  . $ativo . "'" .
                    " AND periodo = " . $periodo .
                    " AND tipo = " . $tipo .
                    " AND val1 = '" . $valor . "'";

                $xml = "<retorno>" . $statementIntranet->executeUpdate($sql) . "</retorno>";

                if( $this->conn ) //conexaoBancoIntranetRemoto
                    try{ $statementIntranetRemoto->executeUpdate($sql); } catch(\PDOException $e){ $e->getTraceAsString(); }

                procuraAlertas();
            }
            else
                echo "Não pude atualizar Alerta por não estar conectado ao banco Intranet";
        }
        catch(\PDOException $e)
        {
            echo "Erro ao tentar atualizar Alerta";
        }

        return $xml;
    }

}