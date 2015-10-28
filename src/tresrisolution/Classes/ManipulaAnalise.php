<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:52
 */

namespace tresrisolution\Classes;


class ManipulaAnalise extends GraficoServer
{
    protected $listAnalises;
    private $conn;

    public function __construct(){

        $this->listAnalises = new Analise();
    }


    public function getAnalisesXML($ultDH)
    {
        $xml = "";

        for( $i = 0; $i < count($this->listAnalises); $i++ )
        {
            if( $this->listAnalises->get($i)->dh > $ultDH )
            {
                $xml .="<analise>";
                $xml .="<u>" . $this->listAnalises->get($i)->usuario . "</u>";
                $xml .="<l>" . $this->listAnalises->get($i)->licenca . "</l>";
                $xml .="<e>" . $this->listAnalises->get($i)->empresa . "</e>";
                $xml .="<n>" . $this->listAnalises->get($i)->perfil  . "</n>";
                $xml .="<a>" . $this->listAnalises->get($i)->ativo   . "</a>";
                $xml .="<p>" . $this->listAnalises->get($i)->periodo . "</p>";
                $xml .="<d>" . $this->listAnalises->get($i)->dh      . "</d>";
                $xml .="<c>" . $this->listAnalises->get($i)->compartilhar . "</c>";
                $xml .="<g>" . $this->listAnalises->get($i)->gratuita . "</g>";
                $xml .="<r>" . $this->listAnalises->get($i)->grupos . "</r>";
                $xml .="</analise>";
            }
        }

        return $xml;
    }

    public function getAnalises($ultDH, $listLicAnalises = array(), $bTemAnaliseGrauita, $modulos)
    {
        $xml = "";

        for( $i = 0; $i < count($this->listAnalises); $i++ )
        {
            if( $this->listAnalises->get($i)->dh > $ultDH
                && (in_array($this->listAnalises->get($i)->licenca,$listLicAnalises)
                    || ($this->listAnalises->get($i)->gratuita && $bTemAnaliseGrauita))
                && $this->listAnalises->get($i)->compartilhar
                && ($this->listAnalises->get($i)->grupos == null || $this->modulosContemGrupo($this->listAnalises->get($i)->grupos, $modulos))
            )
            {

                $xml .=$this->listAnalises->get($i)->usuario;
                $xml .=",";
                $xml .=$this->listAnalises->get($i)->licenca;
                $xml .=",";
                $xml .=$this->listAnalises->get($i)->perfil;
                $xml .=",";
                $xml .=$this->listAnalises->get($i)->ativo;
                $xml .=",";
                $xml .=$this->listAnalises->get($i)->periodo;
                $xml .=",";
                $xml .=$this->listAnalises->get($i)->dh;
            }
        }

        return $xml;
    }

    public function procuraAnalises()
    {
        $rs = null;

        /*Ainda não foi implementado os diferente tipos de conexão*/

//    $connection = $servidorLocal != null && $servidorLocal == "localhost" ? $connectionIntranetRemoto : $connectionIntranet;
//		$statement   = $servidorLocal != null && $servidorLocal == "localhost" ? $statementIntranetRemoto : $statementIntranet;

        try
        {
            if( $this->conn )
            {
                $sql = "";

                $sql .= "SELECT c.email, l.cd_licenca, nm_perfil, ativo_referencia, periodo_ativo, dh_comentario, compartilhar, gratuito, o.cd_empresa, ";
                $sql .= "(SELECT nm_perfil from perfil_grafico_java where usuario = c.email and nm_perfil = ativo_referencia) as nm_perfil2, ";
                $sql .= "(SELECT nm_perfil from perfil_grafico_java where usuario = c.email order by dh_ult_acesso desc limit 1) as nm_perfil3, o.modulos_grupo_analise ";
                $sql .= "FROM cliente_usuario_web c, licenca_realtime l, comentario_compartilhado o WHERE ";
                $sql .= "c.cd_cliente = l.cd_cliente AND ";
                $sql .= "o.cd_licenca = l.cd_licenca ";
                if( $this->ultDHAnalise != null )
                {
                    $sql .= "AND dh_comentario > '";
                    $sql .= $this->ultDHAnalise;
                    $sql .= "' ";
                }
                $sql .="ORDER BY dh_comentario ASC, ativo_referencia ASC ";

                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchObject();

                foreach($result as $rs)
                {
                    $analise = new Analise();

                    $analise->usuario = $rs->email;

                    try
                    {
                        $analise->licenca = $rs->cd_licenca;
                    }
                    catch(\ErrorException $e){}

                    try
                    {
                        $analise->empresa = $rs->cd_empresa;
                    }
                    catch(\ErrorException $e){}

                    $analise->perfil = $rs->nm_perfil;
                    if( $analise->perfil == null || $analise->perfil == "" )
                        $analise->perfil = $rs->nm_perfil2;
                    if( $analise->perfil == null || $analise->perfil == "" )
                        $analise->perfil = $rs->nm_perfil3;

                    $analise->ativo = $rs->ativo_referencia;

                    $analise->periodo = $rs->periodo_ativo;

                    try
                    {
                        $ultDHAnalise = $rs->dh_comentario;
                        $analise->dh = date("Y-m-d H:m:s". strtotime($ultDHAnalise));
                    }
                    catch(\ErrorException $e){}


                    $analise->compartilhar = $rs->compartilhar == "1" ? true : false;

                    $analise->gratuita = $rs->gratuito == "1" ? true : false;

                    $analise->grupos = $rs->modulos_grupo_analise;
                    if( $analise->grupos != null && $analise->grupos == "" )
                        $analise->grupos = null;
                    if( $analise->grupos != null )
                        $analise->grupos = str_replace(";", "|",$analise->grupos);

                    if( count($this->listAnalises) == $this->tamanhoListAN ) // aumenta a capacidade em 10% se necessário
                    {
                        $this->tamanhoListAN += ($this->tamanhoListAN / 10);
                        $this->listAnalises->ensureCapacity($this->tamanhoListAN);
                    }

                    // se já existe uma análise do mesmo analista, para o mesmo ativo e perído,remove
                    for( $i = count($this->listAnalises)-1; $i >= 0; $i-- )
                    {
                        if( $this->listAnalises->get($i)->licenca == $analise->licenca &&
                            $this->listAnalises->get($i)->empresa == $analise->empresa &&
                            $this->listAnalises->get($i)->ativo == $analise->ativo &&
                            $this->listAnalises->get($i)->periodo == $analise->periodo)
                        {
                            $this->listAnalises->ativo = ""; //listAnalises->remove($i) ainda não implemntado
                            break;
                        }
                    }

                    if( count($this->listAnalises) == $this->tamanhoListAN )
                        $this->listAnalises = array(); //listAnalises->remove(0);

                    $this->listAnalises = array();//listAnalises->add($analise) idem
                }

            }
            else
                echo "Nao pude recuperar Analises por nao estar conectado ao banco Intranet";
        }
        catch(\PDOException $e)
        {
            //e.printStackTrace();
            echo "Erro ao tentar recuperar Analises";
        }

    }

}