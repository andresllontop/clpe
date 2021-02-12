<?php

require_once './core/mainModel.php';

class albumModelo extends mainModel
{

    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_album_modelo($conexion, $album)
    {
        $sql = $conexion->prepare("INSERT INTO `album` (nombre,desde,hasta,video,tipo,padre,tipo_archivo) VALUES(:Nombre,:Desde,:Hasta,:Video,:Tipo,:Padre,:TipoArchivo)");
        $sql->bindValue(":Nombre", $album->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Desde", $album->getDesde(), PDO::PARAM_STR);
        $sql->bindValue(":Hasta", $album->getHasta(), PDO::PARAM_STR);
        $sql->bindValue(":Video", $album->getVideo(), PDO::PARAM_STR);
        $sql->bindValue(":TipoArchivo", $album->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Tipo", 1, PDO::PARAM_INT);
        $sql->bindValue(":Padre", 0, PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_album_modelo($conexion, $tipo, $album)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idalbum) AS CONTADOR FROM `album` WHERE idalbum=:IDalbum");
                    $stmt->bindValue(":IDalbum", $album->getIdalbum(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `album` WHERE idalbum=:IDalbum");
                            $stmt->bindValue(":IDalbum", $album->getIdalbum(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insAlbum = new Album();
                                $insAlbum->setIdAlbum($row['idalbum']);
                                $insAlbum->setDesde($row['desde']);
                                $insAlbum->setHasta($row['hasta']);
                                $insAlbum->setVideo($row['video']);
                                $insBeanPagination->setList($insAlbum->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idalbum) AS CONTADOR FROM `album`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `album` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBeanPagination->setCountFilter($row['CONTADOR']);
                                if ($row['CONTADOR'] > 0) {
                                    $stmt = $conexion->prepare("SELECT * FROM `album` WHERE idalbum=:IDalbum");
                                    $stmt->bindValue(":IDalbum", $album->getIdalbum(), PDO::PARAM_INT);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {
                                        $insAlbum = new Album();
                                        $insAlbum->setIdAlbum($row['idalbum']);
                                        $insAlbum->setDesde($row['desde']);
                                        $insAlbum->setHasta($row['hasta']);
                                        $insAlbum->setVideo($row['video']);
                                        $insBeanPagination->setList($insAlbum->__toString());
                                    }
                                }
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
    protected function eliminar_album_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM `album` WHERE idalbum=:IDalbum");
        $sql->bindValue(":IDalbum", $id, PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_album_modelo($conexion, $album)
    {
        $sql = $conexion->prepare("UPDATE `album` SET nombre=:Nombre, desde=:Desde,hasta=:Hasta, video=:Video, tipo_archivo=:TipoArchivo WHERE idalbum=:ID");
        $sql->bindValue(":Nombre", $album->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Desde", $album->getDesde(), PDO::PARAM_STR);
        $sql->bindValue(":Hasta", $album->getHasta(), PDO::PARAM_STR);
        $sql->bindValue(":Video", $album->getVideo(), PDO::PARAM_STR);
        $sql->bindValue(":TipoArchivo", $album->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":ID", $album->getIdalbum(), PDO::PARAM_INT);

        return $sql;
    }

}
