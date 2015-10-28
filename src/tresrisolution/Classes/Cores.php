<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:44
 */

namespace tresrisolution\Classes;


class Cores
{
    private $conn;

    public function getCores(Request $request)
    {
        $this->conn = new ConexoesDB();

        if( $request->getParameter("u") == null || trim($request->getParameter("u")) =="")

            return "<cores></cores>";

        if( !$this->conn )
        {
            echo "Nao pude recuperar Cores por nao conseguir conexao com o banco INTRANET";

            return "";
        }

        $xml = "";

        $rs = null;

        try
        {

            $usuario = $request->getParameter("u");
            $empresa = $request->getParameter("e");

            $sql = "SELECT * FROM smartweb_cor_grafico WHERE nm_usuario = '$usuario' AND cd_empresa = $empresa ";
            $stmt = $this->conn->getInstance('intranet')->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);
//            echo '<pre>';
//            print_r($result);exit;

            $xml .= "<cores>";

            foreach($result as $rs)
            {
                $xml .= "<fb>"  . $rs->fundo_barras . "</fb>";
                $xml .= "<bg>"  . $rs->borda_grafico . "</bg>";
                $xml .= "<fj>"  . $rs->fundo_janela . "</fj>";
                $xml .= "<g>"   . $rs->grid . "</g>";
                $xml .= "<ca>"  . $rs->candle_alta . "</ca>";
                $xml .= "<cb>"  . $rs->candle_baixa . "</cb>";
                $xml .= "<cd>"  . $rs->candle_doji . "</cd>";
                $xml .= "<cao>" . $rs->candle_alta_on . "</cao>";
                $xml .= "<cbo>" . $rs->candle_baixa_on . "</cbo>";
                $xml .= "<ba>"  . $rs->barra_alta . "</ba>";
                $xml .= "<bb>"  . $rs->barra_baixa . "</bb>";
                $xml .= "<bao>" . $rs->barra_alta_on . "</bao>";
                $xml .= "<bbo>" . $rs->barra_baixa_on . "</bbo>";
                $xml .= "<ma>"  . $rs->marca_abe . "</ma>";
                $xml .= "<mf>"  . $rs->marca_fec . "</mf>";
                $xml .= "<l>"   . $rs->linha . "</l>";
                $xml .= "<m>"   . $rs->montanha . "</m>";
                $xml .= "<va>"  . $rs->volume_alta . "</va>";
                $xml .= "<vb>"  . $rs->volume_baixa . "</vb>";
                $xml .= "<ve>"  . $rs->volume_estavel . "</ve>";
                $xml .= "<obv>" . $rs->obv . "</obv>";
                $xml .= "<fv>"  . $rs->fundo_volume . "</fv>";
                $xml .= "<cua>" . $rs->cursor_ultima_alta . "</cua>";
                $xml .= "<cub>" . $rs->cursor_ultima_baixa . "</cub>";
                $xml .= "<cy>"  . $rs->cursor_y . "</cy>";
                $xml .= "<uca>" . $rs->ultima_cursor_alta . "</uca>";
                $xml .= "<ucb>" . $rs->ultima_cursor_baixa . "</ucb>";
                $xml .= "<cab>" . $rs->cabecalho . "</cab>";
                $xml .= "<ep>"  . $rs->escala_preco . "</ep>";
                $xml .= "<fua>" . $rs->fundo_ultima_alta . "</fua>";
                $xml .= "<fub>" . $rs->fundo_ultima_baixa . "</fub>";
                $xml .= "<fue>" . $rs->fundo_ultima_estavel . "</fue>";
                $xml .= "<tua>" . $rs->texto_ultima_alta . "</tua>";
                $xml .= "<tub>" . $rs->texto_ultima_baixa . "</tub>";
                $xml .= "<tue>" . $rs->texto_ultima_estavel . "</tue>";
                $xml .= "<ea>"  . $rs->escala_ano . "</ea>";
                $xml .= "<em1>" . $rs->escala_mes1 . "</em1>";
                $xml .= "<em2>" . $rs->escala_mes2 . "</em2>";
                $xml .= "<em>"  . $rs->escala_mes . "</em>";
                $xml .= "<ed1>" . $rs->escala_dia1 . "</ed1>";
                $xml .= "<ed2>" . $rs->escala_dia2 . "</ed2>";
            }

            // aba Overlay
            $sql = "SELECT * FROM smartweb_cor_overlay WHERE nm_usuario = '$usuario' AND cd_empresa = $empresa ";
            $stmt = $this->conn->getInstance('intranet')->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);

            foreach($result as $rs)
            {
                $xml .= "<mm1>" . $rs->media_movel1 . "</mm1>";
                $xml .= "<mm2>" . $rs->media_movel2 . "</mm2>";
                $xml .= "<mm3>" . $rs->media_movel3 . "</mm3>";
                $xml .= "<mm4>" . $rs->media_movel4 . "</mm4>";
                $xml .= "<mm5>" . $rs->media_movel5 . "</mm5>";
                $xml .= "<bs>" . $rs->bol_superior . "</bs>";
                $xml .= "<bc>" . $rs->bol_central . "</bc>";
                $xml .= "<bi>" . $rs->bol_inferior . "</bi>";
                $xml .= "<bf>" . $rs->bol_fundo . "</bf>";
                $xml .= "<hla>" . $rs->highlow_activator . "</hla>";
                $xml .= "<k1>" . $rs->keltner1 . "</k1>";
                $xml .= "<k2>" . $rs->keltner2 . "</k2>";
                $xml .= "<k3>" . $rs->keltner3 . "</k3>";
                $xml .= "<tf>" . $rs->toposfuntos . "</tf>";
                $xml .= "<s1>" . $rs->sobreposto1 . "</s1>";
                $xml .= "<s2>" . $rs->sobreposto2 . "</s2>";
                $xml .= "<s3>" . $rs->sobreposto3 . "</s3>";
                $xml .= "<s4>" . $rs->sobreposto4 . "</s4>";
                $xml .= "<s5>" . $rs->sobreposto5 . "</s5>";
                $xml .= "<sar>" . $rs->sar . "</sar>";
                $xml .= "<e1>" . $rs->envelope1 . "</e1>";
                $xml .= "<e2>" . $rs->envelope2 . "</e2>";
                $xml .= "<e3>" . $rs->envelope3 . "</e3>";
                $xml .= "<hl1>" . $rs->highlow1 . "</hl1>";
                $xml .= "<hl2>" . $rs->highlow2 . "</hl2>";
                $xml .= "<pv>" . $rs->pivot . "</pv>";
                $xml .= "<ps1>" . $rs->pivot_sup1 . "</ps1>";
                $xml .= "<ps2>" . $rs->pivot_sup2 . "</ps2>";
                $xml .= "<ps3>" . $rs->pivot_sup3 . "</ps3>";
                $xml .= "<pr1>" . $rs->pivot_res1 . "</pr1>";
                $xml .= "<pr2>" . $rs->pivot_res2 . "</pr2>";
                $xml .= "<pr3>" . $rs->pivot_res3 . "</pr3>";
                $xml .= "<c1>" . $rs->comparativo1 . "</c1>";
                $xml .= "<c2>" . $rs->comparativo2 . "</c2>";
                $xml .= "<c3>" . $rs->comparativo3 . "</c3>";
                $xml .= "<c4>" . $rs->comparativo4 . "</c4>";
                $xml .= "<c5>" . $rs->comparativo5 . "</c5>";
                $xml .= "<c6>" . $rs->comparativo6 . "</c6>";
                $xml .= "<p>" . $rs->provento . "</p>";
            }

            // aba Retas
            $sql = "SELECT * FROM smartweb_cor_ferramenta WHERE nm_usuario = '$usuario' AND cd_empresa = $empresa ";
            $stmt = $this->conn->getInstance('intranet')->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);
//                echo '<pre>';
//                print_r($result);exit;

            foreach($result as $rs)
            {
                $xml .= "<ms>" . $rs->magnetica_sup . "</ms>";
                $xml .= "<mr>" . $rs->magnetica_res . "</mr>";
                $xml .= "<mfs>" . $rs->magnetica_fec_sup . "</mfs>";
                $xml .= "<mfr>" . $rs->magnetica_fec_res . "</mfr>";
                $xml .= "<ps>" . $rs->projetada_sup . "</ps>";
                $xml .= "<pr>" . $rs->projetada_res . "</pr>";
                $xml .= "<fs>" . $rs->fixa_sup . "</fs>";
                $xml .= "<fr>" . $rs->fixa_res . "</fr>";
                $xml .= "<es>" . $rs->evolucao_sup . "</es>";
                $xml .= "<er>" . $rs->evolucao_res . "</er>";
                $xml .= "<f>" . $rs->fibonacci . "</f>";
                $xml .= "<r>" . $rs->retracement . "</r>";
                $xml .= "<fbr>" . $rs->fibo_retracement . "</fbr>";
                $xml .= "<fe>" . $rs->fibo_extension . "</fe>";
                $xml .= "<t>" . $rs->texto . "</t>";
                $xml .= "<td>" . $rs->texto_deslocado . "</td>";
                $xml .= "<dh>" . $rs->data_hora . "</dh>";
                $xml .= "<dhd>" . $rs->data_hora_deslocada . "</dhd>";
                $xml .= "<hs>" . $rs->horizontal_sup . "</hs>";
                $xml .= "<hr>" . $rs->horizontal_res . "</hr>";
                $xml .= "<hms>" . $rs->horizontal_mag_sup . "</hms>";
                $xml .= "<hmr>" . $rs->horizontal_mag_res . "</hmr>";
                $xml .= "<hmfs>" . $rs->horizontal_mag_fec_sup . "</hmfs>";
                $xml .= "<hmfr>" . $rs->horizontal_mag_fec_res . "</hmfr>";
                $xml .= "<hvus>" . $rs->horizontal_var_ult_sup . "</hvus>";
                $xml .= "<hvur>" . $rs->horizontal_var_ult_res . "</hvur>";
                $xml .= "<shs>" . $rs->stop_horizontal_sup . "</shs>";
                $xml .= "<shr>" . $rs->stop_horizontal_res . "</shr>";
                $xml .= "<ns>" . $rs->reta_nivel_sup . "</ns>";
                $xml .= "<nr>" . $rs->reta_nivel_res . "</nr>";
                $xml .= "<vy>" . $rs->valor_y . "</vy>";
                $xml .= "<vyd>" . $rs->valor_y_deslocado . "</vyd>";
                $xml .= "<el>" . $rs->elipse . "</el>";
                $xml .= "<re>" . $rs->retangulo . "</re>";
            }

            // aba Estudos
            $sql = "SELECT * FROM smartweb_cor_estudo WHERE nm_usuario = '$usuario' AND cd_empresa = $empresa ";
            $stmt = $this->conn->getInstance('intranet')->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);
//                    echo '<pre>';
//                    print_r($result);exit;

            foreach($result as $rs)
            {
                $xml .= "<l1>" . $rs->linha1 . "</l1>";
                $xml .= "<l2>" . $rs->linha2 . "</l2>";
                $xml .= "<l3>" . $rs->linha3 . "</l3>";
                $xml .= "<l4>" . $rs->linha4 . "</l4>";
                $xml .= "<l5>" . $rs->linha5 . "</l5>";
                $xml .= "<hp>" . $rs->histograma_positivo . "</hp>";
                $xml .= "<hn>" . $rs->histograma_negativo . "</hn>";
                $xml .= "<n1>" . $rs->nivel1 . "</n1>";
                $xml .= "<n2>" . $rs->nivel2 . "</n2>";
                $xml .= "<n3>" . $rs->nivel3 . "</n3>";
                $xml .= "<f1>" . $rs->fundo1 . "</f1>";
                $xml .= "<f2>" . $rs->fundo2 . "</f2>";
            }

            $xml .= "</cores>";

        }
        catch(\PDOException $e)
        {
            echo "Erro ao tentar recuperar cores: ". $e->getMessage();
        }

        return $xml;
    }

    public function salvaCores(Request $request)
    {
        $this->conn = new ConexoesDB();

        if( $request->getParameter("u") == null || trim($request->getParameter("u")) == "" )
            return "<retorno>0</retorno>";


        if( !$this->conn )
        {
            echo "Nao pude salvar Cores por nao conseguir conexao com o banco INTRANET";
            return "";
        }

        $xml = "";
        $rs = null;

        try
        {
            $usuario = $request->getParameter("u");
            $empresa = $request->getParameter("e");

            $bExiste = false;

            $sql = "SELECT nm_usuario FROM smartweb_cor_grafico WHERE nm_usuario = '$usuario' AND cd_empresa = $empresa ";
            $stmt = $this->conn->getInstance('intranet')->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
//                    echo '<pre>';
//                    print_r($stmt->execute());exit;

            if( $result )
                $bExiste = true;

            if( !$bExiste )
            {

                $xml = "";
                $xml .= "INSERT INTO smartweb_cor_grafico VALUES(
                                                                      '$usuario'
                                                                      ,$empresa
                                                                      ,'".$request->getParameter('fb')."'"."
                                                                       ,'".$request->getParameter('bg')."'"."
                                                                       ,'".$request->getParameter('fj')."'"."
                                                                       ,'".$request->getParameter('g')."'"."
                                                                       ,'".$request->getParameter('ca')."'"."
                                                                       ,'".$request->getParameter('cb')."'"."
                                                                       ,'".$request->getParameter('cd')."'"."
                                                                       ,'".$request->getParameter('cao')."'"."
                                                                       ,'".$request->getParameter('cbo')."'"."
                                                                       ,'".$request->getParameter('ba')."'"."
                                                                       ,'".$request->getParameter('bb')."'"."
                                                                       ,'".$request->getParameter('bao')."'"."
                                                                       ,'".$request->getParameter('bbo')."'"."
                                                                       ,'".$request->getParameter('ma')."'"."
                                                                       ,'".$request->getParameter('mf')."'"."
                                                                       ,'".$request->getParameter('l')."'"."
                                                                       ,'".$request->getParameter('m')."'"."
                                                                       ,'".$request->getParameter('va')."'"."
                                                                       ,'".$request->getParameter('vb')."'"."
                                                                       ,'".$request->getParameter('ve')."'"."
                                                                       ,'".$request->getParameter('obv')."'"."
                                                                       ,'".$request->getParameter('fv')."'"."
                                                                       ,'".$request->getParameter('cua')."'"."
                                                                       ,'".$request->getParameter('cub')."'"."
                                                                       ,'".$request->getParameter('cy')."'"."
                                                                       ,'".$request->getParameter('uca')."'"."
                                                                       ,'".$request->getParameter('ucb')."'"."
                                                                       ,'".$request->getParameter('cab')."'"."
                                                                       ,'".$request->getParameter('ep')."'"."
                                                                       ,'".$request->getParameter('fua')."'"."
                                                                       ,'".$request->getParameter('fub')."'"."
                                                                       ,'".$request->getParameter('fue')."'"."
                                                                       ,'".$request->getParameter('tua')."'"."
                                                                       ,'".$request->getParameter('tub')."'"."
                                                                       ,'".$request->getParameter('tue')."'"."
                                                                       ,'".$request->getParameter('ea')."'"."
                                                                       ,'".$request->getParameter('em1')."'"."
                                                                       ,'".$request->getParameter('em2')."'"."
                                                                       ,'".$request->getParameter('em')."'"."
                                                                       ,'".$request->getParameter('ed1')."'"."
                                                                       ,'".$request->getParameter('fua')."'"."
                                                                    )";

            }
            else
            {
                $xml = "";
                $xml .= "UPDATE smartweb_cor_grafico SET
                                                            dh = 'now()'
                                                            , fundo_barras = '".$request->getParameter('fb')."'"."
                                                            , borda_grafico = '".$request->getParameter('bg')."'"."
                                                            , fundo_janela = '".$request->getParameter('fj')."'"."
                                                            , grid = '".$request->getParameter('g')."'"."
                                                            , candle_alta = '".$request->getParameter('ca')."'"."
                                                            , candle_baixa = '".$request->getParameter('cb')."'"."
                                                            , candle_doji = '".$request->getParameter('cd')."'"."
                                                            , candle_alta_on = '".$request->getParameter('cao')."'"."
                                                            ,candle_baixa_on = '".$request->getParameter('cbo')."'"."
                                                            , barra_alta = '".$request->getParameter('ba')."'"."
                                                            , barra_baixa = '".$request->getParameter('bb')."'"."
                                                            , barra_alta_on = '".$request->getParameter('bao')."'"."
                                                            , barra_baixa_on = '".$request->getParameter('bbo')."'"."
                                                            , marca_abe = '".$request->getParameter('ma')."'"."
                                                            , marca_fec = '".$request->getParameter('mf')."'"."
                                                            , linha = '".$request->getParameter('l')."'"."
                                                            , montanha = '".$request->getParameter('m')."'"."
                                                            ,volume_alta = '".$request->getParameter('va')."'"."
                                                            , volume_baixa = '".$request->getParameter('vb')."'"."
                                                            , volume_estavel = '".$request->getParameter('ve')."'"."
                                                            , obv = '".$request->getParameter('obv')."'"."
                                                            , fundo_volume = '".$request->getParameter('fv')."'"."
                                                            , cursor_ultima_alta = '".$request->getParameter('cua')."'"."
                                                            , cursor_ultima_baixa = '".$request->getParameter('cub')."'"."
                                                            , cursor_y = '".$request->getParameter('cy')."'"."
                                                            ,ultima_cursor_alta = '".$request->getParameter('uca')."'"."
                                                            , ultima_cursor_baixa = '".$request->getParameter('ucb')."'"."
                                                            , cabecalho = '".$request->getParameter('cab')."'"."
                                                            , escala_preco = '".$request->getParameter('ep')."'"."
                                                            , fundo_ultima_alta = '".$request->getParameter('fua')."'"."
                                                            , fundo_ultima_baixa = '".$request->getParameter('fub')."'"."
                                                            , fundo_ultima_estavel = '".$request->getParameter('fue')."'"."
                                                            ,texto_ultima_alta = '".$request->getParameter('tua')."'"."
                                                            , texto_ultima_baixa = '".$request->getParameter('tub')."'"."
                                                            , texto_ultima_estavel = '".$request->getParameter('tue')."'"."
                                                            , escala_ano = '".$request->getParameter('ea')."'"."
                                                            , escala_mes1 = '".$request->getParameter('em1')."'"."
                                                            , escala_mes2 = '".$request->getParameter('em2')."'"."
                                                            , escala_mes = '".$request->getParameter('em')."'"."
                                                            , escala_dia1 = '".$request->getParameter('ed1')."'"."
                                                            ,escala_dia2 = '".$request->getParameter('ed2')."'"."
                                                        WHERE nm_usuario = $usuario
                                                        AND cd_empresa = $empresa";
            }

            $retorno = $stmt->execute();


            // aba Overlay
            $bExiste = false;
            $xml = "";
            $xml .= "SELECT nm_usuario FROM smartweb_cor_overlay WHERE nm_usuario = '$usuario' AND cd_empresa = $empresa ";
            $stmt = $this->conn->getInstance('intranet')->prepare($xml);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
//                    echo '<pre>';
//                    print_r($stmt);exit;

            if( $result )
                $bExiste = true;

            if( !$bExiste )
            {
                $xml = "";
                $xml .= "INSERT INTO smartweb_cor_overlay VALUES(
                                                                      '$usuario'
                                                                      ,$empresa
                                                                      ,'now()'
                                                                      ,'".$request->getParameter('mm1')."'"."
                                                                      ,'".$request->getParameter('mm2')."'"."
                                                                      ,'".$request->getParameter('mm3')."'"."
                                                                      ,'".$request->getParameter('mm4')."'"."
                                                                      ,'".$request->getParameter('mm5')."'"."
                                                                      ,'".$request->getParameter('bs')."'"."
                                                                      ,'".$request->getParameter('bc')."'"."
                                                                      ,'".$request->getParameter('bi')."'"."
                                                                      ,'".$request->getParameter('bf')."'"."
                                                                      ,'".$request->getParameter('hla')."'"."
                                                                      ,'".$request->getParameter('k1')."'"."
                                                                      ,'".$request->getParameter('k2')."'"."
                                                                      ,'".$request->getParameter('k3')."'"."
                                                                      ,'".$request->getParameter('tf')."'"."
                                                                      ,'".$request->getParameter('s1')."'"."
                                                                      ,'".$request->getParameter('s2')."'"."
                                                                      ,'".$request->getParameter('s3')."'"."
                                                                      ,'".$request->getParameter('s4')."'"."
                                                                      ,'".$request->getParameter('s5')."'"."
                                                                      ,'".$request->getParameter('sar')."'"."
                                                                      ,'".$request->getParameter('e1')."'"."
                                                                      ,'".$request->getParameter('e2')."'"."
                                                                      ,'".$request->getParameter('e3')."'"."
                                                                      ,'".$request->getParameter('hl1')."'"."
                                                                      ,'".$request->getParameter('hl2')."'"."
                                                                      ,'".$request->getParameter('pv')."'"."
                                                                      ,'".$request->getParameter('ps1')."'"."
                                                                      ,'".$request->getParameter('ps2')."'"."
                                                                      ,'".$request->getParameter('ps3')."'"."
                                                                      ,'".$request->getParameter('pr1')."'"."
                                                                      ,'".$request->getParameter('pr2')."'"."
                                                                      ,'".$request->getParameter('pr3')."'"."
                                                                      ,'".$request->getParameter('c1')."'"."
                                                                      ,'".$request->getParameter('c2')."'"."
                                                                      ,'".$request->getParameter('c3')."'"."
                                                                      ,'".$request->getParameter('c4')."'"."
                                                                      ,'".$request->getParameter('c5')."'"."
                                                                      ,'".$request->getParameter('c6')."'"."
                                                                      ,'".$request->getParameter('p')."'"."
                                                                    )";

            }
            else
            {
                $xml = "";
                $xml .= "UPDATE smartweb_cor_overlay SET
                                                              dh = 'now()'
                                                            , media_movel1 = '".$request->getParameter('mm1')."'"."
                                                            , media_movel2 = '".$request->getParameter('mm2')."'"."
                                                            , media_movel3 = '".$request->getParameter('mm3')."'"."
                                                            , media_movel4 = '".$request->getParameter('mm4')."'"."
                                                            , media_movel5 = '".$request->getParameter('mm5')."'"."
                                                            , bol_superior = '".$request->getParameter('bs')."'"."
                                                            , bol_central  = '".$request->getParameter('bc')."'"."
                                                            , bol_inferior = '".$request->getParameter('bi')."'"."
                                                            , bol_fundo    = '".$request->getParameter('bf')."'"."
                                                            , highlow_activator = '".$request->getParameter('hla')."'"."
                                                            , keltner1 = '".$request->getParameter('k1')."'"."
                                                            , keltner2 = '".$request->getParameter('k2')."'"."
                                                            , keltner3 = '".$request->getParameter('k3')."'"."
                                                            , toposfuntos = '".$request->getParameter('tf')."'"."
                                                            , sobreposto1 = '".$request->getParameter('s1')."'"."
                                                            , sobreposto2 = '".$request->getParameter('s2')."'"."
                                                            , sobreposto3 = '".$request->getParameter('s3')."'"."
                                                            , sobreposto4 = '".$request->getParameter('s4')."'"."
                                                            , sobreposto5 = '".$request->getParameter('s5')."'"."
                                                            , sar = '".$request->getParameter('sar')."'"."
                                                            , envelope1 = '".$request->getParameter('e1')."'"."
                                                            , envelope2 = '".$request->getParameter('e2')."'"."
                                                            , envelope3 = '".$request->getParameter('e3')."'"."
                                                            , highlow1 = '".$request->getParameter('hl1')."'"."
                                                            , highlow2 = '".$request->getParameter('hl2')."'"."
                                                            , pivot = '".$request->getParameter('pv')."'"."
                                                            , pivot_sup1 = '".$request->getParameter('ps1')."'"."
                                                            , pivot_sup2 = '".$request->getParameter('ps2')."'"."
                                                            ,pivot_sup3 = '".$request->getParameter('ps3')."'"."
                                                            , pivot_res1 = '".$request->getParameter('pr1')."'"."
                                                            , pivot_res2 = '".$request->getParameter('pr2')."'"."
                                                            , pivot_res3 = '".$request->getParameter('pr3')."'"."
                                                            , comparativo1 = '".$request->getParameter('c1')."'"."
                                                            , comparativo2 = '".$request->getParameter('c2')."'"."
                                                            , comparativo3 = '".$request->getParameter('c3')."'"."
                                                            , comparativo4 = '".$request->getParameter('c4')."'"."
                                                            , comparativo5 = '".$request->getParameter('c5')."'"."
                                                            ,comparativo6 = '".$request->getParameter('c6')."'"."
                                                            ,provento  = '".$request->getParameter('p')."'"."
                                                        WHERE nm_usuario = '$usuario'
                                                        AND cd_empresa = $empresa";
            }

            $retorno = $stmt->execute();


            // aba Retas
            $bExiste = false;
            $xml = "";
            $xml .= "SELECT nm_usuario
                        FROM smartweb_cor_ferramenta
                        WHERE nm_usuario = '$usuario'
                        AND cd_empresa = $empresa";

            $stmt = $this->conn->getInstance('intranet')->prepare($xml);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
//                    echo '<pre>';
//                    print_r($stmt);exit;

            if( $result )
                $bExiste = true;

            if( !$bExiste )
            {

                $xml = "";
                $xml .= "INSERT INTO smartweb_cor_ferramenta VALUES(
                                                                         $usuario
                                                                         ,$empresa
                                                                         ,'now()'
                                                                         ,'".$request->getParameter('ms')."'"."
                                                                         ,'".$request->getParameter('mr')."'"."
                                                                         ,'".$request->getParameter('mfs')."'"."
                                                                         ,'".$request->getParameter('mfr')."'"."
                                                                         ,'".$request->getParameter('ps')."'"."
                                                                         ,'".$request->getParameter('pr')."'"."
                                                                         ,'".$request->getParameter('fs')."'"."
                                                                         ,'".$request->getParameter('fr')."'"."
                                                                         ,'".$request->getParameter('es')."'"."
                                                                         ,'".$request->getParameter('er')."'"."
                                                                         ,'".$request->getParameter('f')."'"."
                                                                         ,'".$request->getParameter('r')."'"."
                                                                         ,'".$request->getParameter('fbr')."'"."
                                                                         ,'".$request->getParameter('fe')."'"."
                                                                         ,'".$request->getParameter('t')."'"."
                                                                         ,'".$request->getParameter('td')."'"."
                                                                         ,'".$request->getParameter('dh')."'"."
                                                                         ,'".$request->getParameter('dhd')."'"."
                                                                         ,'".$request->getParameter('hs')."'"."
                                                                         ,'".$request->getParameter('hr')."'"."
                                                                         ,'".$request->getParameter('hms')."'"."
                                                                         ,'".$request->getParameter('hmr')."'"."
                                                                         ,'".$request->getParameter('hmfs')."'"."
                                                                         ,'".$request->getParameter('hmfr')."'"."
                                                                         ,'".$request->getParameter('hvus')."'"."
                                                                         ,'".$request->getParameter('hvur')."'"."
                                                                         ,'".$request->getParameter('shs')."'"."
                                                                         ,'".$request->getParameter('shr')."'"."
                                                                         ,'".$request->getParameter('ns')."'"."
                                                                         ,'".$request->getParameter('nr')."'"."
                                                                         ,'".$request->getParameter('vy')."'"."
                                                                         ,'".$request->getParameter('vyd')."'"."
                                                                         ,'".$request->getParameter('el')."'"."
                                                                         ,'".$request->getParameter('re')."'"."
                                                                        )";

            }
            else
            {
                $xml = "";
                $xml .= "UPDATE smartweb_cor_ferramenta SET
                                                                dh = 'now()'
                                                                , magnetica_sup = '".$request->getParameter('ms')."'"."
                                                                , magnetica_res = '".$request->getParameter('mr')."'"."
                                                                , magnetica_fec_sup = '".$request->getParameter('mfs')."'"."
                                                                , magnetica_fec_res = '".$request->getParameter('mfr')."'"."
                                                                , projetada_sup = '".$request->getParameter('ps')."'"."
                                                                , projetada_res = '".$request->getParameter('pr')."'"."
                                                                , fixa_sup = '".$request->getParameter('fs')."'"."
                                                                ,fixa_res = '".$request->getParameter('fr')."'"."
                                                                , evolucao_sup = '".$request->getParameter('es')."'"."
                                                                , evolucao_res = '".$request->getParameter('er')."'"."
                                                                , fibonacci = '".$request->getParameter('f')."'"."
                                                                , retracement = '".$request->getParameter('r')."'"."
                                                                , fibo_retracement = '".$request->getParameter('fbr')."'"."
                                                                , fibo_extension = '".$request->getParameter('fe')."'"."
                                                                , texto = '".$request->getParameter('t')."'"."
                                                                , texto_deslocado = '".$request->getParameter('td')."'"."
                                                                ,data_hora = '".$request->getParameter('dh')."'"."
                                                                , data_hora_deslocada = '".$request->getParameter('dhd')."'"."
                                                                , horizontal_sup = '".$request->getParameter('hs')."'"."
                                                                , horizontal_res = '".$request->getParameter('hr')."'"."
                                                                , horizontal_mag_sup = '".$request->getParameter('hms')."'"."
                                                                , horizontal_mag_res = '".$request->getParameter('hmr')."'"."
                                                                , horizontal_mag_fec_sup = '".$request->getParameter('hmfs')."'"."
                                                                ,horizontal_mag_fec_res	 = '".$request->getParameter('hmfr')."'"."
                                                                , horizontal_var_ult_sup = '".$request->getParameter('hvus')."'"."
                                                                , horizontal_var_ult_res = '".$request->getParameter('hvur')."'"."
                                                                , stop_horizontal_sup = '".$request->getParameter('shs')."'"."
                                                                , stop_horizontal_res = '".$request->getParameter('shr')."'"."
                                                                , reta_nivel_sup = '".$request->getParameter('ns')."'"."
                                                                ,reta_nivel_res = '".$request->getParameter('nr')."'"."
                                                                , valor_y = '".$request->getParameter('vy')."'"."
                                                                , valor_y_deslocado = '".$request->getParameter('vyd')."'"."
                                                                , elipse = '".$request->getParameter('el')."'"."
                                                                , retangulo = '".$request->getParameter('re')."'"."
                                                            WHERE nm_usuario = '$usuario'
                                                            AND cd_empresa = $empresa";
            }

            $retorno = $stmt->execute();

            // aba Estudos
            $bExiste = false;
            $xml = "";
            $xml .= "SELECT nm_usuario FROM smartweb_cor_estudo WHERE nm_usuario = '$usuario' AND cd_empresa = $empresa ";
            $stmt = $this->conn->getInstance('intranet')->prepare($xml);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
//                        echo '<pre>';
//                        print_r($result);exit;

            if($result)
                $bExiste = true;

            if( !$bExiste )
            {
                $xml = "";
                $xml .= "INSERT INTO smartweb_cor_estudo VALUES(
                                                                    '$usuario'
                                                                    ,$empresa
                                                                    ,'now()'
                                                                    ,'".$request->getParameter('linha1')."'"."
                                                                    ,'".$request->getParameter('linha2')."'"."
                                                                    ,'".$request->getParameter('linha3')."'"."
                                                                    ,'".$request->getParameter('linha4')."'"."
                                                                    ,'".$request->getParameter('linha5')."'"."
                                                                    ,'".$request->getParameter('histograma_positivo')."'"."
                                                                    ,'".$request->getParameter('histograma_negativo')."'"."
                                                                    ,'".$request->getParameter('nivel1')."'"."
                                                                    ,'".$request->getParameter('nivel2')."'"."
                                                                    ,'".$request->getParameter('nivel3')."'"."
                                                                    ,'".$request->getParameter('fundo1')."'"."
                                                                    ,'".$request->getParameter('fundo2')."'"."
                                                                   )";

            }
            else
            {
                $xml = "";
                $xml .= "UPDATE smartweb_cor_estudo SET
                                                            dh = now()
                                                            , linha1 = '".$request->getParameter('linha1')."'"."
                                                            , linha2 = '".$request->getParameter('linha2')."'"."
                                                            , linha3 = '".$request->getParameter('linha3')."'"."
                                                            , linha4 = '".$request->getParameter('linha4')."'"."
                                                            , linha5 = '".$request->getParameter('linha5')."'"."
                                                            , histograma_positivo = '".$request->getParameter('histograma_positivo')."'"."
                                                            , histograma_negativo = '".$request->getParameter('histograma_negativo')."'"."
                                                            , nivel1 = '".$request->getParameter('nivel1')."'"."
                                                            , nivel2 = '".$request->getParameter('nivel2')."'"."
                                                            , nivel3 = '".$request->getParameter('nivel3')."'"."
                                                            ,fundo1 = '".$request->getParameter('fundo1')."'"."
                                                            , fundo2 = '".$request->getParameter('fundo2')."'"."
                                                         WHERE nm_usuario = '$usuario'
                                                         AND cd_empresa = $empresa";
            }

            $retorno = $stmt->execute();

            $xml = "<retorno>" . $retorno . "</retorno>";
        }
        catch(\PDOException $e)
        {
            echo $e->getTraceAsString();
        }

        return $xml;
    }

}