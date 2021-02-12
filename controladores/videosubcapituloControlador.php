<?php

require_once './modelos/videosubcapituloModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class videosubcapituloControlador extends videosubcapituloModelo
{
    public function agregar_videosubcapitulo_controlador($VideoSubTitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $VideoSubTitulo->setCodigo(mainModel::limpiar_cadena($VideoSubTitulo->getCodigo()));
            $VideoSubTitulo->setSubTitulo(mainModel::limpiar_cadena($VideoSubTitulo->getSubTitulo()));

            $lista = videosubcapituloModelo::datos_videosubcapitulo_modelo($this->conexion_db, "codigo", $VideoSubTitulo);
            if ($lista["countFilter"] > 0) {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, existe un parrafo con el mismo codigo.");
            } else {
                $stmt = videosubcapituloModelo::agregar_videosubcapitulo_modelo($this->conexion_db, $VideoSubTitulo);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_videosubcapitulo_controlador($this->conexion_db, 0, 5, $VideoSubTitulo->getSubTitulo()));

                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar el parrafo");
                }

            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function datos_videosubcapitulo_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(videosubcapituloModelo::datos_videosubcapitulo_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_videosubcapitulo_controlador($conexion, $inicio, $registros, $codigo)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo` WHERE subtitulo_codigosubtitulo=?");
            $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();

            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `video_subtitulo` as v
                    inner join `subtitulo` as s ON v.subtitulo_codigosubtitulo=s.codigo_subtitulo WHERE v.subtitulo_codigosubtitulo=? ORDER BY s.codigo_subtitulo ASC LIMIT ?,?");
                    $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
                    $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {

                        $insSubTitulo = new SubTitulo();
                        $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                        $insSubTitulo->setPdf($row['subtituloPDF']);
                        $insSubTitulo->setDescripcion($row['descripcion']);
                        $insSubTitulo->setNombre($row['nombre']);

                        $insVideoSubTitulo = new VideoSubTitulo();
                        $insVideoSubTitulo->setIdVideoSubTitulo($row['idvideo_subtitulo']);
                        $insVideoSubTitulo->setCodigo($row['codigovideo_subtitulo']);
                        $insVideoSubTitulo->setNombre($row['nombreVideo']);
                        $insVideoSubTitulo->setImagen($row['imagen']);

                        $insVideoSubTitulo->setSubTitulo($insSubTitulo->__toString());
                        $insBeanPagination->setList($insVideoSubTitulo->__toString());
                    }
                }
            }

            $stmt->closeCursor(); // this is not even required
            $stmt = null; // doing this is mandatory for connection to get closed

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";
        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";
        }
        return $insBeanPagination->__toString();

    }
    public function bean_paginador_videosubcapitulo_controlador($pagina, $registros, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $codigo = mainModel::limpiar_cadena($codigo);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_videosubcapitulo_controlador($this->conexion_db, $inicio, $registros, $codigo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_videosubcapitulo_controlador($VideoSubTitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $VideoSubTitulo->setIdVideoSubTitulo(mainModel::limpiar_cadena($VideoSubTitulo->getIdVideoSubTitulo()));
            $lista = videosubcapituloModelo::datos_videosubcapitulo_modelo($this->conexion_db, "unico", $VideoSubTitulo);

            if ($lista["countFilter"] > 0) {

                $stmt = videosubcapituloModelo::eliminar_videosubcapitulo_modelo($this->conexion_db, $VideoSubTitulo->getIdVideoSubTitulo());
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_videosubcapitulo_controlador($this->conexion_db, 0, 5, $lista["list"][0]['subTitulo']));
                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido eliminar el parrafo");
                }

            } else {
                $insBeanCrud->setMessageServer("error en el servidor, No se encontró parrafo");
            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_videosubcapitulo_controlador($VideoSubTitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $VideoSubTitulo->setCodigo(mainModel::limpiar_cadena($VideoSubTitulo->getCodigo()));
            $VideoSubTitulo->setSubTitulo(mainModel::limpiar_cadena($VideoSubTitulo->getSubTitulo()));
            $VideoSubTitulo->setIdVideoSubTitulo(mainModel::limpiar_cadena($VideoSubTitulo->getIdVideoSubTitulo()));

            $lista = videosubcapituloModelo::datos_videosubcapitulo_modelo($this->conexion_db, "unico", $VideoSubTitulo);
            if ($lista["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, No se encuentra el parrafo");
            } else {
                $stmt = videosubcapituloModelo::actualizar_videosubcapitulo_modelo($this->conexion_db, $VideoSubTitulo);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_videosubcapitulo_controlador($this->conexion_db, 0, 5, $VideoSubTitulo->getSubTitulo()));
                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el parrafo");
                }

            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    protected function archivo($permitidos, $limite_KB, $original, $nombre, $destino)
    {

        if (in_array($original['type'], $permitidos) && ($original['size'] <= $limite_KB * 1024)) {
            $array_ruta = explode('/', $destino);
            if (!file_exists('./adjuntos/archivos/' . $array_ruta[3])) {
                mkdir('./adjuntos/archivos/' . $array_ruta[3], 0777, true);
                mkdir('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4], 0777, true);
                mkdir('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4] . '/' . $array_ruta[5], 0777, true);
                mkdir('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4] . '/' . $array_ruta[5] . '/' . $array_ruta[6], 0777, true);
            } elseif (!file_exists('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4])) {
                mkdir('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4], 0777, true);
                mkdir('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4] . '/' . $array_ruta[5], 0777, true);
                mkdir('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4] . '/' . $array_ruta[5] . '/' . $array_ruta[6], 0777, true);
            } else if (!file_exists('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4] . '/' . $array_ruta[5])) {
                mkdir('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4] . '/' . $array_ruta[5], 0777, true);
                mkdir('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4] . '/' . $array_ruta[5] . '/' . $array_ruta[6], 0777, true);
            } else if (!file_exists('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4] . '/' . $array_ruta[5] . '/' . $array_ruta[6])) {
                mkdir('./adjuntos/archivos/' . $array_ruta[3] . '/' . $array_ruta[4] . '/' . $array_ruta[5] . '/' . $array_ruta[6], 0777, true);
            }
            $array_nombre = explode('.', $nombre);
            $extension = array_pop($array_nombre);
            $array = glob($destino . '/' . $array_nombre[0] . "*." . $extension);
            $cantidad = count($array);
            $nombreImagen = $array_nombre[0] . $cantidad . "." . $extension;
            if (file_exists($destino . '/' . $nombreImagen)) {
                $nombreImagen = $array_nombre[0] . "nuevo" . $cantidad . "." . $extension;
            }

            $resultado_guardado = move_uploaded_file($original['tmp_name'], $destino . '/' . $nombreImagen);

            if ($resultado_guardado) {
                return $nombreImagen;
            } else {
                return "";
            }

        } else {
            return "";
        }

    }
}
