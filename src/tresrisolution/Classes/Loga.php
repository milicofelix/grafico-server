<?php
/**
 * Created by PhpStorm.
 * User: milic
 * Date: 28/10/2015
 * Time: 16:51
 */

namespace tresrisolution\Classes;


class Loga
{
    private $conn;

    public function __construct(){

    }

    public function logaContingencia($erro, $usuario, $corretora)
    {

        try
        {
            if( $corretora == null || empty($corretora))
                $corretora = "-1";

            if( $usuario == null || empty($usuario))
                $usuario = "-";

            if( $erro == null || empty($erro))
                $erro = "-";

            $sql = "DELETE FROM smartweb_contingencia WHERE usuario = ? AND corretora = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1,$usuario, \PDO::PARAM_STR);
            $stmt->bindValue(2,$corretora, \PDO::PARAM_STR);
            $stmt->execute();

            $sql = "INSERT INTO smartweb_contingencia VALUES(?,?,?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1,$usuario, \PDO::PARAM_STR);
            $stmt->bindValue(2,$corretora, \PDO::PARAM_STR);
            $stmt->bindValue(3,'now()');
            $stmt->execute();

        }
        catch(\PDOException $e)
        {
            echo "Erro: ".$e->getMessage();
        }
    }

    public function logaEntrada(Request $request)
    {
        $id = -1;

        if( !$this->conn )

            return "";

        $rs = null;

        try
        {
            $usuario = $request->getParameter("u");
            $empresa = $request->getParameter("e");

            $sql = "INSERT INTO log_grafico_java (usuario, cd_empresa, dh_entrada) VALUES(?,?,?)";
            try
            {
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(1,$usuario, \PDO::PARAM_STR);
                $stmt->bindValue(2,$empresa, \PDO::PARAM_STR);
                $stmt->bindValue(3,'now()');
                $stmt->execute();
            }
            catch(\PDOException $e){}

            $sql = "SELECT id FROM log_grafico_java WHERE usuario= ? AND cd_empresa= ? AND dh_entrada::date= ? ORDER BY id DESC LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1,$usuario, \PDO::PARAM_STR);
            $stmt->bindValue(2,$empresa, \PDO::PARAM_STR);
            $stmt->bindValue(3,'now()');
            $stmt->execute();

            if( $rs = $stmt->nextRowset() )
                $id = $rs->id;
        }
        catch(\PDOException $e){}

        return "<entrada>" . $id . "</entrada>";
    }

    public function logaSaida(Request $request)
    {
        if( !$this->conn )

            return;

        try
        {
            $id = -1;
            try
            {
                $id = $request->getParameter("id");
            }catch(\Exception $e){}

            $sql = "UPDATE log_grafico_java set dh_saida= ? WHERE id= ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1,'now()', \PDO::PARAM_STR);
            $stmt->bindValue(2,$id, \PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(\PDOException $e) {}
    }
    public function logaStatusConexao(Request $request)
    {
        if( !$this->conn )
            return;

        $rs = null;

        try
        {
            $status = $request->getParameter("s");
            $id = -1;
            try
            {
                $id = $request->getParameter("id");
            }catch(\PDOException $e){}

            $sql = "SELECT status_conexao FROM log_grafico_java_sc WHERE id_conexao= ? ORDER BY dh DESC LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $id, \PDO::PARAM_INT);
            $stmt->execute();

            $statusAnterior = null;
            if( $rs = $stmt->nextRowset() )
                $statusAnterior = $rs->status_conexao;

            if( $statusAnterior == null || !$statusAnterior == "status" )
            {
                $sql = "INSERT INTO log_grafico_java_sc (id_conexao, dh, status_conexao) VALUES (?,?,?)";

                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(1, $id, \PDO::PARAM_INT);
                $stmt->bindValue(2,'now()');
                $stmt->bindValue(3,$status,\PDO::PARAM_STR);
                $stmt->execute();
            }
        }
        catch(\PDOException $e) {}

    }

}