<?php

require_once './core/mainModel.php';

class conferenciaModelo extends mainModel
{

    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_conferencia_modelo($conexion, $conferencia)
    {
        $sql = $conexion->prepare("INSERT INTO `conferencia`
            (link,descripcion,fecha,estado,imagen,titulo)
             VALUES(:Link,:Descripcion,:Fecha,:Estado,:Imagen,:Titulo)");
        $sql->bindValue(":Titulo", $conferencia->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Link", $conferencia->getLink(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $conferencia->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $conferencia->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Fecha", $conferencia->getFecha());
        $sql->bindValue(":Estado", $conferencia->getEstado(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_conferencia_modelo($conexion, $tipo, $conferencia)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idconferencia) AS CONTADOR FROM `conferencia` WHERE idconferencia=:IDconferencia");
                    $stmt->bindValue(":IDconferencia", $conferencia->getIdconferencia(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `conferencia` WHERE idconferencia=:IDconferencia");
                            $stmt->bindValue(":IDconferencia", $conferencia->getIdconferencia(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insConferencia = new Conferencia();
                                $insConferencia->setIdconferencia($row['idconferencia']);
                                $insConferencia->setLink($row['link']);
                                $insConferencia->setTitulo($row['titulo']);
                                $insConferencia->setFecha($row['fecha']);
                                $insConferencia->setDescripcion($row['descripcion']);
                                $insConferencia->setEstado($row['estado']);
                                $insConferencia->setImagen($row['imagen']);
                                $insBeanPagination->setList($insConferencia->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "fecha-add":
                    $stmt = $conexion->prepare("SELECT COUNT(idconferencia) AS CONTADOR FROM `conferencia` WHERE fecha=:Fecha");
                    $stmt->bindValue(":Fecha", $conferencia->getFecha(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `conferencia` WHERE fecha=:Fecha");
                            $stmt->bindValue(":Fecha", $conferencia->getFecha(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insConferencia = new Conferencia();
                                $insConferencia->setIdconferencia($row['idconferencia']);
                                $insConferencia->setLink($row['link']);
                                $insConferencia->setTitulo($row['titulo']);
                                $insConferencia->setFecha($row['fecha']);
                                $insConferencia->setDescripcion($row['descripcion']);
                                $insConferencia->setEstado($row['estado']);
                                $insConferencia->setImagen($row['imagen']);
                                $insBeanPagination->setList($insConferencia->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "fecha-update":
                    $stmt = $conexion->prepare("SELECT COUNT(idconferencia) AS CONTADOR FROM `conferencia` WHERE fecha=:Fecha and idconferencia!=:IDconferencia");
                    $stmt->bindValue(":Fecha", $conferencia->getFecha(), PDO::PARAM_STR);
                    $stmt->bindValue(":IDconferencia", $conferencia->getIdconferencia(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `conferencia` WHERE fecha=:Fecha");
                            $stmt->bindValue(":Fecha", $conferencia->getFecha(), PDO::PARAM_STR);
                            $stmt->bindValue(":IDconferencia", $conferencia->getIdconferencia(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insConferencia = new Conferencia();
                                $insConferencia->setIdconferencia($row['idconferencia']);
                                $insConferencia->setLink($row['link']);
                                $insConferencia->setTitulo($row['titulo']);
                                $insConferencia->setFecha($row['fecha']);
                                $insConferencia->setDescripcion($row['descripcion']);
                                $insConferencia->setEstado($row['estado']);
                                $insConferencia->setImagen($row['imagen']);
                                $insBeanPagination->setList($insConferencia->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idconferencia) AS CONTADOR FROM `conferencia`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `conferencia` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insConferencia = new Conferencia();
                                $insConferencia->setIdconferencia($row['idconferencia']);
                                $insConferencia->setLink($row['link']);
                                $insConferencia->setTitulo($row['titulo']);
                                $insConferencia->setFecha($row['fecha']);
                                $insConferencia->setDescripcion($row['descripcion']);
                                $insConferencia->setEstado($row['estado']);
                                $insConferencia->setImagen($row['imagen']);
                                $insBeanPagination->setList($insConferencia->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "alumno":
                    $stmt = $conexion->prepare("SELECT COUNT(idconferencia) AS CONTADOR FROM `conferencia`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `conferencia` ORDER BY fecha DESC LIMIT 4");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insConferencia = new Conferencia();
                                $insConferencia->setIdconferencia($row['idconferencia']);
                                $insConferencia->setTitulo($row['titulo']);
                                $insConferencia->setLink($row['link']);
                                $insConferencia->setFecha($row['fecha']);
                                $insConferencia->setDescripcion($row['descripcion']);
                                $insConferencia->setEstado($row['estado']);
                                $insConferencia->setImagen($row['imagen']);
                                $insBeanPagination->setList($insConferencia->__toString());
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
    protected function eliminar_conferencia_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM `conferencia` WHERE idconferencia=:IDconferencia ");
        $sql->bindValue(":IDconferencia", $id, PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_conferencia_modelo($conexion, $conferencia)
    {
        $sql = $conexion->prepare("UPDATE `conferencia`
            SET link=:Link,descripcion=:Descripcion,titulo=:Titulo,
            imagen=:Imagen,estado=:Estado,fecha=:Fecha
            WHERE idconferencia=:ID");
        $sql->bindValue(":Titulo", $conferencia->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Link", $conferencia->getLink(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $conferencia->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $conferencia->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Fecha", $conferencia->getFecha());
        $sql->bindValue(":Estado", $conferencia->getEstado(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $conferencia->getIdconferencia(), PDO::PARAM_INT);

        return $sql;
    }

}
