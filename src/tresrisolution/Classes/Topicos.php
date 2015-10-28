<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:58
 */

namespace tresrisolution\Classes;


class Topicos extends GraficoServer
{
    private $conn;


    public function getTopico($ativo, $dh, $erro, $usuario, $corretora, $ultDHCorrecao, $ultDHAnalise, $listLicAnalises = array(), $bTemAnaliseGrauita, $modulos)
    {
        echo 'entrou em getTopico';exit;
        $l = 1212121212;//System.currentTimeMillis();Refatorar depois #####################################################################################


        if( $this->bLog )
            echo "Tempo para conectar BD: " . ($this->currentTimeMillis() - $l) . "ms";

        if( !$this->conn )
        {
            echo "Nao pude recuperar Topico por nao conseguir conexao com o banco COTACOES";
            return "";
        }

        $xml = "";

        $statementCotacoes = null;
        $rs = null;

        try
        {
            $l = $this->currentTimeMillis();

            $sql = "SELECT dh, abertura, maxima, minima, ultima, abertura_dia, maxima_dia, minima_dia, " .
                "volume, volume_dia, contratos_dia, fechamento, neg_dia, nm_ativo, cd_bolsa, ndec, vft_dia " .
                "FROM intra_diario i, cadastro_ativo c WHERE " .
                "i.codigo = c.codigo AND " .
                "i.codigo = '" . $ativo . "' ";
            if( $dh != null && !empty($dh) )
                $sql .= "AND dh > '" . str_replace("_", " ",$dh) . "' ";
            $sql .= "ORDER BY dh DESC LIMIT 1 ";


            $stmt = $this->conn->prepare($sql);;
            $stmt->execute();

            //echo new java.util.Date().toString() + " contingencia: usuario=" + usuario + " corretora=" + corretora + " erro=" + erro);

            if( $this->bLog )
                echo "Tempo de consulta para TOPICO: " . ($this->currentTimeMillis() - $l) . "ms";

            $xml .="<topico>";

            if( $rs = $stmt->nextRowset() )
            {
                $DATULT = $rs->dh;
                $DATULT = $DATULT.substring(0, 4) . $DATULT.substring(5, 7) . $DATULT.substring(8, 10);

                $HOR = $rs->dh;
                $HOR = $HOR.substring(11, 13) . $HOR.substring(14, 16) . $HOR.substring(17, 18) . "1"; // configura segundos=1 para caracterizar uma nova barra

                $xml .= "<dh>"; $xml .= $rs->dh; $xml .= "</dh>";

                $xmlTopico = "";

                $xmlTopico .= ativo; $xmlTopico .= ".ALL2\t";

                $xmlTopico .= $rs->nm_ativo;
                $xmlTopico .= "\t"; //NOM
                $xmlTopico .= $rs->abertura_dia;
                $xmlTopico .= "\t"; //ABE
                $xmlTopico .= $rs->maxima_dia;
                $xmlTopico .= "\t"; //MAX
                $xmlTopico .= $rs->minima_dia;
                $xmlTopico .= "\t"; //MIN
                $xmlTopico .= $rs->ultima;
                $xmlTopico .= "\t"; //ULT
                $xmlTopico .= "0\t"; //VAR
                $xmlTopico .= $rs->volume;
                $xmlTopico .= "\t"; //QUL
                $xmlTopico .= $rs->volume_dia;
                $xmlTopico .= "\t"; //VOL
                $xmlTopico .= $rs->vft_dia;
                $xmlTopico .= "\t"; //VFT
                $xmlTopico .= "0\t"; //MED
                $xmlTopico .= $rs->contratos_dia;
                $xmlTopico .= "\t"; //NGCA
                $xmlTopico .= "0\t"; //OCP
                $xmlTopico .= "0\t"; //OVD
                $xmlTopico .= "0\t"; //VOC
                $xmlTopico .= "0\t"; //VOV
                $xmlTopico .= $rs->fechamento;
                $xmlTopico .= "\t"; //FEC
                $xmlTopico .= "0\t"; //PEX
                $xmlTopico .= $DATULT;
                $xmlTopico .= "\t"; //DATULT
                $xmlTopico .= $HOR;
                $xmlTopico .= "\t"; //HOR
                $xmlTopico .= "0\t"; //SEQ
                $xmlTopico .= "0\t"; //CCP
                $xmlTopico .= "0\t"; //CVD
                $xmlTopico .= "0\t"; //VAB
                $xmlTopico .= "0\t"; //DUTE
                $xmlTopico .= "0\t"; //VENC
                $xmlTopico .= "0\t"; //COC
                $xmlTopico .= "0\t"; //COV
                $xmlTopico .= "0\t"; //NCOC
                $xmlTopico .= "0\t"; //NCOV
                $xmlTopico .= "0\t"; //NCC
                $xmlTopico .= "0\t"; //NCV
                $xmlTopico .= "0\t"; //NNG
                $xmlTopico .= "0\t"; //ATR
                $xmlTopico .= "0\t"; //ONG
                $xmlTopico .= "0\t"; //TNEG
                $xmlTopico .= "0\t"; //AJU
                $xmlTopico .= "0\t"; //VOLANT
                $xmlTopico .= "0\t"; //NEGANT
                $xmlTopico .= "0\t"; //VARABE
                $xmlTopico .= "0\t"; //VABABE
                $xmlTopico .= "0\t"; //VARAJU
                $xmlTopico .= "0\t"; //VABAJU
                $xmlTopico .= "0\t"; //DCOR
                $xmlTopico .= "0\t"; //DSAQ
                $xmlTopico .= "0\t"; //DATHOR
                $xmlTopico .= "0\t"; //LPA
                $xmlTopico .= "0\t"; //VPA
                $xmlTopico .= "0\t"; //NTIPO
                $xmlTopico .= "0\t"; //NBOLSA
                $xmlTopico .= "0\t"; //NPREGAO
                $xmlTopico .= "0\t"; //ABEANT
                $xmlTopico .= "0\t"; //AJUA
                $xmlTopico .= "0\t"; //AJUVOL
                $xmlTopico .= "0\t"; //BOLSA
                $xmlTopico .= "0\t"; //BUY
                $xmlTopico .= $rs->contratos_dia;
                $xmlTopico .= "\t"; //CAB
                $xmlTopico .= $rs->volume_dia;
                $xmlTopico .= "\t"; //CNG
                $xmlTopico .= "0\t"; //CNGANT
                $xmlTopico .= $DATULT;
                $xmlTopico .= "\t"; //DAT
                $xmlTopico .= "0\t"; //DATANT
                $xmlTopico .= "0\t"; //HBOP
                $xmlTopico .= "0\t"; //LBOP
                $xmlTopico .= "0\t"; //LOTE
                $xmlTopico .= "0\t"; //MAXANT
                $xmlTopico .= "0\t"; //MINANT
                $xmlTopico .= $rs->ndec;
                $xmlTopico .= "\t"; //NDEC
                $xmlTopico .= $rs->neg_dia;
                $xmlTopico .= "\t"; //NEG
                $xmlTopico .= "0\t"; //NOC
                $xmlTopico .= "0\t"; //NOV
                $xmlTopico .= "0\t"; //NTIT
                $xmlTopico .= "0\t"; //PTEOR
                $xmlTopico .= "0\t"; //QTEOR
                $xmlTopico .= $rs->volume_dia;
                $xmlTopico .= "\t"; //QTT
                $xmlTopico .= "0\t"; //QTTANT
                $xmlTopico .= "0\t"; //QTTM20
                $xmlTopico .= "0\t"; //QTTM5
                $xmlTopico .= "0\t"; //RES1
                $xmlTopico .= "0\t"; //RES2
                $xmlTopico .= "0\t"; //SELL
                $xmlTopico .= "0\t"; //SUP1
                $xmlTopico .= "0\t"; //SUP2
                $xmlTopico .= "0\t"; //TIPO
                $xmlTopico .= "0\t"; //UCOC
                $xmlTopico .= "0\t"; //UCOV
                $xmlTopico .= "0\t"; //UNCOC
                $xmlTopico .= "0\t"; //UNCOV
                $xmlTopico .= "0\t"; //UNOC
                $xmlTopico .= "0\t"; //UNOV
                $xmlTopico .= "0\t"; //UOCP
                $xmlTopico .= "0\t"; //UOVD
                $xmlTopico .= "0\t"; //UVOC
                $xmlTopico .= "0\t"; //UVOV
                $xmlTopico .= "0\t"; //VFTANT
                $xmlTopico .= "0\t"; //VFTM20
                $xmlTopico .= "0\t"; //VFTM5
                $xmlTopico .= "0\t"; //WALL
                $xmlTopico .= "-\t"; //-
                $xmlTopico .= "FIM\t"; //FIM
                $xmlTopico .= $rs->abertura;
                $xmlTopico .= "\t"; //ABEC
                $xmlTopico .= $rs->maxima;
                $xmlTopico .= "\t"; //MAXC
                $xmlTopico .= $rs->minima;
                $xmlTopico .= "\t"; //MINC


                $xml .= "<dados>";
                $xml .= $xmlTopico;
                $xml .= "</dados>";
            }
            else
            {
                $xml .= "<dh></dh>";
                $xml .= "<dados></dados>";
            }

            $xml .= "<correcao>";
            $xml .= "CORRECAO\t";
            $xml .= getHistoricosCorrigidos($ultDHCorrecao);
            $xml .= "</correcao>";

            $xml .= "<analise>";
            $xml .= "ANALISE\t";
            $xml .= getAnalises($ultDHAnalise, $listLicAnalises, $bTemAnaliseGrauita, $modulos);
            $xml .= "</analise>";

            $xml .= "</topico>";

        }
        catch(\PDOException $e)
        {
            $e->getTraceAsString();
        }


        return $xml;
    }

}