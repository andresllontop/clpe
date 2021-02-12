<?php

require_once './core/mainModel.php';

class videosModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_videos_modelo($conexion, $video)
    {
        $stmt = $conexion->prepare("INSERT INTO `videos`
        (nombre,imagen,video,enlace,ubicacion)
         VALUES(:Nombre,:Imagen,:Video,:Enlace,:Ubicacion)");
        $stmt->bindValue(":Nombre", $video->getNombre(), PDO::PARAM_STR);
        $stmt->bindValue(":Imagen", $video->getImagen(), PDO::PARAM_STR);
        $stmt->bindValue(":Video", $video->getArchivo(), PDO::PARAM_STR);
        $stmt->bindValue(":Enlace", $video->getEnlace(), PDO::PARAM_STR);
        $stmt->bindValue(":Ubicacion", $video->getUbicacion(), PDO::PARAM_INT);
        return $stmt;
    }
    protected function datos_videos_modelo($conexion, $tipo, $video)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idvideos) AS CONTADOR FROM `videos` WHERE idvideos=:Codigo");
                    $stmt->bindValue(":Codigo", $video->getIdvideo(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `videos` WHERE idvideos=:Codigo");
                            $stmt->bindValue(":Codigo", $video->getIdvideo(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insVideo = new Video();
                                $insVideo->setIdvideo($row['idvideos']);
                                $insVideo->setNombre($row['nombre']);
                                $insVideo->setImagen($row['imagen']);
                                $insVideo->setEnlace($row['enlace']);
                                $insVideo->setArchivo($row['video']);
                                $insVideo->setUbicacion($row['ubicacion']);

                                $insBeanPagination->setList($insVideo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idvideos) AS CONTADOR FROM `videos` ");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `videos` ORDER BY idvideos");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insVideo = new Video();
                                $insVideo->setIdvideo($row['idvideos']);
                                $insVideo->setNombre($row['nombre']);
                                $insVideo->setImagen($row['imagen']);
                                $insVideo->setEnlace($row['enlace']);
                                $insVideo->setArchivo($row['video']);
                                $insVideo->setUbicacion($row['ubicacion']);

                                $insBeanPagination->setList($insVideo->__toString());
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
    protected function eliminar_videos_modelo($conexion, $codigo)
    {
        $stmt = $conexion->prepare("DELETE FROM `videos` WHERE
        idvideos=:IDvideos ");
        $stmt->bindValue(":IDvideos", $codigo, PDO::PARAM_INT);
        return $stmt;
    }
    protected function actualizar_videos_modelo($conexion, $video)
    {
        $stmt = $conexion->prepare("UPDATE `videos`
        SET nombre=:Nombre,imagen=:Imagen,enlace=:Enlace,video=:Video,ubicacion=:Ubicacion WHERE idvideos=:ID ");
        $stmt->bindValue(":Nombre", $video->getNombre(), PDO::PARAM_STR);
        $stmt->bindValue(":Imagen", $video->getImagen(), PDO::PARAM_STR);
        $stmt->bindValue(":Video", $video->getArchivo(), PDO::PARAM_STR);
        $stmt->bindValue(":Enlace", $video->getEnlace(), PDO::PARAM_STR);
        $stmt->bindValue(":Ubicacion", $video->getUbicacion(), PDO::PARAM_INT);
        $stmt->bindValue(":ID", $video->getIdvideo(), PDO::PARAM_INT);

        return $stmt;
    }

    protected function datos_videos_tipo_modelo($conexion, $ubicacion)
    {

        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(idvideos) AS CONTADOR FROM `videos` WHERE ubicacion=:Ubicacion");
            $stmt->bindValue(":Ubicacion", $ubicacion, PDO::PARAM_INT);

            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `videos` WHERE ubicacion=:Ubicacion");
                    $stmt->bindValue(":Ubicacion", $ubicacion, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insVideo = new Video();
                        $insVideo->setIdvideo($row['idvideos']);
                        $insVideo->setNombre($row['nombre']);
                        $insVideo->setImagen($row['imagen']);
                        $insVideo->setEnlace($row['enlace']);
                        $insVideo->setArchivo($row['video']);
                        $insVideo->setUbicacion($row['ubicacion']);

                        $insBeanPagination->setList($insVideo->__toString());
                    }
                }
            }
            $stmt->closeCursor();
            $stmt = null;

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanPagination->__toString();

    }

}
