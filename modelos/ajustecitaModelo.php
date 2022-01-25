<?php

require_once './core/mainModel.php';

class ajustecitaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_ajustecita_modelo($conexion, $Ajustecita)
    {
        $sql = $conexion->prepare("INSERT INTO
        `ajuste_cita`(tipo,subtitulo)
        VALUES(:Tipo,:SubTitulo)");
        $sql->bindValue(":Tipo", $Ajustecita->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":SubTitulo", $Ajustecita->getSubtitulo(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_ajustecita_modelo($conexion, $tipo, $ajustecita)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idajuste_cita) AS CONTADOR FROM `ajuste_cita` WHERE idajuste_cita=:IDajustecita");
                    $stmt->bindValue(":IDajustecita", $ajustecita->getIdajusteCita(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `ajuste_cita` WHERE idajuste_cita=:IDajustecita");
                            $stmt->bindValue(":IDajustecita", $ajustecita->getIdajusteCita(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insAjustecita = new Ajustecita();
                                $insAjustecita->setIdajusteCita($row['idajuste_cita']);
                                $insAjustecita->setTipo($row['tipo']);
                                $insAjustecita->setSubtitulo($row['subtitulo']);

                                $insBeanPagination->setList($insAjustecita->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idajuste_cita) AS CONTADOR FROM `ajuste_cita`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `ajuste_cita`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insAjustecita = new Ajustecita();
                                $insAjustecita->setIdajusteCita($row['idajuste_cita']);
                                $insAjustecita->setTipo($row['tipo']);
                                $insAjustecita->setSubtitulo($row['subtitulo']);
                                $insBeanPagination->setList($insAjustecita->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                default:
                    # code...
                    break;
            }
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanPagination->__toString();

    }
    protected function eliminar_ajustecita_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM
     `ajuste_cita` WHERE  idajuste_cita=:ID ");
        $sql->bindValue(":ID", $id, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_ajustecita_modelo($conexion, $Ajustecita)
    {
        $sql = $conexion->prepare("UPDATE `ajuste_cita`
        SET subtitulo=:Titulo,tipo=:Tipo WHERE idajuste_cita=:ID");
        $sql->bindValue(":Titulo", $Ajustecita->getSubtitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $Ajustecita->getTipo(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $Ajustecita->getIdajusteCita(), PDO::PARAM_INT);
        return $sql;
    }

}
