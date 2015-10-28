<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:47
 */

namespace tresrisolution\Classes;


class GraficoServer
{

    // <String=código do ativo,TreeMap=histórico do ativo>
    //private LinkedHashMap<String,TreeMap<String,Barra>> mapHistoricos = new LinkedHashMap<String,TreeMap<String,Barra>>();

    protected $dataHoje;

    private $atualizandoMemoria = false;

    protected $lUltimoRequest;

    // lista de ativos corrigidos
    protected $tamanhoListAC = 3000;
    protected $listAC;
    //private LinkedHashMap<String,Integer> mapAC = new LinkedHashMap<String,Integer>();
    protected $ultDHAtivoCorrigido = null;

    // lista de todas as análises
    protected $tamanhoListAN = 200;
    protected $listAnalises;
    protected $ultDHAnalise = null;
    //private Thread threadAnalises;

    // lista de todos os alertas
    protected $tamanhoListAL = 3000;
    protected $listAlertas = array();
    //private Thread threadAlertas;

    protected $bLog = false;

    protected $ultMinutoCacheMemoria = -1;

    public function __construct()
    {
        $this->lUltimoRequest = $this->currentTimeMillis();
        $this->listAC = new ArrayList();
        $this->dataHoje = date('Ymd');
    }


    public function modulosContemGrupo($grupos, $modulos)
    {
        if( $modulos != null && !empty($modulos) ) {
            $grupos  = str_replace($grupos,"|", ";");
            $modulos = str_replace($modulos,"|", ";");
            $vGrupos[]  = str_split($grupos,";");
            $vModulos[] = str_split($modulos,";");
            for( $i = 0; $i < count($vGrupos); $i++ )
                for( $j = 0; $j < count($vModulos); $j++ )
                    if( in_array($vGrupos[$i],$vModulos[$j]) )
                        return true;
        }

        return false;
    }

    public function currentTimeMillis()
    {
        $timeparts = explode(" ",microtime());
        $currenttime = bcadd(($timeparts[0]*1000),bcmul($timeparts[1],1000));

        return $currenttime;
    }

}