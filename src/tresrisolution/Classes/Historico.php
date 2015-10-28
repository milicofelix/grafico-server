<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:48
 */

namespace tresrisolution\Classes;


class Historico extends GraficoServer
{
    private $conn;
    private $dt;
    public function __construct(){
        parent::__construct();
        $this->dt   = new Horas();
    }

    /**
     * @param $ultDH
     * @return string
     */
    public function getHistoricosCorrigidos($ultDH)
    {
        echo "Entrou no method getHistoricosCorrigidos";exit;
        $sb = "";

        for($i = $this->listAC->getIterator(); $i->valid(); $i->next() ){

            if (strlen($sb) != 0)
                $sb .= ";";

            $sb .= $this->listAC->offsetGet('codigo');
            $sb .= ",";
            $sb .= $this->listAC->offsetGet('dh');
        }

        return $sb;
    }

    /**
     * @param $ativo
     * @param $periodo
     * @param $horaFimPregao
     * @param $somenteBarrasHoje
     * @param $indexador
     * @param $nBarras
     * @return string
     */
    public function getHistorico($ativo, $periodo, $horaFimPregao, $somenteBarrasHoje, $indexador, $nBarras)
    {

        $xml = "";

        $tmHistorico[] = $this->getHistoricoBanco2($ativo, $periodo, $horaFimPregao);
//        echo '<pre>';
//        print_r($tmHistorico[0]);exit;

        if( count($tmHistorico[0]) > 0 )
        {
            $l = $this->currentTimeMillis();


            $xml .= "<serie id=\"" . $ativo . "\" hh=\"" . $this->dt->getHoraInt() . "\">";

            $startBarra = -1; // barra inicial que ser� enviada ao cliente

            foreach( $tmHistorico[0] as $k => $b )
            {
                $startBarra++;
//                echo !empty($somenteBarrasHoje)?$somenteBarrasHoje:'Vazio';exit;

                if(is_object($b)) {

                    if ($somenteBarrasHoje && $tmHistorico[0][$k]->data != $this->dataHoje) continue;
//                    else if ($startBarra < count($tmHistorico[0]) - $nBarras) continue;

                    $xml .= "<barra>";
                    $xml .= "<d>" . $tmHistorico[0][$k]->data . "</d>";
                    $xml .= "<u>" . $tmHistorico[0][$k]->ultima . "</u>";
                    if (!$indexador || $periodo > 0)
                        $xml .= "<h>" . $tmHistorico[0][$k]->hora . "</h>";
                    if (!$indexador) {
                        $xml .= "<a>" . $tmHistorico[0][$k]->abertura . "</a>";
                        $xml .= "<M>" . $tmHistorico[0][$k]->maxima . "</M>";
                        $xml .= "<m>" . $tmHistorico[0][$k]->minima . "</m>";
                        $xml .= "<v>" . $tmHistorico[0][$k]->volume . "</v>";
                        $xml .= "<n>" . $tmHistorico[0][$k]->negocios . "</n>";
                        $xml .= "<t>" . $tmHistorico[0][$k]->vft . "</t>";
                    }
                    $xml .="</barra>";
                }

            }

            $xml .="</serie>";
            if( $this->bLog )
                echo "Tempo de consulta no Hashtable para " . $ativo . ($periodo > 0 ? "." . $periodo : "") . ": " . ($this->currentTimeMillis() - $l) . "ms";
        }

        return $xml;

    }

    public function getHistoricoBanco2($ativo, $periodo, $horaFimPregao)
    {

        $tmHistorico = array();


        try
        {
            $maxBarras = 2600; // para di�rio
            if( $periodo == 1 )
                $maxBarras = 2160;  // aprox 4 dias
            else if( $periodo == 5 )
                $maxBarras = 1620;  // aprox 15 dias
            else if( $periodo == 15 )
                $maxBarras = 1584;  // aprox 44 dias

            $sql = null;
            if( preg_match('/^AED_/',$ativo)) // apenas para avan�o e decl�nio
            {
                $indice = substr($ativo, strpos($ativo,"_")+1);
                $sql = "SELECT 0 as abertura, 0 as maxima, desceram as minima, subiram as ultima, 0 as volume, 0 as negocios, data, 0 as vft
                          FROM indices_bovespa_diario
                          WHERE codigo = '" . $indice . "' order by data desc limit " . $maxBarras;
            }
            else if( $periodo == 0 ) // di�rio
            {
                //$sql = "SELECT abertura, maxima, minima, ultima, volume, negocios, dt as data, vft FROM historico_ajustado_diario WHERE codigo = '" . $ativo . "' order by dt desc limit " . $maxBarras;
                $sql  = "SELECT abertura, maxima, minima, ultima, volume, negocios, dt as data, vft
                          FROM historico_ajustado_diario
                          WHERE codigo = '" . $ativo . "'
                          AND dt <> now()::date ";
                $sql .= "UNION ";

                $sql .= "SELECT abertura, maxima, minima, ultima, volume, negocios, dt as data, vft
                          FROM historico_ajustado_diario_hoje
                          WHERE codigo = '" . $ativo . "'
                          AND dt = now()::date ";
                $sql .= "ORDER BY data DESC LIMIT ". $maxBarras;
            }
            else if( $periodo == 15 )
            {
                //$sql = "SELECT abertura, maxima, minima, ultima, volume, negocios, dh as data, vft FROM historico_ajustado_15min WHERE codigo = '" . $ativo . "' order by dh desc limit " . $maxBarras;
                $sql  = "SELECT abertura, maxima, minima, ultima, volume, negocios, dh as data, vft FROM historico_ajustado_15min WHERE codigo = '" . $ativo . "' and dh::date <> now()::date ";
                $sql .= "UNION ";
                $sql .= "SELECT abertura, maxima, minima, ultima, volume, negocios, dh as data, vft
                          FROM historico_ajustado_15min_hoje
                          WHERE codigo = '" . $ativo . "'
                          AND dh::date = now()::date ";
                $sql .= "ORDER BY data DESC LIMIT " . $maxBarras;
            }
            else if( $periodo == 5 )
            {
                $sql  = "SELECT abertura, maxima, minima, ultima, volume, negocios, dh as data, vft
                          FROM historico_ajustado_5min
                          WHERE codigo = '" . $ativo . "'
                          AND dh::date <> now()::date ";
                $sql .= "UNION ";
                $sql .= "SELECT abertura, maxima, minima, ultima, volume, negocios, dh as data, vft
                          FROM historico_ajustado_5min_hoje
                          WHERE codigo = '" . $ativo . "'
                          AND dh::date = now()::date ";
                $sql .= "ORDER BY data DESC LIMIT " . $maxBarras;
            }
            else if( $periodo == 1 )
            {
                $sql  = "SELECT abertura, maxima, minima, ultima, volume, contratos as negocios, dh as data, vft
                          FROM intra_diario
                          WHERE codigo = '" . $ativo . "'
                          AND dh::date = now()::date ";
                $sql .= "UNION ";
                $sql .= "SELECT abertura, maxima, minima, ultima, volume, negocios, dh as data, vft
                          FROM historico_ajustado_1min
                          WHERE codigo = '" . $ativo . "' ";
                $sql .= "ORDER BY data DESC LIMIT " . $maxBarras;

            }
            $this->conn = new ConexoesDB();
            $stmt = $this->conn->getInstance('cotacoes')->prepare($sql);
            $stmt->execute();
//            print_r($tmHistorico);exit;
            $result = $stmt->fetchAll();
//            echo "<pre>";
//            print_r($result);exit;
            $b = new Barra();

            foreach($result as $rs)
            {
                if( $periodo == 0 )
                {
                    $b->data = str_replace('-','',substr($rs[6],0,10));
                    $b->hora = 200000; //Integer.pa$rseInt(horaFimPregao);
                }
                else
                {
                    ;
                    $b->data = str_replace('-','',substr($rs[6],0,10));
                    $b->hora = str_replace(':','',substr($rs[6],11,8));
                }

                $b->abertura = $rs[0];
                $b->maxima   = $rs[1];
                $b->minima   = $rs[2];
                $b->ultima   = $rs[3];
                $b->volume   = $rs[4];
                $b->negocios = $rs[5];
                $b->vft      = $rs[7];

                // faz data e hora como chave
                array_push($tmHistorico,$b->data . ($b->hora < 1000 ? "000" : $b->hora < 10000 ? "00" : $b->hora < 100000 ? "0" : "") . $b->hora,$b);

            }
//            echo "<pre>";
//            print_r($tmHistorico);exit;
        }
        catch(\PDOException $e)
        {
            //e.printStackTrace();
            echo "Erro ao tentar recuperar historico de ativo";
        }

        return $tmHistorico;
    }

    public function procuraHistoricosCorrigidos()
    {
        echo "Entrou em procuraHistoricosCorrigidos";exit;
//echo "SERVIDOR=".$serv    idorLocal;
//		if( (servidorLocal.equals("localhost") && !conexaoBancoIntranetRemoto()) || !conexaoBancoIntranet() )
//        {
//            echo "Nao pude recuperar Ativos Corrigidos por nao conseguir conexao com o banco INTRANET";
//        }
        echo "Entramos no method procuraHistoricosCorrigidos";exit;
        try
        {
            //statement = servidorLocal.equals("localhost") ? connectionIntranetRemoto.createStatement() : connectionIntranet.createStatement();

            //if( connectionIntranet != null && !connectionIntranet.isClosed() && statement != null )
            {
                $sql = "select codigo, corrigido FROM smartweb_cadastro_ativo WHERE corrigido is not null ";
                if( $this->ultDHAtivoCorrigido != null )
                    $sql .= "and corrigido > '" . $this->ultDHAtivoCorrigido . "' ";
                $sql .= "order by corrigido asc";

                echo "$sql=".$sql;

                $this->conn = new ConexoesDB();
                $stmt = $this->conn->getInstance('cotacoes')->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchObject();

                foreach($result as $rs)
                {
                    $dh = 0 ;
                    try
                    {
                        $ultDHAtivoCorrigido = $rs->corrigido;
                        $dh = date("Y-m-d H:m:s", strtotime($this->ultDHAtivoCorrigido));
                    }
                    catch(\PDOException $e)
                    { $e->getTraceAsString(); }

                    if( count($this->listAC) == $this->tamanhoListAC ) // aumenta a capacidade em 10% se necess�rio
                    {
                        $this->tamanhoListAC += ($this->tamanhoListAC / 10);
                        $this->listAC.ensureCapacity($this->tamanhoListAC);
                    }

                    if( $this->mapAC->containsKey($rs->codigo) )
                    {
                        $this->listAC->set($this->mapAC->get($rs->codigo), new AtivoCorrigido($dh, $rs->codigo));
                    }
                    else
                    {
                        $this->listAC->add(new AtivoCorrigido($dh, $rs->codigo));
                        $this->mapAC->put($rs->codigo, $this->listAC->count()-1);
                    }
                }

                if( $this->listAC->count() > 0 )
                {
                    Collections.sort($listAC);

                    // mapeia novamente os ativos para n�o repetirem na lista depois
                    $this->mapAC->clear();
                    for( $i = 0; $i < count($listAC); $i++ )
                        $this->mapAC->put($listAC->get($i)->codigo, $i);

                    echo "SizeAC:" . count($listAC);
                    echo "Ativo:" . $this->listAC->get(0)->codigo . " DH:" . new SimpleDateFormat("yyyy-MM-dd H:m:s").format(new Date($this->listAC->get(0).$dh));
                }
            }
            //else
            //echo "Nao pude recuperar Ativos Corrigidos por nao estar conectado ao banco Intranet";
        }
        catch(\PDOException $e)
        {
            $e->getTraceAsString();
            echo "Erro ao tentar recuperar Ativos Corrigidos";
        }
    }
}