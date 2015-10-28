<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:55
 */

namespace tresrisolution\Classes;


class Perfil extends GraficoServer
{
    protected $listAnalises = array();
    private $conn;
    private $connIntranet;
    private $connIntraRemoto;

    public function __construct()
    {
        $this->conn = new ConexoesDB();

    }

    public function getPerfisCompartilhados(Request $request)
    {
        echo "Entrou no method getPerfisCompartilhados";exit;
        $xml = "";
        $ativoReferencia = $request->getParameter("ar");
        $licencas = $request->getParameter("l"); // licenças dos analistas que o usuário tem permissão para ver análises
        $listLicencas = array();

        $modulos = $request->getParameter("mo");

        foreach( explode(',',$licencas) as $l )
        {

            $listLicencas[] = $l;

        }

        for( $i = count($this->listAnalises)-1; $i >= 0; $i-- )
        {
            if( (in_array($this->listAnalises->get($i)->licenca,$listLicencas) || $this->listAnalises->get($i)->gratuita)
                && $this->listAnalises->get($i)->compartilhar
                && ($this->listAnalises->get($i)->grupos == null || $this->modulosContemGrupo($this->listAnalises->get($i)->grupos, $modulos))
                && $this->listAnalises->get($i)->ativo == $ativoReferencia )
            {
                $xml .="<perfis>";
                $xml .="<u>" . $this->listAnalises->get($i)->usuario . "</u>";
                $xml .="<l>" . $this->listAnalises->get($i)->licenca . "</l>";
                $xml .="<n>" . $this->listAnalises->get($i)->perfil  . "</n>";
                $xml .="<p>" . $this->listAnalises->get($i)->periodo . "</p>";
                $xml .="<d>" . $this->listAnalises->get($i)->dh      . "</d>";
                $xml .="</perfis>";
            }
        }

        return $xml;
    }

    public function getPerfisCompartilhadosAnalista(Request $request)
    {
        echo "Entrou no method getPerfisCompartilhadosAnalista";exit;
        $xml = "";

        $licenca = 0;

        try
        {
            $licenca = $request->getParameter("l"); // licença do analista
        }
        catch(\PDOException $e){}

        $modulos = $request->getParameter("mo");

        for( $i = count($this->listAnalises)-1; $i >= 0; $i-- )
        {
            if( $this->listAnalises->get($i)->licenca == $licenca
                && $this->listAnalises->get($i)->compartilhar
                && ($this->listAnalises->get($i)->grupos == null ||  $this->modulosContemGrupo($this->listAnalises->get($i)->grupos,  $modulos))
            )
            {
                $xml .= "<perfis>";
                $xml .= "<u>" . $this->listAnalises->get($i)->usuario . "</u>";
                $xml .= "<l>" . $this->listAnalises->get($i)->licenca . "</l>";
                $xml .= "<n>" . $this->listAnalises->get($i)->perfil  . "</n>";
                $xml .= "<a>" . $this->listAnalises->get($i)->ativo   . "</a>";
                $xml .= "<p>" . $this->listAnalises->get($i)->periodo . "</p>";
                $xml .= "<d>" . $this->listAnalises->get($i)->dh      . "</d>";
                $xml .= "</perfis>";
            }
        }

        return $xml;
    }

    public function getPerfisCompartilhadosTodosAnalistas(Request $request)
    {
        echo "Entrou no method getPerfisCompartilhadosTodosAnalistas";exit;
        $xml = "";

        $licencas = $request->getParameter("l"); // licenças dos analistas que o usuário tem permissão para ver análises
        $listLicencas = array();

        foreach( explode(',',$licencas) as $l )
        {
            try
            {
                array_push($listLicencas,$l);
            }
            catch(\PDOException $e){}
        }

        $modulos = $request->getParameter("mo");

        for( $i = count($this->listAnalises)-1; $i >= 0; $i-- )
        {
            if( (in_array($this->listAnalises->get($i)->licenca,$listLicencas) || $this->listAnalises->get($i)->gratuita)
                && $this->listAnalises->get($i)->compartilhar
                && ($this->listAnalises->get($i)->grupos == null ||  $this->modulosContemGrupo($this->listAnalises->get($i)->grupos,   $modulos))
            )
            {
                $xml .= "<perfis>";
                $xml .= "<u>" . $this->listAnalises->get($i)->usuario . "</u>";
                $xml .= "<l>" . $this->listAnalises->get($i)->licenca . "</l>";
                $xml .= "<n>" . $this->listAnalises->get($i)->perfil  . "</n>";
                $xml .= "<a>" . $this->listAnalises->get($i)->ativo   . "</a>";
                $xml .= "<p>" . $this->listAnalises->get($i)->periodo . "</p>";
                $xml .= "<d>" . $this->listAnalises->get($i)->dh      . "</d>";
                $xml .= "</perfis>";
            }
        }

        return $xml;
    }

    public function salvaPerfilGrafico(Request $request)
    {
        $this->connIntranet     = $this->conn->getInstance('intranet');
        $this->connIntraRemoto  = $this->conn->getInstance('intranetremoto');

        $xml = "";

        try
        {
            $usuario = $request->getParameter("u");
            $empresa = $request->getParameter("e");
            $perfil  = $request->getParameter("p");
            $analista = $request->getParameter("analista")	!= null && $request->getParameter("analista") == "1";

            echo $analista;exit;

            $cd_perfil = -1;

            $xml .= "SELECT cd_perfil FROM perfil_grafico_java WHERE usuario = '$usuario' AND cd_empresa = $empresa AND nm_perfil = '$perfil'";

            $stmt = $this->connIntranet->prepare($xml);
//            echo "<pre>";
//            print_r($stmt);exit;
            if($stmt->execute())
                $cd_perfil = 1;

            if( $cd_perfil == -1 )
            {
                $xml = "";

                $xml .= "INSERT INTO perfil_grafico_java VALUES(";
                $xml .= "nextval('\"perfil_cd_perfil_seq\"'::text)";
                $xml .= ",'" . $usuario . "'";  // $usuario
                $xml .= ",'xxxxxxxx'";        // senha
                $xml .= ",'" . $empresa . "'"; // cd_$empresa
                $xml .= ",now()";           // dh_criacao
                $xml .= ",now()";           // dh_ult_acesso
                $xml .= ",'" . $request->getParameter("p") . "'";     // nm_perfil
                $xml .= ",'" . $request->getParameter("bar") . "'";   // barras
                $xml .= ",'" . $request->getParameter("pm1") . "'";   // periodo_mm1
                $xml .= ",'" . $request->getParameter("pm2") . "'";   // periodo_mm2
                $xml .= ",'" . $request->getParameter("pm3") . "'";   // periodo_mm3
                $xml .= ",'" . $request->getParameter("pm4") . "'";   // periodo_mm4
                $xml .= ",'" . $request->getParameter("tm1") . "'";   // tipo_mm1
                $xml .= ",'" . $request->getParameter("tm2") . "'";   // tipo_mm2
                $xml .= ",'" . $request->getParameter("tm3") . "'";   // tipo_mm3
                $xml .= ",'" . $request->getParameter("tm4") . "'";   // tipo_mm4
                $xml .= ",'" . $request->getParameter("vol") . "'";   // volume
                $xml .= ",'" . $request->getParameter("tvol") . "'";  // tam_vol
                $xml .= ",'" . $request->getParameter("bol") . "'";   // bollinger
                $xml .= ",'" . $request->getParameter("pbol1") . "'"; // boll_periodo1
                $xml .= ",'" . $request->getParameter("pbol2") . "'"; // boll_periodo2
                $xml .= ",'" . $request->getParameter("pbol3") . "'"; // boll_periodo3
                $xml .= ",'" . $request->getParameter("par") . "'";   // parabolico
                $xml .= ",'" . $request->getParameter("ppar1") . "'"; // para_periodo1
                $xml .= ",'" . $request->getParameter("ppar2") . "'"; // para_periodo2
                $xml .= ",'" . $request->getParameter("ppar3") . "'"; // para_periodo3
                $xml .= ",'" . $request->getParameter("nb") . "'";    // nbarras
                $xml .= ",'" . $request->getParameter("lb") . "'";    // barras_esquerda
                $xml .= ",'" . $request->getParameter("bl") . "'";    // barras_livres
                $xml .= ",'" . $request->getParameter("ec") . "'";    // esquema_cor
                $xml .= ",'" . $request->getParameter("env") . "'";   // envelope
                $xml .= ",'" . $request->getParameter("pEnvM1") . "'";// env_periodo1
                $xml .= ",'" . $request->getParameter("pEnvM2") . "'";// env_periodo2
                $xml .= ",'" . $request->getParameter("pEnvM3") . "'";// env_periodo3
                $xml .= ",'" . $request->getParameter("bc") . "'";    // base_calculo
                $xml .= ",'" . $request->getParameter("ae") . "'";    // alt_estudo
                $xml .= ",'" . $request->getParameter("ms") . "'";    // margem_superior
                $xml .= ",'" . $request->getParameter("mi") . "'";    // margem_inferior
                $xml .= ",'" . $request->getParameter("es") . "'";    // escala
                $xml .= ",'" . $request->getParameter("gr") . "'";    // grid
                $xml .= ",'" . $request->getParameter("mbol") . "'";  // boll_media
                $xml .= ",'" . $request->getParameter("mr1") . "'";   // rec_mm1
                $xml .= ",'" . $request->getParameter("mr2") . "'";   // rec_mm2
                $xml .= ",'" . $request->getParameter("mr3") . "'";   // rec_mm3
                $xml .= ",'" . $request->getParameter("mr4") . "'";   // rec_mm4
                $xml .= ",'" . $request->getParameter("tf") . "'";    // topos_fundos
                $xml .= ",'" . $request->getParameter("vtf") . "'";   // var_topos_fundos
                $xml .= ",'" . $request->getParameter("mtf") . "'";   // modo_topos_fundos
                $xml .= ",'" . $request->getParameter("btf") . "'";   // base_topos_fundos
                $xml .= ",'" . $request->getParameter("sf") . "'";    // stop_financeiro
                $xml .= ",'" . $request->getParameter("pmv1") . "'";  // periodo_mmv1
                $xml .= ",'" . $request->getParameter("er") . "'";    // escala_redonda
                $xml .= ",'" . $request->getParameter("cmp") . "'";   // estudo_cmp
                $xml .= ",'" . $request->getParameter("ac") . "'";    // ativos_cmp
                $xml .= ",'" . $request->getParameter("lzc") . "'";   // linha_zero_cmp
                $xml .= ",'" . $request->getParameter("sc") . "'";    // simulador_cmp
                $xml .= ",'" . $request->getParameter("vsc") . "'";   // valor_simulado_cmp
                $xml .= ",'" . $request->getParameter("as") . "'";   // ativo_spr
                $xml .= ",'" . $request->getParameter("q1") . "'";   // qtde1_spr
                $xml .= ",'" . $request->getParameter("q2") . "'";   // qtde2_spr
                $xml .= ",'" . $request->getParameter("ss") . "'";   // simulador_spr
                $xml .= ",'" . $request->getParameter("gsp") . "'";   // estudo_sobreposto
                $xml .= ",'" . $request->getParameter("agsp") . "'";  // ativos_sobrepostos
                $xml .= ",'" . $request->getParameter("hlo")  . "'"; // highlow
                $xml .= ",'" . $request->getParameter("pHL1") . "'"; // hlo_periodo1
                $xml .= ",'" . $request->getParameter("pHL2") . "'"; // hlo_periodo2
                $xml .= ",'" . $request->getParameter("pHL3") . "'"; // hlo_periodo3
                $xml .= ",'" . $request->getParameter("ktc")  . "'"; // Keltner Channels (KTC)
                $xml .= ",'" . $request->getParameter("pKTC") . "'"; // ktc_periodo1
                $xml .= ",'" . $request->getParameter("aed")  . "'"; // ativos do clímax
                $xml .= ",'" . $request->getParameter("lad")  . "'"; // ativos do Linha de Avanço & Declínio
                $xml .= ",'" . $request->getParameter("cha")  . "'"; // ativos do Chaiken Oscillator
                $xml .= ",'" . $request->getParameter("mos")  . "'"; // ativos do McClellan Oscillator
                $xml .= ",'" . $request->getParameter("bth")  . "'"; // ativos do Breadth Thrust
                $xml .= ",'" . $request->getParameter("el")  . "'"; // estilos para linhas
                $xml .= ",'" . $request->getParameter("vr")  . "'"; // valor da reta na escala
                $xml .= ",'" . ($request->getParameter("pvt") != null ? $request->getParameter("pvt") : "0")  . "'"; // pivot
                $xml .= ",'" . ($request->getParameter("ph") != null ? $request->getParameter("ph") : "0")  . "'";  // pivot em todo o histórico
                $xml .= ",'" . ($request->getParameter("pn") != null ? $request->getParameter("pn") : "")  . "'";  // pivot níveis
                $xml .= ",'" . ($request->getParameter("hla") != null ? $request->getParameter("hla") : "0")  . "'"; // high low activator
                $xml .= ",'" . ($request->getParameter("pHLAH") != null ? $request->getParameter("pHLAH") : "3")  . "'"; // média high high low activator
                $xml .= ",'" . ($request->getParameter("pHLAL") != null ? $request->getParameter("pHLAL") : "3")  . "'"; // média low high low activator

                $xml .= ",'" . ($request->getParameter("cvd") != null ? $request->getParameter("cvd") : "1")  . "'"; // cor de volume diferenciado

                $xml .= ",'" . ($request->getParameter("pm5") != null ? $request->getParameter("pm5") : "0")  . "'"; // período média 5
                $xml .= ",'" . ($request->getParameter("tm5") != null ? $request->getParameter("tm5") : "0")  . "'"; // tipo média 5

                $xml .= ",'" . ($request->getParameter("bc1") != null ? $request->getParameter("bc1") : "0")  . "'"; // base de cálculo média 1
                $xml .= ",'" . ($request->getParameter("bc2") != null ? $request->getParameter("bc2") : "0")  . "'"; // base de cálculo média 2
                $xml .= ",'" . ($request->getParameter("bc3") != null ? $request->getParameter("bc3") : "0")  . "'"; // base de cálculo média 3
                $xml .= ",'" . ($request->getParameter("bc4") != null ? $request->getParameter("bc4") : "0")  . "'"; // base de cálculo média 4
                $xml .= ",'" . ($request->getParameter("bc5") != null ? $request->getParameter("bc5") : "0")  . "'"; // base de cálculo média 5

                $xml .= ",'" . ($request->getParameter("dm1") != null ? $request->getParameter("dm1") : "0")  . "'"; // desloca média 1
                $xml .= ",'" . ($request->getParameter("dm2") != null ? $request->getParameter("dm2") : "0")  . "'"; // desloca média 2
                $xml .= ",'" . ($request->getParameter("dm3") != null ? $request->getParameter("dm3") : "0")  . "'"; // desloca média 3
                $xml .= ",'" . ($request->getParameter("dm4") != null ? $request->getParameter("dm4") : "0")  . "'"; // desloca média 4
                $xml .= ",'" . ($request->getParameter("dm5") != null ? $request->getParameter("dm5") : "0")  . "'"; // desloca média 5

                $xml .= ",'" . ($request->getParameter("ha") != null ? $request->getParameter("ha") : "0")  . "'"; // heiken ashi
                $xml .= ",'" . ($request->getParameter("hap1") != null ? $request->getParameter("hap1") : "3")  . "'"; // período 1 heiken ashi
                $xml .= ",'" . ($request->getParameter("hap2") != null ? $request->getParameter("hap2") : "5")  . "'"; // período 2 heiken ashi

                $xml .= ",'" . ($request->getParameter("pKTC2") != null ? $request->getParameter("pKTC2") : "10")  . "'"; // período 2 KTC/KCM
                $xml .= ",'" . ($request->getParameter("pKTC3") != null ? $request->getParameter("pKTC3") : "10")  . "'"; // período 3 KTC/KCM
                $xml .= ",'" . ($request->getParameter("cKTC2") != null ? $request->getParameter("cKTC2") : "1")  . "'"; // coeficiente 2 KTC/KCM
                $xml .= ",'" . ($request->getParameter("cKTC3") != null ? $request->getParameter("cKTC3") : "1")  . "'"; // coeficiente 3 KTC/KCM
                $xml .= ",'" . ($request->getParameter("bcKTC") != null ? $request->getParameter("bcKTC") : "0")  . "'"; // banda central KTC/KCM

                $xml .= ",'" . ($request->getParameter("ftfc") != null ? $request->getParameter("ftfc") : "0")  . "'"; // Fura Teto e Fura Chão
                $xml .= ",'" . ($request->getParameter("cftfc") != null ? $request->getParameter("cftfc") : "0.146")  . "'"; // coeficiente Fura Teto e Fura Chão
                $xml .= ",'" . ($request->getParameter("hftfc") != null ? $request->getParameter("hftfc") : "0")  . "'"; // histórico do Fura Teto e Fura Chão

                $xml .= ",'" . ($request->getParameter("jrs") != null ? $request->getParameter("jrs") : "0")  . "'"; // Joe Ross
                $xml .= ",'" . ($request->getParameter("pjrs") != null ? $request->getParameter("pjrs") : "15")  . "'"; // período Joe Ross
                $xml .= ",'" . ($request->getParameter("cjrs1") != null ? $request->getParameter("cjrs1") : "0.146")  . "'"; // coeficiente 1 Joe Ross
                $xml .= ",'" . ($request->getParameter("cjrs2") != null ? $request->getParameter("cjrs2") : "0")  . "'"; // coeficiente 2 Joe Ross
                $xml .= ",'" . ($request->getParameter("ujrs") != null ? $request->getParameter("ujrs") : "0")  . "'"; // considera última barra do Joe Ross

                $xml .= ",'" . ($request->getParameter("nic") != null ? $request->getParameter("nic") : "0")  . "'"; // Nuvem de Ichimoku
                $xml .= ",'" . ($request->getParameter("ts") != null ? $request->getParameter("ts") : "9")  . "'"; // Tenkan Sen
                $xml .= ",'" . ($request->getParameter("ks") != null ? $request->getParameter("ks") : "26")  . "'"; // Kijun Sen
                $xml .= ",'" . ($request->getParameter("dcs") != null ? $request->getParameter("dcs") : "-26")  . "'"; // Desloca Chikou Span
                $xml .= ",'" . ($request->getParameter("dssa") != null ? $request->getParameter("dssa") : "26")  . "'"; // Desloca Senkou Span A
                $xml .= ",'" . ($request->getParameter("dssb") != null ? $request->getParameter("dssb") : "26")  . "'"; // Desloca Senkou Span B
                $xml .= ",'" . ($request->getParameter("ssb") != null ? $request->getParameter("ssb") : "52")  . "'"; // Senkou Span B

                $xml .= ")";
            }
            else
            {
                $xml = "";

                $xml .= "UPDATE perfil_grafico_java SET ";
                $xml .= "dh_ult_acesso = now()";           // dh_ult_acesso
                $xml .= ",barras = '" . $request->getParameter("bar") . "'";   // barras
                $xml .= ",periodo_mm1 = '" . $request->getParameter("pm1") . "'";   // periodo_mm1
                $xml .= ",periodo_mm2 = '" . $request->getParameter("pm2") . "'";   // periodo_mm2
                $xml .= ",periodo_mm3 = '" . $request->getParameter("pm3") . "'";   // periodo_mm3
                $xml .= ",periodo_mm4 = '" . $request->getParameter("pm4") . "'";   // periodo_mm4
                $xml .= ",tipo_mm1 = '" . $request->getParameter("tm1") . "'";   // tipo_mm1
                $xml .= ",tipo_mm2 = '" . $request->getParameter("tm2") . "'";   // tipo_mm2
                $xml .= ",tipo_mm3 = '" . $request->getParameter("tm3") . "'";   // tipo_mm3
                $xml .= ",tipo_mm4 = '" . $request->getParameter("tm4") . "'";   // tipo_mm4
                $xml .= ",volume = '" . $request->getParameter("vol") . "'";   // volume
                $xml .= ",tam_vol = '" . $request->getParameter("tvol") . "'";  // tam_vol
                $xml .= ",bollinger = '" . $request->getParameter("bol") . "'";   // bollinger
                $xml .= ",boll_periodo1 = '" . $request->getParameter("pbol1") . "'"; // boll_periodo1
                $xml .= ",boll_periodo2 = '" . $request->getParameter("pbol2") . "'"; // boll_periodo2
                $xml .= ",boll_periodo3 = '" . $request->getParameter("pbol3") . "'"; // boll_periodo3
                $xml .= ",parabolico = '" . $request->getParameter("par") . "'";   // parabolico
                $xml .= ",para_periodo1 = '" . $request->getParameter("ppar1") . "'"; // para_periodo1
                $xml .= ",para_periodo2 = '" . $request->getParameter("ppar2") . "'"; // para_periodo2
                $xml .= ",para_periodo3 = '" . $request->getParameter("ppar3") . "'"; // para_periodo3
                $xml .= ",nbarras = '" . $request->getParameter("nb") . "'";    // nbarras
                $xml .= ",barras_esquerda = '" . $request->getParameter("lb") . "'";    // barras_esquerda
                $xml .= ",barras_livres = '" . $request->getParameter("bl") . "'";    // barras_livres
                $xml .= ",esquema_cor = '" . $request->getParameter("ec") . "'";    // esquema_cor
                $xml .= ",envelope = '" . $request->getParameter("env") . "'";   // envelope
                $xml .= ",env_periodo1 = '" . $request->getParameter("pEnvM1") . "'";// env_periodo1
                $xml .= ",env_periodo2 = '" . $request->getParameter("pEnvM2") . "'";// env_periodo2
                $xml .= ",env_periodo3 = '" . $request->getParameter("pEnvM3") . "'";// env_periodo3
                $xml .= ",base_calculo = '" . $request->getParameter("bc") . "'";    // base_calculo
                $xml .= ",alt_estudo = '" . $request->getParameter("ae") . "'";    // alt_estudo
                $xml .= ",margem_superior = '" . $request->getParameter("ms") . "'";    // margem_superior
                $xml .= ",margem_inferior = '" . $request->getParameter("mi") . "'";    // margem_inferior
                $xml .= ",escala = '" . $request->getParameter("es") . "'";    // escala
                $xml .= ",grid = '" . $request->getParameter("gr") . "'";    // grid
                $xml .= ",boll_media = '" . $request->getParameter("mbol") . "'";  // boll_media
                $xml .= ",rec_mm1 = '" . $request->getParameter("mr1") . "'";   // rec_mm1
                $xml .= ",rec_mm2 = '" . $request->getParameter("mr2") . "'";   // rec_mm2
                $xml .= ",rec_mm3 = '" . $request->getParameter("mr3") . "'";   // rec_mm3
                $xml .= ",rec_mm4 = '" . $request->getParameter("mr4") . "'";   // rec_mm4
                $xml .= ",topos_fundos = '" . $request->getParameter("tf") . "'";    // topos_fundos
                $xml .= ",var_topos_fundos = '" . $request->getParameter("vtf") . "'";   // var_topos_fundos
                $xml .= ",modo_topos_fundos = '" . $request->getParameter("mtf") . "'";   // modo_topos_fundos
                $xml .= ",base_topos_fundos = '" . $request->getParameter("btf") . "'";   // base_topos_fundos
                $xml .= ",stop_financeiro = '" . $request->getParameter("sf") . "'";    // stop_financeiro
                $xml .= ",periodo_mmv1 = '" . $request->getParameter("pmv1") . "'";  // periodo_mmv1
                $xml .= ",escala_redonda = '" . $request->getParameter("er") . "'";    // escala_redonda
                $xml .= ",estudo_cmp = '" . $request->getParameter("cmp") . "'"; // estudo_cmp
                $xml .= ",ativos_cmp = '" . $request->getParameter("ac") . "'"; // ativos_cmp
                $xml .= ",linha_zero_cmp = '" . $request->getParameter("lzc") . "'"; // linha_zero_cmp
                $xml .= ",simulador_cmp = '" . $request->getParameter("sc") . "'"; // simulador_cmp
                $xml .= ",valor_simulado_cmp = '" . $request->getParameter("vsc") . "'"; // valor_simulado_cmp
                $xml .= ",ativo_spr = '" . $request->getParameter("as") . "'"; // ativo_spr
                $xml .= ",qtde1_spr = '" . $request->getParameter("q1") . "'"; // qtde1_spr
                $xml .= ",qtde2_spr = '" . $request->getParameter("q2") . "'"; // qtde2_spr
                $xml .= ",simulador_spr = '" . $request->getParameter("ss") . "'"; // simulador_spr
                $xml .= ",estudo_sobreposto = '" . $request->getParameter("gsp") . "'"; // estudo_sobreposto
                $xml .= ",ativos_sobrepostos = '" . $request->getParameter("agsp") . "'"; // ativos_sobrepostos
                $xml .= ",highlow = '" . $request->getParameter("hlo")  . "'"; // highlow
                $xml .= ",hlo_periodo1 = '" . $request->getParameter("pHL1") . "'"; // hlo_periodo1
                $xml .= ",hlo_periodo2 = '" . $request->getParameter("pHL2") . "'"; // hlo_periodo2
                $xml .= ",hlo_periodo3 = '" . $request->getParameter("pHL3") . "'"; // hlo_periodo3
                $xml .= ",ktc = '" . $request->getParameter("ktc")  . "'"; // Keltner Channels (KTC)
                $xml .= ",ktc_periodo1 = '" . $request->getParameter("pKTC") . "'"; // ktc_periodo1
                $xml .= ",aed_indices = '" . $request->getParameter("aed")  . "'"; // ativos do clímax
                $xml .= ",lad_indices = '" . $request->getParameter("lad")  . "'"; // ativos do Linha de Avanço & Declínio
                $xml .= ",cha_indices = '" . $request->getParameter("cha")  . "'"; // ativos do Chaiken Oscillator
                $xml .= ",mos_indices = '" . $request->getParameter("mos")  . "'"; // ativos do McClellan Oscillator
                $xml .= ",bth_indices = '" . $request->getParameter("bth")  . "'"; // ativos do Breadth Thrust
                $xml .= ",estilo_linha = '" . $request->getParameter("el")  . "'"; // estilos para linhas
                $xml .= ",valor_reta_escala = '" . $request->getParameter("vr")  . "'"; // valor da reta na escala
                $xml .= ",pivot = '" . ($request->getParameter("pvt") != null ? $request->getParameter("pvt") : "0")  . "'";          // pivot
                $xml .= ",pivot_historico = '" . ($request->getParameter("ph") != null ? $request->getParameter("ph") : "0")  . "'"; // pivot em todo o histórico
                $xml .= ",pivot_niveis = '" . ($request->getParameter("pn") != null ? $request->getParameter("pn") : "")  . "'";    // pivot níveis
                $xml .= ",hilo_activator = '" . ($request->getParameter("hla") != null ? $request->getParameter("hla") : "0")  . "'"; // high low activator
                $xml .= ",hla_mediah = '" . ($request->getParameter("pHLAH") != null ? $request->getParameter("pHLAH") : "3")  . "'"; // méida high do high low activator
                $xml .= ",hla_medial = '" . ($request->getParameter("pHLAL") != null ? $request->getParameter("pHLAL") : "3")  . "'"; // méida low do high low activator

                $xml .= ",cor_vol_diferenciado = '" . ($request->getParameter("cvd") != null ? $request->getParameter("cvd") : "1")  . "'"; // cor volume diferenciado

                $xml .= ",periodo_mm5 = '" . ($request->getParameter("pm5") != null ? $request->getParameter("pm5") : "0")  . "'"; // período média 5
                $xml .= ",tipo_mm5 = '" . ($request->getParameter("tm5") != null ? $request->getParameter("tm5") : "0")  . "'"; // tipo média 5
                $xml .= ",bc_mm1 = '" . ($request->getParameter("bc1") != null ? $request->getParameter("bc1") : "0")  . "'"; // base de cálculo média 1
                $xml .= ",bc_mm2 = '" . ($request->getParameter("bc2") != null ? $request->getParameter("bc2") : "0")  . "'"; // base de cálculo média 2
                $xml .= ",bc_mm3 = '" . ($request->getParameter("bc3") != null ? $request->getParameter("bc3") : "0")  . "'"; // base de cálculo média 3
                $xml .= ",bc_mm4 = '" . ($request->getParameter("bc4") != null ? $request->getParameter("bc4") : "0")  . "'"; // base de cálculo média 4
                $xml .= ",bc_mm5 = '" . ($request->getParameter("bc5") != null ? $request->getParameter("bc5") : "0")  . "'"; // base de cálculo média 5
                $xml .= ",desloca_mm1 = '" . ($request->getParameter("dm1") != null ? $request->getParameter("dm1") : "0")  . "'"; // desloca média 1
                $xml .= ",desloca_mm2 = '" . ($request->getParameter("dm2") != null ? $request->getParameter("dm2") : "0")  . "'"; // desloca média 2
                $xml .= ",desloca_mm3 = '" . ($request->getParameter("dm3") != null ? $request->getParameter("dm3") : "0")  . "'"; // desloca média 3
                $xml .= ",desloca_mm4 = '" . ($request->getParameter("dm4") != null ? $request->getParameter("dm4") : "0")  . "'"; // desloca média 4
                $xml .= ",desloca_mm5 = '" . ($request->getParameter("dm5") != null ? $request->getParameter("dm5") : "0")  . "'"; // desloca média 5

                $xml .= ",heikenashi = '" . ($request->getParameter("ha") != null ? $request->getParameter("ha") : "0")  . "'"; // heiken ashi
                $xml .= ",ha_periodo1 = '" . ($request->getParameter("hap1") != null ? $request->getParameter("hap1") : "3")  . "'"; // período 1 do heiken ashi
                $xml .= ",ha_periodo2 = '" . ($request->getParameter("hap2") != null ? $request->getParameter("hap2") : "5")  . "'"; // período 2 do heiken ashi

                $xml .= ",ktc_periodo2 = '" . ($request->getParameter("pKTC2") != null ? $request->getParameter("pKTC2") : "10")  . "'"; // período 2 do KTC
                $xml .= ",ktc_periodo3 = '" . ($request->getParameter("pKTC3") != null ? $request->getParameter("pKTC3") : "10")  . "'"; // período 3 do KTC
                $xml .= ",ktc_coeficiente2 = '" . ($request->getParameter("cKTC2") != null ? $request->getParameter("cKTC2") : "1")  . "'"; // coeficiente 2 do KTC
                $xml .= ",ktc_coeficiente3 = '" . ($request->getParameter("cKTC3") != null ? $request->getParameter("cKTC3") : "1")  . "'"; // coeficiente 3 do KTC
                $xml .= ",ktc_plota_central = '" . ($request->getParameter("bcKTC") != null ? $request->getParameter("bcKTC") : "0")  . "'"; // banda central do KTC

                $xml .= ",furatetochao = '" . ($request->getParameter("ftfc") != null ? $request->getParameter("ftfc") : "0")  . "'"; // Fura Teto e Fura Chão
                $xml .= ",ftc_coeficiente = '" . ($request->getParameter("cftfc") != null ? $request->getParameter("cftfc") : "0.146")  . "'"; // coeficiente do Fura Teto e Fura Chão
                $xml .= ",ftc_historico = '" . ($request->getParameter("hftfc") != null ? $request->getParameter("hftfc") : "0")  . "'"; // histórico do Fura Teto e Fura Chão

                $xml .= ",joeross = '" . ($request->getParameter("jrs") != null ? $request->getParameter("jrs") : "0")  . "'"; // Joe Ross
                $xml .= ",jrs_periodo = '" . ($request->getParameter("pjrs") != null ? $request->getParameter("pjrs") : "15")  . "'"; // período Joe Ross
                $xml .= ",jrs_coeficiente1 = '" . ($request->getParameter("cjrs1") != null ? $request->getParameter("cjrs1") : "0.146")  . "'"; // coeficiente 1 do Joe Ross
                $xml .= ",jrs_coeficiente2 = '" . ($request->getParameter("cjrs2") != null ? $request->getParameter("cjrs2") : "0")  . "'"; // coeficiente 2 do Joe Ross
                $xml .= ",jrs_ultima_barra = '" . ($request->getParameter("ujrs") != null ? $request->getParameter("ujrs") : "0")  . "'"; // considera última barra do Joe Ross

                $xml .= ",nuvem_ichimoku = '" . ($request->getParameter("nic") != null ? $request->getParameter("nic") : "0")  . "'"; // Nuvem de Ichimoku
                $xml .= ",ni_tenkansen = '" . ($request->getParameter("ts") != null ? $request->getParameter("ts") : "9")  . "'"; // Tenkan Sen
                $xml .= ",ni_kijunsen = '" . ($request->getParameter("ks") != null ? $request->getParameter("ks") : "26")  . "'"; // Kijun Sen
                $xml .= ",ni_chikouspan = '" . ($request->getParameter("dcs") != null ? $request->getParameter("dcs") : "-26")  . "'"; // Desloca Chikou Span
                $xml .= ",ni_senkouspana = '" . ($request->getParameter("dssa") != null ? $request->getParameter("dssa") : "26")  . "'"; // Desloca Senkou Span A
                $xml .= ",ni_senkouspanb = '" . ($request->getParameter("dssb") != null ? $request->getParameter("dssb") : "26")  . "'"; // Desloca Senkou Span B
                $xml .= ",ni_p_senkouspanb = '" . ($request->getParameter("ssb") != null ? $request->getParameter("ssb") : "52")  . "'"; // Senkou Span B

                $xml .= " WHERE";
                $xml .= " cd_perfil = " . $cd_perfil;
            }

            $retorno = $this->connIntranet->prepare($xml)->execute();

            if( $analista && $this->connIntraRemoto ) //conexaoBancoIntranetRemoto
                try{$this->connIntraRemoto->prepare($xml)->execute();} catch(\PDOException $e){ $e->getTraceAsString(); }

            if( $retorno == 1 && $cd_perfil == -1 ) // se foi um insert bem sucedido, pesquisa o cd_perfil
            {
                $xml = "";

                $xml .= "SELECT cd_perfil FROM perfil_grafico_java WHERE $usuario = '" . $usuario . "' AND cd_$empresa = " . $empresa . " AND nm_perfil = '" . $perfil . "'";
                $stmt = $this->connIntranet->prepare($xml);
//            echo "<pre>";
//            print_r($stmt);exit;
                if($stmt->execute())
                    $cd_perfil = 1;
            }

            if( $retorno == 1 ) // grava estudos do perfil
            {

                $xml .= "DELETE FROM perfil_grafico_java_estudo WHERE cd_perfil = " . $cd_perfil;

                $this->connIntranet->prepare($xml)->execute();
                if( $analista && $this->connIntraRemoto )
                    try{ $this->connIntraRemoto->prepare($xml)->execute(); } catch(\PDOException $e){ $e->getTraceAsString(); }

                $nEstudo = 1;
                while( $retorno == 1 && $request->getParameter("e".$nEstudo) != null && !$request->getParameter("e".$nEstudo) == "livre" && !empty(trim($request->getParameter("e".$nEstudo))) )
                {
                    $xml = "";
                    $xml .= "INSERT INTO perfil_grafico_java_estudo VALUES(";
                    $xml .= $cd_perfil;
                    $xml .= ",'" . $request->getParameter("e".$nEstudo) . "'";     // estudo
                    $xml .= ","  . $request->getParameter("e".$nEstudo."p1");      // periodo1
                    $xml .= ","  . $request->getParameter("e".$nEstudo."p2");      // periodo2
                    $xml .= ","  . $request->getParameter("e".$nEstudo."p3");      // periodo3
                    $xml .= ","  . $request->getParameter("e".$nEstudo."o");       // oscilador
                    $xml .= ","  . $request->getParameter("e".$nEstudo."m");       // media
                    $xml .= ","  . $request->getParameter("e".$nEstudo."bc");      // base_calculo
                    $xml .= ",'" . $request->getParameter("e".$nEstudo."l") . "'"; // linhas
                    $xml .= ",nextval('\"perfil_cd_perfil_estudo_seq\"'::text)";
                    if( $request->getParameter("e".$nEstudo."tm") != null && $request->getParameter("e".$nEstudo."tm").length() == 1 )
                        $xml .= ",'" . $request->getParameter("e".$nEstudo."tm") . "'"; // tipo da média
                    else
                        $xml .= ",'A'"; // tipo da média
                    $xml .= ")";

                    $retorno = $this->connIntranet->prepare($xml)->execute();
                    if( $analista && $this->connIntraRemoto )
                        try{ $this->connIntraRemoto->prepare($xml)->execute(); } catch(\PDOException $e){ $e->getTraceAsString(); }

                    $nEstudo++;
                }
            }

            $xml = "<retorno>" . $retorno . "</retorno>";

        }
        catch(\PDOException $e)
        {
            //echo "$sql Insert/Update Perfil: " . sb.to());
            $e->getTraceAsString();
        }

        return $xml;
    }

    public function excluiPerfilGrafico($request)
    {
        echo "Entrou no method excluiPerfilGrafico";exit;

        $xml = "";
        $retorno = 1;

        try
        {
            //boolean existe = false;

            $usuario = $request->getParameter("u");
            $empresa = $request->getParameter("e");
            $perfil  = $request->getParameter("p");
            $analista = $request->getParameter("analista")	!= null && $request->getParameter("analista") == "1";

            $cd_perfil = -1;

            $sql = "SELECT cd_perfil FROM perfil_grafico_java WHERE usuario = '" . $usuario . "' AND cd_empresa = " . $empresa . " AND nm_perfil = '" . $perfil . "'";
            $rs = $statementIntranet->executeQuery($sql);
            if( $rs->next() )
            {
                $cd_perfil = $rs->getInt(1);

                $sql = "DELETE FROM perfil_grafico_java_estudo WHERE cd_perfil = " . $cd_perfil;
                $statementIntranet->executeUpdate($sql);

                if( $analista && $this->conn ) //conexaoBancoIntranetRemoto
                    try{ $statementIntranetRemoto->executeUpdate($sql); } catch(\PDOException $e){ $e->getTraceAsString(); }

                if( $retorno == 1 )
                {
                    $sql = "DELETE FROM perfil_grafico_java WHERE cd_perfil = " . $cd_perfil;
                    $statementIntranet->executeUpdate($sql);

                    if( $analista && $this->conn ) //conexaoBancoIntranetRemoto
                        try{ $statementIntranetRemoto->executeUpdate($sql); } catch(\PDOException $e){ $e->getTraceAsString(); }
                }
            }
        }
        catch(\PDOException $e)
        {
            $retorno = 0;
            //e.printStackTrace();
            echo "Erro ao tentar excluir perfil";
        }

        return "<retorno>" . $retorno . "</retorno>";
    }

    public function getPerfisGrafico(Request $request)
    {

        $this->conn = new ConexoesDB();

        if( !$this->conn ) //conexaoBancoIntranet
        {
            echo "Nao pude recuperar Perfis por nao conseguir conexao com o banco INTRANET";
            return "";
        }
        $xml = "";

        try
        {
            //statement = connectionIntranet.createStatement();

            $usuario = $request->getParameter("u");
            $empresa = $request->getParameter("e");

            $sql = "SELECT nm_perfil FROM perfil_grafico_java WHERE usuario = '" . $usuario . "' AND cd_empresa = " . $empresa . " ORDER BY dh_ult_acesso DESC";

            $stmt = $this->conn->getInstance('intranet')->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//            echo '<pre>';
//            print_r($result);exit;

            $xml .= "<perfis>";

            foreach($result as $rs)
                $xml .= "<p>" . $rs['nm_perfil'] . "</p>";
            $xml .= "</perfis>";
        }
        catch(\PDOException $e)
        {

            echo "Erro ao tentar recuperar perfis: ".$e->getMessage();exit;
        }
        return $xml;
    }

    public function getPerfilGrafico(Request $request)
    {
//        echo "Entrou no method getPerfilGrafico";exit;

        $xml = "";


        try
        {
            $usuario = $request->getParameter("u");
            $empresa = $request->getParameter("e");
            $perfil  = $request->getParameter("p");

            $sql = "SELECT * FROM perfil_grafico_java WHERE usuario = '" . $usuario . "' AND cd_empresa = " . $empresa;
            if( $perfil != null && !empty($perfil) )
                $sql .= " AND nm_perfil = '" . $perfil . "'";
            else
                $sql .= " ORDER BY dh_ult_acesso DESC LIMIT 1";

            $this->conn = new ConexoesDB();

            $stmt = $this->conn->getInstance('intranet')->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);

            foreach($result as $rs )
            {
                $xml .= "<perfil>";
                $xml .= "<p>"      . $rs->nm_perfil         . "</p>";
                $xml .= "<bar>"    . $rs->barras            . "</bar>";
                $xml .= "<pm1>"    . $rs->periodo_mm1       . "</pm1>";
                $xml .= "<pm2>"    . $rs->periodo_mm2       . "</pm2>";
                $xml .= "<pm3>"    . $rs->periodo_mm3       . "</pm3>";
                $xml .= "<pm4>"    . $rs->periodo_mm4       . "</pm4>";
                $xml .= "<pm5>"    . $rs->periodo_mm5       . "</pm5>";
                $xml .= "<tm1>"    . $rs->tipo_mm1          . "</tm1>";
                $xml .= "<tm2>"    . $rs->tipo_mm2          . "</tm2>";
                $xml .= "<tm3>"    . $rs->tipo_mm3          . "</tm3>";
                $xml .= "<tm4>"    . $rs->tipo_mm4          . "</tm4>";
                $xml .= "<tm5>"    . $rs->tipo_mm5          . "</tm5>";
                $xml .= "<bc1>"    . $rs->bc_mm1            . "</bc1>";
                $xml .= "<bc2>"    . $rs->bc_mm2            . "</bc2>";
                $xml .= "<bc3>"    . $rs->bc_mm3            . "</bc3>";
                $xml .= "<bc4>"    . $rs->bc_mm4            . "</bc4>";
                $xml .= "<bc5>"    . $rs->bc_mm5            . "</bc5>";
                $xml .= "<dm1>"    . $rs->desloca_mm1       . "</dm1>";
                $xml .= "<dm2>"    . $rs->desloca_mm2       . "</dm2>";
                $xml .= "<dm3>"    . $rs->desloca_mm3       . "</dm3>";
                $xml .= "<dm4>"    . $rs->desloca_mm4       . "</dm4>";
                $xml .= "<dm5>"    . $rs->desloca_mm5       . "</dm5>";
                $xml .= "<vol>"    . $rs->volume            . "</vol>";
                $xml .= "<tvol>"   . $rs->tam_vol           . "</tvol>";
                $xml .= "<bol>"    . $rs->bollinger         . "</bol>";
                $xml .= "<pbol1>"  . $rs->boll_periodo1     . "</pbol1>";
                $xml .= "<pbol2>"  . $rs->boll_periodo2     . "</pbol2>";
                $xml .= "<pbol3>"  . $rs->boll_periodo3     . "</pbol3>";
                $xml .= "<par>"    . $rs->parabolico        . "</par>";
                $xml .= "<ppar1>"  . $rs->para_periodo1 	 . "</ppar1>";
                $xml .= "<ppar2>"  . $rs->para_periodo2     . "</ppar2>";
                $xml .= "<ppar3>"  . $rs->para_periodo3     . "</ppar3>";
                $xml .= "<nb>"     . $rs->nbarras           . "</nb>";
                $xml .= "<lb>"     . $rs->barras_esquerda   . "</lb>";
                $xml .= "<bl>"     . $rs->barras_livres     . "</bl>";
                $xml .= "<ec>"     . $rs->esquema_cor       . "</ec>";
                $xml .= "<env>"    . $rs->envelope          . "</env>";
                $xml .= "<pEnvM1>" . $rs->env_periodo1      . "</pEnvM1>";
                $xml .= "<pEnvM2>" . $rs->env_periodo2      . "</pEnvM2>";
                $xml .= "<pEnvM3>" . $rs->env_periodo3      . "</pEnvM3>";
                $xml .= "<bc>"     . $rs->base_calculo      . "</bc>";
                $xml .= "<ae>"     . $rs->alt_estudo        . "</ae>";
                $xml .= "<ms>"     . $rs->margem_superior   . "</ms>";
                $xml .= "<mi>"     . $rs->margem_inferior   . "</mi>";
                $xml .= "<es>"     . $rs->escala            . "</es>";
                $xml .= "<gr>"     . $rs->grid              . "</gr>";
                $xml .= "<mbol>"   . $rs->boll_media        . "</mbol>";
                $xml .= "<mr1>"    . $rs->rec_mm1           . "</mr1>";
                $xml .= "<mr2>"    . $rs->rec_mm2           . "</mr2>";
                $xml .= "<mr3>"    . $rs->rec_mm3           . "</mr3>";
                $xml .= "<mr4>"    . $rs->rec_mm4           . "</mr4>";
                $xml .= "<tf>"     . $rs->topos_fundos      . "</tf>";
                $xml .= "<vtf>"    . $rs->var_topos_fundos  . "</vtf>";
                $xml .= "<mtf>"    . $rs->modo_topos_fundos . "</mtf>";
                $xml .= "<btf>"    . $rs->base_topos_fundos . "</btf>";
                $xml .= "<sf>"     . $rs->stop_financeiro   . "</sf>";
                $xml .= "<pmv1>"   . $rs->periodo_mmv1      . "</pmv1>";
                $xml .= "<er>"     . $rs->escala_redonda    . "</er>";
                $xml .= "<cmp>"    . $rs->estudo_cmp        . "</cmp>";
                $xml .= "<ac>"     . $rs->ativos_cmp        . "</ac>";
                $xml .= "<lzc>"    . $rs->linha_zero_cmp    . "</lzc>";
                $xml .= "<sc>"     . $rs->simulador_cmp     . "</sc>";
                $xml .= "<vsc>"    . $rs->valor_simulado_cmp . "</vsc>";
                $xml .= "<as>"     . $rs->ativo_spr         . "</as>";
                $xml .= "<q1>"     . $rs->qtde1_spr         . "</q1>";
                $xml .= "<q2>"     . $rs->qtde2_spr         . "</q2>";
                $xml .= "<ss>"     . $rs->simulador_spr     . "</ss>";
                $xml .= "<gsp>"    . $rs->estudo_sobreposto . "</gsp>";
                $xml .= "<agsp>"   . $rs->ativos_sobrepostos. "</agsp>";
                $xml .= "<hlo>"  . $rs->highlow          . "</hlo>";
                $xml .= "<pHL1>" . $rs->hlo_periodo1     . "</pHL1>";
                $xml .= "<pHL2>" . $rs->hlo_periodo2     . "</pHL2>";
                $xml .= "<pHL3>" . $rs->hlo_periodo3     . "</pHL3>";
                $xml .= "<ktc>"  . $rs->ktc          . "</ktc>";
                $xml .= "<pKTC>" . $rs->ktc_periodo1 . "</pKTC>";
                $xml .= "<aed>" . $rs->aed_indices . "</aed>";
                $xml .= "<lad>" . $rs->lad_indices . "</lad>";
                $xml .= "<cha>" . $rs->cha_indices . "</cha>";
                $xml .= "<mos>" . $rs->mos_indices . "</mos>";
                $xml .= "<bth>" . $rs->bth_indices . "</bth>";
                $xml .= "<el>" . $rs->estilo_linha . "</el>";
                $xml .= "<vr>" . $rs->valor_reta_escala . "</vr>";
                $xml .= "<pvt>" . $rs->pivot . "</pvt>";
                $xml .= "<ph>" . $rs->pivot_historico . "</ph>";
                $xml .= "<pn>" . $rs->pivot_niveis . "</pn>";
                $xml .= "<hla>" . $rs->hilo_activator . "</hla>";
                $xml .= "<pHLAH>" . $rs->hla_mediah . "</pHLAH>";
                $xml .= "<pHLAL>" . $rs->hla_medial . "</pHLAL>";
                $xml .= "<cvd>" . $rs->cor_vol_diferenciado . "</cvd>";

                $xml .= "<ha>" . $rs->heikenashi . "</ha>";
                $xml .= "<hap1>" . $rs->ha_periodo1 . "</hap1>";
                $xml .= "<hap2>" . $rs->ha_periodo2 . "</hap2>";

                $xml .= "<pKTC2>" . $rs->ktc_periodo2 . "</pKTC2>";
                $xml .= "<pKTC3>" . $rs->ktc_periodo3 . "</pKTC3>";
                $xml .= "<cKTC2>" . $rs->ktc_coeficiente2 . "</cKTC2>";
                $xml .= "<cKTC3>" . $rs->ktc_coeficiente3 . "</cKTC3>";
                $xml .= "<bcKTC>" . $rs->ktc_plota_central . "</bcKTC>";

                $xml .= "<ftfc>" . $rs->furatetochao . "</ftfc>";
                $xml .= "<cftfc>" . $rs->ftc_coeficiente . "</cftfc>";
                $xml .= "<hftfc>" . $rs->ftc_historico . "</hftfc>";

                $xml .= "<jrs>" . $rs->joeross . "</jrs>";
                $xml .= "<pjrs>" . $rs->jrs_periodo . "</pjrs>";
                $xml .= "<cjrs1>" . $rs->jrs_coeficiente1 . "</cjrs1>";
                $xml .= "<cjrs2>" . $rs->jrs_coeficiente2 . "</cjrs2>";
                $xml .= "<ujrs>" . $rs->jrs_ultima_barra . "</ujrs>";

                $xml .= "<nic>" . $rs->nuvem_ichimoku . "</nic>";
                $xml .= "<ts>" . $rs->ni_tenkansen . "</ts>";
                $xml .= "<ks>" . $rs->ni_kijunsen . "</ks>";
                $xml .= "<dcs>" . $rs->ni_chikouspan . "</dcs>";
                $xml .= "<dssa>" . $rs->ni_senkouspana . "</dssa>";
                $xml .= "<dssb>" . $rs->ni_senkouspanb . "</dssb>";
                $xml .= "<ssb>" . $rs->ni_p_senkouspanb . "</ssb>";

                $xml .= "</perfil>";
            }


            try
            {

                $sql = "SELECT * FROM perfil_grafico_java_estudo WHERE cd_perfil = " . $rs->cd_perfil . " ORDER BY cd_perfil_estudo";
                $this->conn = new ConexoesDB();
                $stmt = $this->conn->getInstance('intranet')->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(\PDO::FETCH_OBJ);

                $nEstudo = 1;
                foreach($result as $rs)
                {
                    $xml .= "<estudo>";
                    $xml .= "<e".$nEstudo.">"    . $rs->estudo        . "</e".$nEstudo.">";
                    $xml .= "<e".$nEstudo."p1>"  . $rs->periodo1      . "</e".$nEstudo."p1>";
                    $xml .= "<e".$nEstudo."p2>"  . $rs->periodo2      . "</e".$nEstudo."p2>";
                    $xml .= "<e".$nEstudo."p3>"  . $rs->periodo3      . "</e".$nEstudo."p3>";
                    $xml .= "<e".$nEstudo."o>"   . $rs->oscilador     . "</e".$nEstudo."o>";
                    $xml .= "<e".$nEstudo."m>"   . $rs->media         . "</e".$nEstudo."m>";
                    $xml .= "<e".$nEstudo."bc>"  . $rs->base_calculo  . "</e".$nEstudo."bc>";
                    $xml .= "<e".$nEstudo."l>"   . $rs->linhas        . "</e".$nEstudo."l>";
                    $xml .= "<e".$nEstudo."tm>"  . $rs->tipo_media    . "</e".$nEstudo."tm>";
                    $xml .= "</estudo>";
                }
            }
            catch(\PDOException $e){}
        }
        catch(\PDOException $e)
        {
            $e->getTraceAsString();
            echo "Erro ao tentar recuperar perfil";
        }

        return $xml;
    }

}