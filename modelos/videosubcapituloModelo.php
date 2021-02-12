<?php

require_once './core/mainModel.php';

class videosubcapituloModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_videosubcapitulo_modelo($conexion, $VideoSubTitulo)
    {
        $stmt = $conexion->prepare("INSERT INTO `video_subtitulo`
            (subtitulo_codigosubtitulo,nombreVideo,codigovideo_subtitulo)
             VALUES(:CodigoSubtitulo,:Video,:CodigoParrafo)");
        $stmt->bindValue(":Video", $VideoSubTitulo->getNombre(), PDO::PARAM_STR);
        $stmt->bindValue(":CodigoParrafo", $VideoSubTitulo->getCodigo(), PDO::PARAM_STR);
        $stmt->bindValue(":CodigoSubtitulo", $VideoSubTitulo->getSubTitulo(), PDO::PARAM_STR);
        return $stmt;
    }
    protected function datos_videosubcapitulo_modelo($conexion, $tipo, $VideoSubTitulo)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo` WHERE idvideo_subtitulo=:IDlibro");
                    $stmt->bindValue(":IDlibro", $VideoSubTitulo->getIdVideoSubTitulo(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `video_subtitulo` WHERE idvideo_subtitulo=:IDlibro");
                            $stmt->bindValue(":IDlibro", $VideoSubTitulo->getIdVideoSubTitulo(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {
                                $insVideoSubTitulo = new VideoSubTitulo();
                                $insVideoSubTitulo->setIdVideoSubTitulo($row['idvideo_subtitulo']);
                                $insVideoSubTitulo->setCodigo($row['codigovideo_subtitulo']);
                                $insVideoSubTitulo->setNombre($row['nombreVideo']);
                                $insVideoSubTitulo->setImagen($row['imagen']);

                                $insVideoSubTitulo->setSubTitulo($row['subtitulo_codigosubtitulo']);
                                $insBeanPagination->setList($insVideoSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo` ");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `video_subtitulo`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {

                                $insVideoSubTitulo = new VideoSubTitulo();
                                $insVideoSubTitulo->setIdVideoSubTitulo($row['idvideo_subtitulo']);
                                $insVideoSubTitulo->setCodigo($row['codigovideo_subtitulo']);
                                $insVideoSubTitulo->setNombre($row['nombreVideo']);
                                $insVideoSubTitulo->setImagen($row['imagen']);

                                $insVideoSubTitulo->setSubTitulo($row['subtitulo_codigosubtitulo']);
                                $insBeanPagination->setList($insVideoSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "codigo":

                    $stmt = $conexion->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo` WHERE codigovideo_subtitulo=:IDlibro");
                    $stmt->bindValue(":IDlibro", $VideoSubTitulo->getCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `video_subtitulo` WHERE codigovideo_subtitulo=:IDlibro");
                            $stmt->bindValue(":IDlibro", $VideoSubTitulo->getCodigo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {

                                $insVideoSubTitulo = new VideoSubTitulo();
                                $insVideoSubTitulo->setIdVideoSubTitulo($row['idvideo_subtitulo']);
                                $insVideoSubTitulo->setCodigo($row['codigovideo_subtitulo']);
                                $insVideoSubTitulo->setNombre($row['nombreVideo']);
                                $insVideoSubTitulo->setImagen($row['imagen']);

                                $insVideoSubTitulo->setSubTitulo($row['subtitulo_codigosubtitulo']);
                                $insBeanPagination->setList($insVideoSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "subtitulo":

                    $stmt = $conexion->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo`  WHERE subtitulo_codigosubtitulo = :Codigo_subtitulo");
                    $stmt->bindValue(":Codigo_subtitulo", $VideoSubTitulo->getSubTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `video_subtitulo`
                            WHERE subtitulo_codigosubtitulo = :Codigo_subtitulo ORDER BY codigovideo_subtitulo");
                            $stmt->bindValue(":Codigo_subtitulo", $VideoSubTitulo->getSubTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {

                                $insVideoSubTitulo = new VideoSubTitulo();
                                $insVideoSubTitulo->setIdVideoSubTitulo($row['idvideo_subtitulo']);
                                $insVideoSubTitulo->setCodigo($row['codigovideo_subtitulo']);
                                $insVideoSubTitulo->setNombre($row['nombreVideo']);
                                $insVideoSubTitulo->setImagen($row['imagen']);

                                $insVideoSubTitulo->setSubTitulo($row['subtitulo_codigosubtitulo']);
                                $insBeanPagination->setList($insVideoSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "leccion-maximo":
                    $stmt = $conexion->prepare("SELECT count(idlecciones) AS CONTADOR FROM `lecciones`  WHERE cuenta_codigocuenta = :Cuenta");
                    $stmt->bindValue(":Cuenta", $VideoSubTitulo->getSubTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT MAX(subtitulo_codigosubtitulo) AS CONTADOR FROM `lecciones`  WHERE cuenta_codigocuenta = :Cuenta");
                            $stmt->bindValue(":Cuenta", $VideoSubTitulo->getSubTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {
                                $insVideoSubTitulo = new VideoSubTitulo();
                                $insVideoSubTitulo->setCodigo($row['subtitulo_codigosubtitulo']);
                                $insBeanPagination->setList($insVideoSubTitulo->__toString());
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
    protected function eliminar_videosubcapitulo_modelo($conexion, $codigo)
    {
        $stmt = $conexion->prepare("DELETE FROM `video_subtitulo` WHERE idvideo_subtitulo=:IDtitulo ");
        $stmt->bindValue(":IDtitulo", $codigo, PDO::PARAM_INT);
        return $stmt;
    }
    protected function actualizar_videosubcapitulo_modelo($conexion, $VideoSubTitulo)
    {

        $stmt = $conexion->prepare("UPDATE `video_subtitulo`
            SET subtitulo_codigosubtitulo=:CodigoSubtitulo,nombreVideo=:Video,
            codigovideo_subtitulo=:CodigoParrafo
            WHERE idvideo_subtitulo=:ID");
        $stmt->bindValue(":Video", $VideoSubTitulo->getNombre(), PDO::PARAM_STR);
        $stmt->bindValue(":CodigoParrafo", $VideoSubTitulo->getCodigo(), PDO::PARAM_STR);
        $stmt->bindValue(":CodigoSubtitulo", $VideoSubTitulo->getSubTitulo(), PDO::PARAM_STR);
        $stmt->bindValue(":ID", $VideoSubTitulo->getIdVideoSubTitulo(), PDO::PARAM_INT);
        return $stmt;
    }

}
