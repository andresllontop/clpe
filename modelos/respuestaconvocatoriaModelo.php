<?php

require_once './core/mainModel.php';

class respuestaconvocatoriaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_respuestaconvocatoria_modelo($conexion, $Persona)
    {
        $sql = $conexion->prepare("INSERT INTO `persona_convocatoria`
        (ip,fecha,cantidad,codigo_convocatoria)
         VALUES(?,?,?,?)");
        $sql->bindValue(1, $Persona->getIp(), PDO::PARAM_STR);
        $sql->bindValue(2, $Persona->getFecha(), PDO::PARAM_STR);
        $sql->bindValue(3, $Persona->getCantidad(), PDO::PARAM_INT);
        $sql->bindValue(4, $Persona->getCodigo(), PDO::PARAM_STR);

        return $sql;

    }
    protected function datos_respuestaconvocatoria_modelo($conexion, $tipo, $Convocatoria)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idpersona_convocatoria) AS CONTADOR FROM `persona_convocatoria` WHERE idpersona_convocatoria=:IDrespuestaconvocatoria");
                    $stmt->bindValue(":IDrespuestaconvocatoria", $Convocatoria->getIdPersonaConvocatoria(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `persona_convocatoria` WHERE idpersona_convocatoria=:IDrespuestaconvocatoria");
                            $stmt->bindValue(":IDrespuestaconvocatoria", $Convocatoria->getIdPersonaConvocatoria(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insConvocatoria = new PersonaConvocatoria();
                                $insConvocatoria->setIdPersonaConvocatoria($row['idpersona_convocatoria']);
                                $insConvocatoria->setCantidad($row['cantidad']);
                                $insConvocatoria->setIp($row['ip']);
                                $insConvocatoria->setFecha($row['fecha']);
                                $insConvocatoria->setCodigo($row['codigo_convocatoria']);
                                $insBeanPagination->setList($insConvocatoria->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idrespuestaconvocatoria) AS CONTADOR FROM `respuestaconvocatoria` ");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `respuestaconvocatoria`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insConvocatoria = new RespuestaConvocatoria();
                                $insConvocatoria->setIdRespuestaConvocatoria($row['idrespuesta_convocatoria']);
                                $insConvocatoria->setPregunta($row['pregunta']);
                                $insConvocatoria->setRespuesta($row['respuesta']);
                                $insConvocatoria->setFecha($row['fecha']);

                                $insBeanPagination->setList($insConvocatoria->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "detalle":
                    $stmt = $conexion->prepare("SELECT COUNT(idrespuesta_convocatoria) AS CONTADOR FROM `respuesta_convocatoria` WHERE idpersona_convocatoria=:ID");
                    $stmt->bindValue(":ID", $Convocatoria->getIdPersonaConvocatoria(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `respuesta_convocatoria` WHERE idpersona_convocatoria=:ID");
                            $stmt->bindValue(":ID", $Convocatoria->getIdPersonaConvocatoria(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insConvocatoria = new RespuestaConvocatoria();
                                $insConvocatoria->setIdRespuestaConvocatoria($row['idrespuesta_convocatoria']);
                                $insConvocatoria->setPregunta($row['pregunta']);
                                $insConvocatoria->setTipo($row['tipo']);
                                $insConvocatoria->setRespuesta($row['respuesta']);
                                $insBeanPagination->setList($insConvocatoria->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "unico-imagen":
                    $stmt = $conexion->prepare("SELECT COUNT(idrespuesta_convocatoria) AS CONTADOR FROM `respuesta_convocatoria` WHERE idpersona_convocatoria=:ID AND tipo=2");
                    $stmt->bindValue(":ID", $Convocatoria->getIdPersonaConvocatoria(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `respuesta_convocatoria` WHERE idpersona_convocatoria=:ID AND tipo=2");
                            $stmt->bindValue(":ID", $Convocatoria->getIdPersonaConvocatoria(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insConvocatoria = new RespuestaConvocatoria();
                                $insConvocatoria->setIdRespuestaConvocatoria($row['idrespuesta_convocatoria']);
                                $insConvocatoria->setPregunta($row['pregunta']);
                                $insConvocatoria->setTipo($row['tipo']);
                                $insConvocatoria->setRespuesta($row['respuesta']);
                                $insBeanPagination->setList($insConvocatoria->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
            }
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanPagination->__toString();

    }
    protected function eliminar_respuestaconvocatoria_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `respuesta_convocatoria`
        WHERE idpersona_convocatoria=:ID");
        $sql->bindValue(":ID", $codigo);
        return $sql;
    }
    protected function eliminar_personaconvocatoria_modelo($conexion, $codigo)
    {

        $sql = $conexion->prepare("DELETE FROM `persona_convocatoria`
        WHERE idpersona_convocatoria=:ID");
        $sql->bindValue(":ID", $codigo);
        return $sql;
    }

}
