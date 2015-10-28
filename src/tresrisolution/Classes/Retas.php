<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:57
 */

namespace tresrisolution\Classes;


class Retas
{
    private $conn;

    public function getRetas(Request $request)
    {
//    echo "Entrou no method getRetas";exit;
        $this->conn = new ConexoesDB();

        if( !$this->conn )
        {
            echo "Nao pude recuperar Retas por nao conseguir conexao com o banco INTRANET";
            return "";
        }

        $xml = "";

        try
        {
            if( $this->conn )
            {
                $usuario = $request->getParameter("u");
                $empresa = $request->getParameter("e");
                $ativo   = $request->getParameter("ativo");
                $periodo = $request->getParameter("periodo");

                $sql = "SELECT data1, data2, hora1, hora2, val1, val2, texto, tipo, alerta, acionado, estudo, posicao_texto, id_anotacao
						FROM retas_java
						WHERE cd_empresa 	= $empresa
						AND lower(ativo) 	= '$ativo'
						AND periodo 		= '$periodo'
						AND usuario 		= '$usuario'";

                $stmt = $this->conn->getInstance('intranet')->prepare($sql);;
                $stmt->execute();
                $result = $stmt->fetchAll(\PDO::FETCH_OBJ);

                $xml .= "<retas>";

                $xml .= "<u>" . $usuario . "</u>";
                $xml .= "<a>" . $ativo . "</a>";
                $xml .= "<p>" . $periodo . "</p>";

                foreach($result as $rs)
                {
                    $xml .= "<reta>";
                    $xml .= "<d1>" . $rs->data1 . "</d1>";
                    $xml .= "<d2>" . $rs->data2 . "</d2>";
                    $xml .= "<h1>" . $rs->hora1 . "</h1>";
                    $xml .= "<h2>" . $rs->hora2 . "</h2>";
                    $xml .= "<v1>" . $rs->val1  . "</v1>";
                    $xml .= "<v2>" . $rs->val2  . "</v2>";

                    $texto = $rs->texto;

                    if( preg_match('/</',$texto) ) {
                        $texto = preg_replace("/</", "abretag", $texto);
                    }
                    $xml .= "<te>" . $texto . "</te>";

                    $xml .= "<ti>" . $rs->tipo  . "</ti>";
                    $xml .= "<al>" . $rs->alerta  . "</al>";
                    $xml .= "<ac>" . $rs->acionado  . "</ac>";
                    $xml .= "<es>" . $rs->estudo  . "</es>";

                    $xml .= "<pt>" . $rs->posicao_texto  . "</pt>";
                    $xml .= "<ia>" . $rs->id_anotacao  . "</ia>";

                    $xml .= "</reta>";
                }

                $xml .= "</retas>";

            }
            else
                echo "Não pude recuperar retas por não estar conectado ao banco Intranet";
        }
        catch(\PDOException $e)
        {
            echo "Erro ao tentar recuperar retas";
            $e->getTraceAsString();
        }

        return $xml;
    }

    public function salvaRetas(Request $request)
    {
        $this->conn = new ConexoesDB();
        echo "Entramos no método salvaRetas";exit;
        if( !$this->conn )
        {
            echo "Nao pude salvar Retas por nao conseguir conexao com o banco INTRANET";
            return "";
        }

        $xml = "";

        try
        {
            if( $this->conn )
            {

                $existe = false;

                $usuario = $request->getParameter("u");
                $empresa = $request->getParameter("e");
                $ativo   = $request->getParameter("ativo");
                $periodo = $request->getParameter("periodo");
                $analista = $request->getParameter("analista")	!= null && $request->getParameter("analista") == "1";

                // salva $ativo anterior do gráfico
                $ativoAnt   = $request->getParameter("ativoant");
                $periodoAnt = $request->getParameter("periodoant");
                if( $ativoAnt != null && !$ativoAnt == "")
                {
                    $ativo = $ativoAnt;
                    $periodo = $periodoAnt;
                }

                $data1[] 	= $request->getParameter("d1") != null ? explode(';',$request->getParameter("d1")) : null;
                $hora1[] 	= $request->getParameter("h1") != null ? explode(';',$request->getParameter("h1")) : null;
                $val1[]  	= $request->getParameter("v1") != null ? explode(';',$request->getParameter("v1")) : null;
                $data2[] 	= $request->getParameter("d2") != null ? explode(';',$request->getParameter("d2")) : null;
                $hora2[] 	= $request->getParameter("h2") != null ? explode(';',$request->getParameter("h2")) : null;
                $val2[]  	= $request->getParameter("v2") != null ? explode(';',$request->getParameter("v2")) : null;
                $texto[] 	= $request->getParameter("tx") != null ? explode(';',$request->getParameter("tx")) : null;
                $tipo[]  	= $request->getParameter("tp") != null ? explode(';',$request->getParameter("tp")) : null;

                $alerta[]   = null;
                $acionado[] = null;

                $estudo[] = null;

                $posicao[] = null;
                $id[] = null;

                if( $request->getParameter("al") != null )
                {
                    $alerta[]   = explode(";",$request->getParameter("al"));
                    $acionado[] = explode(";",$request->getParameter("ac"));
                }

                if( $request->getParameter("es") != null )
                    $estudo[]   = explode(";",$request->getParameter("es"));

                if( $request->getParameter("pt") != null )
                {
                    $posicao 	= explode(";",$request->getParameter("pt"));
                    $id[]      	= explode(";",$request->getParameter("ia"));
                }

                $xml .= "DELETE FROM retas_java WHERE usuario = '$usuario'
							AND cd_empresa = '$empresa'
							AND ativo = '$ativo'
							AND periodo = '$periodo'";

                $stmt = $this->conn->getInstance('intranet')->prepare($xml);
                $retorno = $stmt->execute();
                //Terá que executar em outro ambiente, ainda não foi implementado
                try{$stmt->execute();}catch(\PDOException $e){$e->getTraceAsString();}

                for( $i = 0; $data1 != null && $i < count($data1); $i++ )
                {
                    $xml = "";
                    $xml .= "INSERT INTO retas_java VALUES
                                                        (
                                                          ,'$usuario'
                                                          ,'xxxxxxxx'
                                                          ,'$empresa'
                                                          ,'$ativo'
                                                          ,'$periodo'
                                                          ,'now()'
                                                          ,'$data1[$i]'
                                                          ,'$data2[$i]'
                                                          ,'$hora1[$i]'
                                                          ,'$hora2[$i]'
                                                          ,'$val1[$i]'
                                                          ,'$val2[$i]'
                                                          ,'$texto[$i]'
                                                          ,'$texto[$i]'
                                                          ,'$tipo[$i]'
                                                          ,'$alerta[$i]'
                                                          ,'($acionado[$i] == \"1\") ? \"t\" : \"f\"'
                                                          ,'count($estudo) > $i) ? $estudo[$i] : \"\"'
                                                          ,null
                                                          ,null
                                                        )";

                    if( $posicao != null && count($posicao) > $i )
                    {
                        $xml .= $posicao[$i];
                        $xml .= $id[$i];
                    }
                    else
                    {
                        $xml .= '1';
                        $xml .= '-1';
                    }

                    $retorno = $stmt->execute();


                    if( $analista && $this->conn )
                        //Terá que executar em outro ambiente, ainda não foi implementado
                        try{$stmt->execute();}catch(\PDOException $e){$e->getTraceAsString();}
                }

                $xml = "<$retorno>" . $retorno . "</$retorno>";
            }
            else
                echo "Não pude salvar retas por não estar conectado ao banco Intranet";
        }
        catch(\SQLiteException $e)
        {
            //echo "Erro ao tentar salvar retas: " . sb.to());
            $e->getTraceAsString();
        }

        return $xml;
    }

    public function excluiRetas(Request $request)
    {
        echo 'Entrou no method excluiRetas';exit;
        if( !$this->conn )
        {
            echo "Nao pude excluir Retas por nao conseguir conexao com o banco INTRANET";
            return "";
        }

        $xml = "";

        try
        {
            if( $this->conn )
            {
                $usuario = $request->getParameter("u");
                $empresa = $request->getParameter("e");
                $ativo   = $request->getParameter("ativo");
                $periodo = $request->getParameter("periodo");
                $analista = $request->getParameter("analista")	!= null && $request->getParameter("analista") == "1";

                // exclui retas do $ativo anterior do gráfico
                $ativoAnt   = $request->getParameter("ativoant");
                $periodoAnt = $request->getParameter("periodoant");
                if( $ativoAnt != null && !empty($ativoAnt ))
                {
                    $ativo = $ativoAnt;
                    $periodo = $periodoAnt;
                }

                $sql = "DELETE FROM retas_java WHERE usuario = '$usuario'".
                    " AND cd_empresa = $empresa" .
                    " AND ativo = '$ativo'"  .
                    " AND periodo = '$periodo'?";
                $stmt = $this->conn->getInstance('intranet')->prepare($sql);


                $retorno = $stmt->execute();

                //Terá que executar em outro ambiente, ainda não foi implementado
                try{$stmt->execute();}catch(\PDOException $e){$e->getTraceAsString();}

                $xml = "<$retorno>" . $retorno . "</$retorno>";
            }
            else
                echo "Não pude excluir retas por não estar conectado ao banco Intranet";
        }
        catch(\PDOException $e)
        {
            echo "Erro ao tentar excluir retas";
        }

        return $xml;
    }

}