<?php

require_once './core/mainModel.php';

class citaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_cita_modelo($conexion, $Cita)
    {
        $sql = $conexion->prepare("INSERT INTO
        `cita`(tipo,cliente,subtitulo,estado_solicitud,fecha_solicitud,asunto)
        VALUES(:Tipo,:Cliente,:SubTitulo,:Estado,:Fecha,:Asunto)");
        $sql->bindValue(":Cliente", $Cita->getCliente(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $Cita->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Estado", $Cita->getEstadoSolicitud(), PDO::PARAM_INT);
        $sql->bindValue(":SubTitulo", $Cita->getSubtitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Asunto", $Cita->getAsunto(), PDO::PARAM_STR);
        $sql->bindValue(":Fecha", $Cita->getFechaSolicitud());
        return $sql;
    }
    protected function datos_cita_modelo($conexion, $tipo, $cita)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":

                    $stmt = $conexion->prepare("SELECT COUNT(idcita) AS CONTADOR FROM `cita` WHERE idcita=:IDcita");
                    $stmt->bindValue(":IDcita", $cita->getIdcita(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);

                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `cita`
                            WHERE idcita=:IDcita");
                            $stmt->bindValue(":IDcita", $cita->getIdcita(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $inscita = new Cita();
                                $inscita->setIdcita($row['idcita']);
                                $inscita->setTipo($row['tipo']);
                                $inscita->setAsunto($row['asunto']);
                                $inscita->setCliente($row['cliente']);
                                $inscita->setSubtitulo($row['subtitulo']);
                                $inscita->setEstadoSolicitud($row['estado_solicitud']);
                                $inscita->setFechaSolicitud($row['fecha_solicitud']);
                                $inscita->setFechaProgramada($row['fecha_rogramada']);
                                $inscita->setFechaAtendida($row['fecha_atendida']);
                                $inscita->setFechaAceptacion($row['fecha_aceptacion']);

                                $insBeanPagination->setList($inscita->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "add":
                        $stmt = $conexion->prepare("SELECT COUNT(idcita) AS CONTADOR FROM `cita` WHERE tipo=1 and subtitulo=:Subtitulo");
                        $stmt->bindValue(":Subtitulo", $cita->getSubtitulo(), PDO::PARAM_STR);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            $insBeanPagination->setCountFilter($row['CONTADOR']);
                            if ($row['CONTADOR'] > 0) {
                                $stmt = $conexion->prepare("SELECT * FROM `cita`
                                 WHERE tipo=1 and subtitulo=:Subtitulo");
                                $stmt->bindValue(":Subtitulo", $cita->getSubtitulo(), PDO::PARAM_STR);
                                $stmt->execute();
                                $datos = $stmt->fetchAll();
                                foreach ($datos as $row) {
    
                                    $inscita = new Cita();
                                    $inscita->setIdcita($row['idcita']);
                                    $inscita->setTipo($row['tipo']);
                                    $inscita->setAsunto($row['asunto']);
                                    $inscita->setCliente($row['cliente']);
                                    $inscita->setSubtitulo($row['subtitulo']);
                                    $inscita->setEstadoSolicitud($row['estado_solicitud']);
                                    $inscita->setFechaSolicitud($row['fecha_solicitud']);
                                    $inscita->setFechaProgramada($row['fecha_rogramada']);
                                    $inscita->setFechaAtendida($row['fecha_atendida']);
                                    $inscita->setFechaAceptacion($row['fecha_aceptacion']);
    
                                    $insBeanPagination->setList($inscita->__toString());
                                }
                            }
                        }
                        $stmt->closeCursor();
                        $stmt = null;
                        break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idcita) AS CONTADOR FROM `cita`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT cit.*
                            FROM `cita` as cit
                            INNER JOIN
                          (
                              SELECT  MAX(subtitulo) AS subtitulo
                              FROM `cita`
                              GROUP BY cliente
                          ) t2
                              ON  cit.subtitulo = t2.subtitulo
                            WHERE cit.tipo=1
                            GROUP BY cit.cliente ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $inscita = new Cita();
                                $inscita->setIdcita($row['idcita']);
                                $inscita->setTipo($row['tipo']);
                                $inscita->setAsunto($row['asunto']);
                                $inscita->setCliente($row['cliente']);
                                $inscita->setSubtitulo($row['subtitulo']);
                                $inscita->setEstadoSolicitud($row['estado_solicitud']);
                                $inscita->setFechaSolicitud($row['fecha_solicitud']);
                                $inscita->setFechaProgramada($row['fecha_rogramada']);
                                $inscita->setFechaAtendida($row['fecha_atendida']);
                                $inscita->setFechaAceptacion($row['fecha_aceptacion']);

                                $insBeanPagination->setList($inscita->__toString());
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
    protected function eliminar_cita_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM
     `cita` WHERE idcita=:ID ");
        $sql->bindValue(":ID", $id, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_cita_modelo($conexion, $Cita)
    {
        $sql = $conexion->prepare("UPDATE `cita`
        SET fecha_atendida=:FechaAtendida, estado_solicitud=:Estado  WHERE idcita=:ID");
        $sql->bindValue(":FechaAtendida", $Cita->getFechaAtendida(), PDO::PARAM_STR);
        $sql->bindValue(":Estado", $Cita->getEstadoSolicitud(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $Cita->getIdcita(), PDO::PARAM_INT);
        return $sql;
    }

}
