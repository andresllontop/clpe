<?php

require_once './modelos/leccionesModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
require_once './classes/principal/videosubtitulo.php';
require_once './classes/principal/libro.php';
require_once './classes/principal/subtitulo.php';
require_once './classes/principal/titulo.php';
require_once './classes/principal/test.php';
require_once './classes/principal/detalletest.php';
require_once './classes/principal/respuesta.php';
require_once './classes/principal/empresa.php';
class leccionesControlador extends leccionesModelo
{
    public function agregar_lecciones_controlador($Leccion)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $Leccion->setCuenta(mainModel::limpiar_cadena($Leccion->getCuenta()));
            $Leccion->setSubTitulo(mainModel::limpiar_cadena($Leccion->getSubTitulo()));
            $Leccion->setComentario(mainModel::limpiar_cadena($Leccion->getComentario()));

            $stmt = $this->conexion_db->prepare("SELECT COUNT(idlecciones) AS CONTADOR FROM `lecciones` WHERE subtitulo_codigosubtitulo=:IDlibro and cuenta_codigocuenta=:Cuenta");
            $stmt->bindValue(":IDlibro", $Leccion->getSubTitulo(), PDO::PARAM_STR);
            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {

                if ($row['CONTADOR'] == 0) {
                    if (isset($_FILES['txtVideoLeccion'])) {
                        $original = $_FILES['txtVideoLeccion'];
                        //  $nombre = $original['name'];
                        $array = explode(".", $Leccion->getSubTitulo());
                        $arrayfecha = explode(" ", date("Y-m-d H:i:s"));
                        $arrayhour = explode(":", $arrayfecha[1]);
                        $TypeFormato = ".mp4";
                        if ($original['name'] != "blob") {
                            $tmp = explode('.', $original['name']);
                            $TypeFormato = "." . end($tmp);
                        }

                        $nombre = $Leccion->getCuenta() . $array[0] . $array[1] . $array[2] . $array[3] . $arrayfecha[0] . '-' . $arrayhour[0] . '-' . $arrayhour[1] . '-' . $arrayhour[2] . $TypeFormato;
                        if ($original['error'] > 0) {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otro video");
                        } else {
                            //500 MB
                            $resultado_guardado = mainModel::archivo(array("video/mp4", "video/ogg", "video/webm", "video/quicktime", "video/mov"), 500 * 1024, $original, $nombre, "./adjuntos/video-usuarios/");
                            if ($resultado_guardado != "") {
                                $Leccion->setVideo($resultado_guardado);
                                $stmt = leccionesModelo::agregar_lecciones_modelo($this->conexion_db, $Leccion);
                                $dateTime = date("Y/m/d H:i:s");
                                if ($stmt->execute()) {
                                    $stmt = $this->conexion_db->prepare("INSERT INTO `tarea`(cuenta,tipo,fecha,codigo_subtitulo,estado) VALUES(:Cuenta,:Tipo,:Fecha,:Subtitulo,:Estado)");
                                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                    $stmt->bindValue(":Subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                                    $stmt->bindValue(":Estado", 0);
                                    $stmt->bindValue(":Fecha", $dateTime);
                                    $stmt->bindValue(":Tipo", 0, PDO::PARAM_INT);

                                    if ($stmt->execute()) {
                                        $stmt = $this->conexion_db->prepare("SELECT COUNT(id) AS CONTADOR FROM `bitacora` WHERE cuenta_codigoCuenta=:Cuenta");
                                        $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                        $stmt->execute();
                                        $datos = $stmt->fetchAll();
                                        foreach ($datos as $row) {
                                            if ($row['CONTADOR'] == 0) {
                                                $stmt = $this->conexion_db->prepare("INSERT INTO `bitacora`(cuenta_codigoCuenta,tipo,fecha_inicio,estado) VALUES(:Cuenta,:Tipo,:Fecha,:Estado)");
                                                $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                                $stmt->bindValue(":Estado", 0, PDO::PARAM_INT);
                                                $stmt->bindValue(":Fecha", $dateTime);
                                                $stmt->bindValue(":Tipo", 2, PDO::PARAM_INT);
                                            } else {
                                                $stmt = $this->conexion_db->prepare("UPDATE `bitacora` SET  fecha_inicio=:Fecha WHERE cuenta_codigoCuenta=:Cuenta");
                                                $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                                $stmt->bindValue(":Fecha", $dateTime);
                                            }
                                        }

                                        if ($stmt->execute()) {
                                            $this->conexion_db->commit();
                                            $insBeanCrud->setMessageServer(self::obtener_lecciones_alumno_mensaje_controlador($Leccion)['messageServer']);

                                        } else {
                                            $insBeanCrud->setMessageServer("error en el servidor, No se puede registrar la bitacora");

                                        }

                                    } else {
                                        $insBeanCrud->setMessageServer("error en el servidor, No se puede registrar la tarea");

                                    }
                                } else {
                                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar la lección");

                                }
                            } else {

                                $insBeanCrud->setMessageServer("Hubo un error al guardar el video,formato no permitido o tamaño excedido");

                            }

                        }
                    } else {
                        $insBeanCrud->setMessageServer("por favor Ingrese video");
                    }
                } else {
                    //actualizar
                    $stmt = $this->conexion_db->prepare("SELECT * FROM `lecciones` WHERE subtitulo_codigosubtitulo=:IDlibro and cuenta_codigocuenta=:Cuenta");
                    $stmt->bindValue(":IDlibro", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row5) {

                        if (isset($_FILES['txtVideoLeccion'])) {
                            $original = $_FILES['txtVideoLeccion'];
                            //  $nombre = $original['name'];
                            $array = explode(".", $Leccion->getSubTitulo());
                            $arrayfecha = explode(" ", date("Y-m-d H:i:s"));
                            $arrayhour = explode(":", $arrayfecha[1]);
                            $TypeFormato = ".mp4";

                            if ($original['name'] != "blob") {
                                $tmp = explode('.', $original['name']);
                                $TypeFormato = "." . end($tmp);
                            }
                            $nombre = $Leccion->getCuenta() . $array[0] . $array[1] . $array[2] . $array[3] . $arrayfecha[0] . '-' . $arrayhour[0] . '-' . $arrayhour[1] . '-' . $arrayhour[2] . $TypeFormato;

                            if ($original['error'] > 0) {
                                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otro video");
                            } else {
                                //500 MB
                                $resultado_guardado = mainModel::archivo(array("video/mp4", "video/ogg", "video/webm", "video/quicktime"), 500 * 1024, $original, $nombre, "./adjuntos/video-usuarios/");
                                if ($resultado_guardado != "") {
                                    $Leccion->setVideo($resultado_guardado);

                                    $stmt = leccionesModelo::actualizar_lecciones_alumno_modelo($this->conexion_db, $Leccion);

                                    $dateTime = date("Y/m/d H:i:s");
                                    if ($stmt->execute()) {

                                        $stmt = $this->conexion_db->prepare("UPDATE `tarea` SET fecha=:Fecha WHERE cuenta=:Cuenta and tipo=:Tipo and codigo_subtitulo=:Subtitulo");
                                        $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                        $stmt->bindValue(":Subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                                        $stmt->bindValue(":Fecha", $dateTime);
                                        $stmt->bindValue(":Tipo", 0, PDO::PARAM_INT);

                                        if ($stmt->execute()) {
                                            $stmt = $this->conexion_db->prepare("SELECT COUNT(id) AS CONTADOR FROM `bitacora` WHERE cuenta_codigoCuenta=:Cuenta");
                                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                            $stmt->execute();
                                            $datos = $stmt->fetchAll();
                                            foreach ($datos as $row2) {
                                                if ($row2['CONTADOR'] > 0) {
                                                    $stmt = $this->conexion_db->prepare("UPDATE `bitacora` SET  fecha_inicio=:Fecha WHERE cuenta_codigoCuenta=:Cuenta");
                                                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                                    $stmt->bindValue(":Fecha", $dateTime);
                                                }
                                            }

                                            if ($stmt->execute()) {
                                                $this->conexion_db->commit();
                                                $insBeanCrud->setMessageServer("ok");

                                                if ($row5['video'] != "") {

                                                    unlink('./adjuntos/video-usuarios/' . $row5['video']);

                                                }
                                                $insBeanCrud->setMessageServer(self::obtener_lecciones_alumno_mensaje_controlador($Leccion)['messageServer']);
                                            } else {
                                                $insBeanCrud->setMessageServer("error en el servidor, No se puede registrar la bitacora");

                                            }

                                        } else {
                                            $insBeanCrud->setMessageServer("error en el servidor, No se puede registrar la tarea");

                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar la lección");

                                    }
                                } else {

                                    $insBeanCrud->setMessageServer("Hubo un error al guardar el video,formato no permitido o tamaño excedido");

                                }

                            }
                        } else {
                            // $insBeanCrud->setMessageServer("por favor Ingrese video");
                            $Leccion->setVideo($row5['video']);
                            $stmt = leccionesModelo::actualizar_lecciones_alumno_modelo($this->conexion_db, $Leccion);
                            $dateTime = date("Y/m/d H:i:s");
                            if ($stmt->execute()) {
                                $stmt = $this->conexion_db->prepare("UPDATE `tarea` SET fecha=:Fecha WHERE cuenta=:Cuenta and tipo=:Tipo and codigo_subtitulo=:Subtitulo");
                                $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                $stmt->bindValue(":Subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                                $stmt->bindValue(":Fecha", $dateTime);
                                $stmt->bindValue(":Tipo", 0, PDO::PARAM_INT);

                                if ($stmt->execute()) {
                                    $stmt = $this->conexion_db->prepare("SELECT COUNT(id) AS CONTADOR FROM `bitacora` WHERE cuenta_codigoCuenta=:Cuenta");
                                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row4) {
                                        if ($row4['CONTADOR'] > 0) {
                                            $stmt = $this->conexion_db->prepare("UPDATE `bitacora` SET  fecha_inicio=:Fecha WHERE cuenta_codigoCuenta=:Cuenta");
                                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                            $stmt->bindValue(":Fecha", $dateTime);
                                        }
                                    }

                                    if ($stmt->execute()) {
                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        $insBeanCrud->setMessageServer(self::obtener_lecciones_alumno_mensaje_controlador($Leccion)['messageServer']);
                                    } else {
                                        $insBeanCrud->setMessageServer("error en el servidor, No se puede registrar la bitacora");

                                    }

                                } else {
                                    $insBeanCrud->setMessageServer("error en el servidor, No se puede registrar la tarea");

                                }
                            } else {
                                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar la lección");

                            }
                        }
                    }

                }
            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            return "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            return "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function actualizar_lecciones_controlador($Leccion)
    {
        $insBeanCrud = new BeanCrud();

        try {
            $this->conexion_db->beginTransaction();
            $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "unico", $Leccion);
            if ($lecciones["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra la lección seleccionada");
            } else {
                $stmt = leccionesModelo::actualizar_lecciones_modelo($this->conexion_db, $Leccion);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    // $Leccion->setCuenta($lecciones['list'][0]['cuenta']);
                    //$Leccion->setPagina(1);
                    //$Leccion->setRegistro(20);
                    //$insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, "conteo", $Leccion));
                } else {
                    $insBeanCrud->setMessageServer("No se actualizó el estado de la lección");
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

    public function actualizar_lecciones_estado_controlador($Leccion)
    {
        $insBeanCrud = new BeanCrud();
        $insBean = null;
        try {
            $this->conexion_db->beginTransaction();

            $stmt = $this->conexion_db->prepare("UPDATE `lecciones` SET tipo_estado=:Estado WHERE cuenta_codigocuenta=:Cuenta and subtitulo_codigosubtitulo=:Subtitulo");
            $stmt->bindValue(":Estado", $Leccion->getEstado(), PDO::PARAM_INT);
            $stmt->bindValue(":Subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
            if ($stmt->execute()) {
                $this->conexion_db->commit();
                $insBeanCrud->setMessageServer("ok");
                $insBean = self::obtener_lecciones_alumno_controlador($Leccion);
                $insBean = json_encode($insBean);
            } else {
                $insBeanCrud->setMessageServer("No se actualizó el estado de la lección");
                $insBean = json_encode($insBeanCrud->__toString());
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
        return $insBean;
    }
    public function datos_lecciones_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_lecciones_controlador($conexion, $inicio, $registros, $codigo, $subtitulo)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->prepare("SELECT COUNT(idlecciones) AS CONTADOR  FROM `lecciones` WHERE cuenta_codigocuenta=? and subtitulo_codigosubtitulo=?");
            $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
            $stmt->bindValue(2, $subtitulo, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT sub.*, lec.idlecciones,lec.video,lec.comentario FROM `lecciones` as lec inner join `subtitulo` as sub ON sub.codigo_subtitulo=lec.subtitulo_codigosubtitulo WHERE lec.cuenta_codigocuenta=? and lec.subtitulo_codigosubtitulo=? ORDER BY lec.subtitulo_codigosubtitulo ASC LIMIT ?,?");
                    $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
                    $stmt->bindValue(2, $subtitulo, PDO::PARAM_STR);
                    $stmt->bindValue(3, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(4, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $insSubTitulo = new SubTitulo();
                        $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                        $insSubTitulo->setPdf($row['subtituloPDF']);
                        $insSubTitulo->setNombre($row['nombre']);
                        $insSubTitulo->setDescripcion($row['descripcion']);
                        $insSubTitulo->setImagen($row['subtitulo_imagen']);

                        $insLeccion = new Leccion();
                        $insLeccion->setIdleccion($row['idlecciones']);
                        $insLeccion->setVideo($row['video']);
                        $insLeccion->setComentario($row['comentario']);

                        $insLeccion->setSubTitulo($insSubTitulo->__toString());
                        $insBeanPagination->setList($insLeccion->__toString());
                    }
                }
            }

            $stmt = $conexion->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo` WHERE subtitulo_codigosubtitulo=?");
            $stmt->bindValue(1, $subtitulo, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();

            foreach ($datos as $row) {
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `video_subtitulo` as v
                    inner join `subtitulo` as s ON v.subtitulo_codigosubtitulo=s.codigo_subtitulo WHERE v.subtitulo_codigosubtitulo=? ORDER BY s.codigo_subtitulo ASC LIMIT ?,?");
                    $stmt->bindValue(1, $subtitulo, PDO::PARAM_STR);
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
    public function bean_paginador_lecciones_controlador($pagina, $registros, $codigo, $subtitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $codigo = mainModel::limpiar_cadena($codigo);
            $subtitulo = mainModel::limpiar_cadena($subtitulo);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_lecciones_controlador($this->conexion_db, $inicio, $registros, $codigo, $subtitulo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_lecciones_controlador($Leccion)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "unico", $Leccion);
            if ($lecciones["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra la lección seleccionada");
            } else {
                $stmt = leccionesModelo::eliminar_lecciones_modelo($this->conexion_db, mainModel::limpiar_cadena($Leccion->getIdleccion()));
                if ($stmt->execute()) {
                    $stmt = $this->conexion_db->prepare("DELETE FROM `tarea` WHERE codigo_subtitulo=:IDtitulo and cuenta=:CodigoCuenta and tipo=0");
                    $stmt->bindValue(":IDtitulo", $lecciones["list"][0]['subTitulo']);
                    $stmt->bindValue(":CodigoCuenta", $lecciones["list"][0]['cuenta']);

                    if ($stmt->execute()) {
                        if ($lecciones["list"][0]['video'] != "" || $lecciones["list"][0]['video'] != null) {
                            unlink('./adjuntos/video-usuarios/' . $lecciones["list"][0]['video']);
                        }

                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        //  $Leccion->setCuenta($lecciones['list'][0]['cuenta']);
                        //$Leccion->setPagina(1);
                        //$Leccion->setRegistro(20);
                        //$insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, "conteo", $Leccion));
                    } else {
                        $insBeanCrud->setMessageServer("No se eliminó la tarea");
                    }

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó la lección");
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
    public function eliminar_video_lecciones_controlador($Leccion)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "unico", $Leccion);
            if ($lecciones["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra la lección seleccionada");
            } else {
                $stmt = leccionesModelo::eliminar_video_lecciones_modelo($this->conexion_db, mainModel::limpiar_cadena($Leccion->getIdleccion()));
                if ($stmt->execute()) {
                    if ($lecciones["list"][0]['video'] != "" || $lecciones["list"][0]['video'] != null) {
                        unlink('./adjuntos/video-usuarios/' . $lecciones["list"][0]['video']);
                    }

                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    //  $Leccion->setCuenta($lecciones['list'][0]['cuenta']);
                    // $Leccion->setPagina(1);
                    //$Leccion->setRegistro(20);
                    //$insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, "conteo", $Leccion));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el video de la lección");
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
    public function obtener_lecciones_alumno_controlador($leccion)
    {

        $insBeanCrud = new BeanCrud();
        $insBeanPagination = new BeanPagination();
        try {
            $leccionMaximo = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "leccion-maximo", $leccion);
            if ($leccionMaximo['countFilter'] == 0) {
                $insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video-minimo", $leccion));
            } else {
                $array = explode(".", $leccionMaximo['list'][0]['subTitulo']);
                // aumentar subtitulo
                $numerofinal = ($array[3] + 1);
                if (strlen($numerofinal) == 1) {
                    $numerofinal = "0" . $numerofinal;
                }
                $leccion->setSubTitulo($array[0] . "." . $array[1] . "." . $array[2] . "." . $numerofinal);

                //BUSCAR EL MAXIMO SUBTIULO PARA SABER SI CULMINO EL LIBRO
                $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS MAXIMO FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
                $stmt->bindValue(":IDlibro", $array[0], PDO::PARAM_STR);
                $stmt->execute();
                $variable = $stmt->fetchAll();
                foreach ($variable as $row) {

                    if ($row['MAXIMO'] == null || $row['MAXIMO'] == $leccionMaximo['list'][0]['subTitulo']) {
                        //culmino curso
                        //BUSCAR EL MAXIMO SUBTIULO PARA SABER EL EXAMEN DEL CAPITULO
                        $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS CONTADOR FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
                        $stmt->bindValue(":IDlibro", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                        $stmt->execute();
                        $datos1 = $stmt->fetchAll();

                        foreach ($datos1 as $row1) {
                            if ($row1['CONTADOR'] == null || $row1['CONTADOR'] != $leccionMaximo['list'][0]['subTitulo']) {

                                // echo ("NO SON IGUALES");
                                //BUSCAR SI HAY EXAMENES POR REALIZAR
                                $stmt = $this->conexion_db->prepare("SELECT COUNT(iddetalle_test) AS CONTADOR FROM `detalle_test` WHERE codigo_subtitulo=:IDlibro and idtest=(SELECT idtest FROM `test` WHERE codigotitulo=:IDTitulo and tipo=2)");
                                $stmt->bindValue(":IDlibro", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);
                                $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                $stmt->execute();
                                $datos2 = $stmt->fetchAll();
                                foreach ($datos2 as $row2) {
                                    $insBeanPagination->setCountFilter($row2['CONTADOR']);
                                    if ($row2['CONTADOR'] > 0) {
                                        //SI HAY EXAMEN INTERNO POR REALIZAR
                                        //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL SUBTITULO INTERNO
                                        $stmt = $this->conexion_db->prepare("SELECT count(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:SubTitulo and tipo=2");
                                        $stmt->bindValue(":Cuenta", $leccion->getCuenta(), PDO::PARAM_STR);
                                        $stmt->bindValue(":SubTitulo", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);

                                        $stmt->execute();
                                        $datos3 = $stmt->fetchAll();
                                        foreach ($datos3 as $row3) {

                                            if ($row3['CONTADOR'] == 0) {
                                                //NO REALIZO EL EXAMEN INTERNO
                                                //MOSTRAR  EXAMENES POR REALIZAR
                                                $stmt = $this->conexion_db->prepare("SELECT TES.nombre as test_nombre,DET.*, TES.tipo AS tes_tipo, TES.descripcion AS descrip, SUB.nombre AS sub_nombre, TIT.tituloNombre AS tit_nombre FROM `detalle_test`AS DET INNER JOIN `test` AS TES ON TES.idtest=DET.idtest INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=DET.codigo_subtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo WHERE DET.codigo_subtitulo=:IDlibro and TES.codigotitulo=:IDTitulo and TES.tipo=2");
                                                $stmt->bindValue(":IDlibro", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);
                                                $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                                $stmt->execute();
                                                $datos2 = $stmt->fetchAll();
                                                foreach ($datos2 as $row2) {
                                                    $insTitulo = new Titulo();
                                                    $insTitulo->setNombre($row2['tit_nombre']);

                                                    $insSubTitulo = new SubTitulo();
                                                    $insSubTitulo->setDescripcion($row2['sub_nombre']);
                                                    $insSubTitulo->setCodigo($row2['codigo_subtitulo']);
                                                    $insSubTitulo->setTitulo($insTitulo->__toString());

                                                    $insTest = new Test();
                                                    $insTest->setIdTest($row2['idtest']);
                                                    $insTest->setDescripcion($row2['descrip']);
                                                    $insTest->setNombre($row2['test_nombre']);
                                                    $insTest->setTipo($row2['tes_tipo']);
                                                    $insDetalleTest = new DetalleTest();
                                                    $insDetalleTest->setIdDetalleTest($row2['iddetalle_test']);
                                                    $insDetalleTest->setSubtitulo($insSubTitulo->__toString());
                                                    $insDetalleTest->setDescripcion($row2['descripcion']);
                                                    $insDetalleTest->setTest($insTest->__toString());
                                                    $insBeanPagination->setList($insDetalleTest->__toString());
                                                    $insBeanCrud->setBeanPagination($insBeanPagination->__toString());

                                                }
                                                $insBeanCrud->setMessageServer("interno");
                                            } else {
                                                //YA REALIZO EL EXAMEN INTERNO
                                                $insBeanCrud->setMessageServer("fin");

                                            }
                                        }

                                    } else {

                                        //NO HAY EXAMEN INTERNO POR REALIZAR
                                        $insBeanCrud->setMessageServer("fin");

                                    }
                                }

                            } else {
                                //echo ("cumlmino el capitulo"/OBTENER EL EXAMEN DEL CAPITULO);

                                //BUSCAR SI HAY EXAMENES POR REALIZAR DEL CAPITULO CULMINADO
                                $stmt = $this->conexion_db->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE codigotitulo=:IDTitulo and tipo=1");
                                $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                $stmt->execute();
                                $datos2 = $stmt->fetchAll();
                                foreach ($datos2 as $row2) {
                                    if ($row2['CONTADOR'] > 0) {
                                        //SI HAY EXAMEN PARA EL CAPITULO
                                        //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL CAPITULO
                                        $stmt = $this->conexion_db->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:IDTitulo and tipo=1");
                                        $stmt->bindValue(":Cuenta", $leccion->getCuenta(), PDO::PARAM_STR);
                                        $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                        $stmt->execute();
                                        $datos3 = $stmt->fetchAll();
                                        foreach ($datos3 as $row3) {
                                            if ($row3['CONTADOR'] > 0) {
                                                //YA REGISTRARTE EL EXAMEN DEL CAPITULO
                                                $insBeanCrud->setMessageServer("fin");

                                            } else {
                                                //MOSTRAR  EXAMENES POR REALIZAR DEL CAPITULO
                                                $stmt = $this->conexion_db->prepare("SELECT DET.*, TES.tipo AS tes_tipo, TES.descripcion AS descrip, SUB.nombre AS sub_nombre, TIT.tituloNombre AS tit_nombre FROM `detalle_test`AS DET INNER JOIN `test` AS TES ON TES.idtest=DET.idtest INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=DET.codigo_subtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo WHERE TES.codigotitulo=:IDTitulo and TES.tipo=1");
                                                $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                                $stmt->execute();
                                                $datos2 = $stmt->fetchAll();
                                                foreach ($datos2 as $row2) {
                                                    $insTitulo = new Titulo();
                                                    $insTitulo->setNombre($row2['tit_nombre']);

                                                    $insSubTitulo = new SubTitulo();
                                                    $insSubTitulo->setDescripcion($row2['sub_nombre']);
                                                    $insSubTitulo->setCodigo($row2['codigo_subtitulo']);
                                                    $insSubTitulo->setTitulo($insTitulo->__toString());

                                                    $insTest = new Test();
                                                    $insTest->setIdTest($row2['idtest']);
                                                    $insTest->setDescripcion($row2['descrip']);
                                                    $insTest->setTipo($row2['tes_tipo']);
                                                    $insDetalleTest = new DetalleTest();
                                                    $insDetalleTest->setIdDetalleTest($row2['iddetalle_test']);
                                                    $insDetalleTest->setSubtitulo($insSubTitulo->__toString());
                                                    $insDetalleTest->setDescripcion($row2['descripcion']);
                                                    $insDetalleTest->setTest($insTest->__toString());
                                                    $insBeanPagination->setList($insDetalleTest->__toString());
                                                    $insBeanCrud->setBeanPagination($insBeanPagination->__toString());

                                                }
                                                $insBeanCrud->setMessageServer("general");

                                            }
                                        }

                                    } else {
                                        //NO HAY EXAMEN PARA EL CAPITULO
                                        $insBeanCrud->setMessageServer("fin");
                                    }
                                }

                            }

                        }

                    } else {

                        //BUSCAR EL MAXIMO SUBTIULO PARA SABER EL EXAMEN DEL CAPITULO
                        $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS CONTADOR FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
                        $stmt->bindValue(":IDlibro", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                        $stmt->execute();
                        $datos1 = $stmt->fetchAll();

                        foreach ($datos1 as $row1) {
                            if ($row1['CONTADOR'] == null || $row1['CONTADOR'] != $leccionMaximo['list'][0]['subTitulo']) {

                                // echo ("NO SON IGUALES");
                                //BUSCAR SI HAY EXAMENES POR REALIZAR
                                $stmt = $this->conexion_db->prepare("SELECT COUNT(iddetalle_test) AS CONTADOR FROM `detalle_test` WHERE codigo_subtitulo=:IDlibro and idtest=(SELECT idtest FROM `test` WHERE codigotitulo=:IDTitulo and tipo=2)");
                                $stmt->bindValue(":IDlibro", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);
                                $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                $stmt->execute();
                                $datos2 = $stmt->fetchAll();
                                foreach ($datos2 as $row2) {
                                    $insBeanPagination->setCountFilter($row2['CONTADOR']);
                                    if ($row2['CONTADOR'] > 0) {
                                        //SI HAY EXAMEN INTERNO POR REALIZAR
                                        //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL SUBTITULO INTERNO
                                        $stmt = $this->conexion_db->prepare("SELECT count(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:SubTitulo and tipo=2");
                                        $stmt->bindValue(":Cuenta", $leccion->getCuenta(), PDO::PARAM_STR);
                                        $stmt->bindValue(":SubTitulo", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);

                                        $stmt->execute();
                                        $datos3 = $stmt->fetchAll();
                                        foreach ($datos3 as $row3) {

                                            if ($row3['CONTADOR'] == 0) {
                                                //NO REALIZO EL EXAMEN INTERNO
                                                //MOSTRAR  EXAMENES POR REALIZAR
                                                $stmt = $this->conexion_db->prepare("SELECT TES.nombre as test_nombre,DET.*, TES.tipo AS tes_tipo, TES.descripcion AS descrip, SUB.nombre AS sub_nombre, TIT.tituloNombre AS tit_nombre FROM `detalle_test`AS DET INNER JOIN `test` AS TES ON TES.idtest=DET.idtest INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=DET.codigo_subtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo WHERE DET.codigo_subtitulo=:IDlibro and TES.codigotitulo=:IDTitulo and TES.tipo=2");
                                                $stmt->bindValue(":IDlibro", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);
                                                $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                                $stmt->execute();
                                                $datos2 = $stmt->fetchAll();
                                                foreach ($datos2 as $row2) {
                                                    $insTitulo = new Titulo();
                                                    $insTitulo->setNombre($row2['tit_nombre']);

                                                    $insSubTitulo = new SubTitulo();
                                                    $insSubTitulo->setDescripcion($row2['sub_nombre']);
                                                    $insSubTitulo->setCodigo($row2['codigo_subtitulo']);
                                                    $insSubTitulo->setTitulo($insTitulo->__toString());

                                                    $insTest = new Test();
                                                    $insTest->setIdTest($row2['idtest']);
                                                    $insTest->setDescripcion($row2['descrip']);
                                                    $insTest->setNombre($row2['test_nombre']);
                                                    $insTest->setTipo($row2['tes_tipo']);
                                                    $insDetalleTest = new DetalleTest();
                                                    $insDetalleTest->setIdDetalleTest($row2['iddetalle_test']);
                                                    $insDetalleTest->setSubtitulo($insSubTitulo->__toString());
                                                    $insDetalleTest->setDescripcion($row2['descripcion']);
                                                    $insDetalleTest->setTest($insTest->__toString());
                                                    $insBeanPagination->setList($insDetalleTest->__toString());
                                                    $insBeanCrud->setBeanPagination($insBeanPagination->__toString());

                                                }
                                                $insBeanCrud->setMessageServer("interno");
                                            } else {
                                                //YA REALIZO EL EXAMEN INTERNO
                                                //  MOSTRAR LECCION DEL SUBTITULO SIGUIENTE
                                                $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video", $leccion);
                                                $insBeanCrud->setBeanPagination($lecciones);

                                            }
                                        }

                                    } else {

                                        //NO HAY EXAMEN INTERNO POR REALIZAR
                                        $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video", $leccion);
                                        //  MOSTRAR LECCION DEL SUBTITULO SIGUIENTE
                                        $insBeanCrud->setBeanPagination($lecciones);

                                    }
                                }

                            } else {
                                //echo ("cumlmino el capitulo"/OBTENER EL EXAMEN DEL CAPITULO);

                                //BUSCAR SI HAY EXAMENES POR REALIZAR DEL CAPITULO CULMINADO
                                $stmt = $this->conexion_db->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE codigotitulo=:IDTitulo and tipo=1");
                                $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                $stmt->execute();
                                $datos2 = $stmt->fetchAll();
                                foreach ($datos2 as $row2) {
                                    if ($row2['CONTADOR'] > 0) {
                                        //SI HAY EXAMEN PARA EL CAPITULO
                                        //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL CAPITULO
                                        $stmt = $this->conexion_db->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:IDTitulo and tipo=1");
                                        $stmt->bindValue(":Cuenta", $leccion->getCuenta(), PDO::PARAM_STR);
                                        $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                        $stmt->execute();
                                        $datos3 = $stmt->fetchAll();
                                        foreach ($datos3 as $row3) {
                                            if ($row3['CONTADOR'] > 0) {
                                                //YA REGISTRARTE EL EXAMEN DEL CAPITULO
                                                //ir al siguiente capitulo

                                                // aumentar capitulo
                                                $numerofinal = ($array[2] + 1);
                                                if (strlen($numerofinal) == 1) {
                                                    $numerofinal = "0" . $numerofinal;
                                                }
                                                $leccion->setSubTitulo($array[0] . "." . $array[1] . "." . $numerofinal . "." . "01");
                                                $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video", $leccion);
                                                $insBeanCrud->setBeanPagination($lecciones);

                                            } else {
                                                //MOSTRAR  EXAMENES POR REALIZAR DEL CAPITULO
                                                $stmt = $this->conexion_db->prepare("SELECT DET.*, TES.tipo AS tes_tipo, TES.descripcion AS descrip, SUB.nombre AS sub_nombre, TIT.tituloNombre AS tit_nombre FROM `detalle_test`AS DET INNER JOIN `test` AS TES ON TES.idtest=DET.idtest INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=DET.codigo_subtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo WHERE TES.codigotitulo=:IDTitulo and TES.tipo=1");
                                                $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                                $stmt->execute();
                                                $datos2 = $stmt->fetchAll();
                                                foreach ($datos2 as $row2) {
                                                    $insTitulo = new Titulo();
                                                    $insTitulo->setNombre($row2['tit_nombre']);

                                                    $insSubTitulo = new SubTitulo();
                                                    $insSubTitulo->setDescripcion($row2['sub_nombre']);
                                                    $insSubTitulo->setCodigo($row2['codigo_subtitulo']);
                                                    $insSubTitulo->setTitulo($insTitulo->__toString());

                                                    $insTest = new Test();
                                                    $insTest->setIdTest($row2['idtest']);
                                                    $insTest->setDescripcion($row2['descrip']);
                                                    $insTest->setTipo($row2['tes_tipo']);
                                                    $insDetalleTest = new DetalleTest();
                                                    $insDetalleTest->setIdDetalleTest($row2['iddetalle_test']);
                                                    $insDetalleTest->setSubtitulo($insSubTitulo->__toString());
                                                    $insDetalleTest->setDescripcion($row2['descripcion']);
                                                    $insDetalleTest->setTest($insTest->__toString());
                                                    $insBeanPagination->setList($insDetalleTest->__toString());
                                                    $insBeanCrud->setBeanPagination($insBeanPagination->__toString());

                                                }
                                                $insBeanCrud->setMessageServer("general");

                                            }
                                        }

                                    } else {
                                        //NO HAY EXAMEN PARA EL CAPITULO
                                        $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video", $leccion);
                                        //  MOSTRAR LECCION DEL SUBTITULO SIGUIENTE
                                        $insBeanCrud->setBeanPagination($lecciones);
                                    }
                                }

                            }

                        }
                    }

                }

            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function obtener_lecciones_alumno_mensaje_controlador($leccionMaximo)
    {

        $insBeanCrud = new BeanCrud();
        $insBeanPagination = new BeanPagination();
        try {
            $array = explode(".", $leccionMaximo->getSubtitulo());

            //BUSCAR EL MAXIMO SUBTIULO PARA SABER SI CULMINO EL LIBRO
            $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS MAXIMO FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
            $stmt->bindValue(":IDlibro", $array[0], PDO::PARAM_STR);
            $stmt->execute();
            $variable = $stmt->fetchAll();
            foreach ($variable as $row) {

                if ($row['MAXIMO'] == null || $row['MAXIMO'] == $leccionMaximo->getSubtitulo()) {
                    //FIN DEL CURSO
                    //BUSCAR EL MAXIMO SUBTIULO PARA SABER EL EXAMEN DEL CAPITULO
                    $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS CONTADOR FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
                    $stmt->bindValue(":IDlibro", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                    $stmt->execute();
                    $datos1 = $stmt->fetchAll();

                    foreach ($datos1 as $row1) {
                        if ($row1['CONTADOR'] == null || $row1['CONTADOR'] != $leccionMaximo->getSubtitulo()) {

                            // echo ("NO SON IGUALES");
                            //BUSCAR SI HAY EXAMENES POR REALIZAR
                            $stmt = $this->conexion_db->prepare("SELECT COUNT(iddetalle_test) AS CONTADOR FROM `detalle_test` WHERE codigo_subtitulo=:IDlibro and idtest=(SELECT idtest FROM `test` WHERE codigotitulo=:IDTitulo and tipo=2)");
                            $stmt->bindValue(":IDlibro", $leccionMaximo->getSubtitulo(), PDO::PARAM_STR);
                            $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                            $stmt->execute();
                            $datos2 = $stmt->fetchAll();
                            foreach ($datos2 as $row2) {
                                $insBeanPagination->setCountFilter($row2['CONTADOR']);
                                if ($row2['CONTADOR'] > 0) {
                                    //SI HAY EXAMEN INTERNO POR REALIZAR
                                    //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL SUBTITULO INTERNO
                                    $stmt = $this->conexion_db->prepare("SELECT count(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:SubTitulo and tipo=2");
                                    $stmt->bindValue(":Cuenta", $leccionMaximo->getCuenta(), PDO::PARAM_STR);
                                    $stmt->bindValue(":SubTitulo", $leccionMaximo->getSubtitulo(), PDO::PARAM_STR);

                                    $stmt->execute();
                                    $datos3 = $stmt->fetchAll();
                                    foreach ($datos3 as $row3) {

                                        if ($row3['CONTADOR'] == 0) {
                                            //NO REALIZO EL EXAMEN INTERNO
                                            //MOSTRAR  EXAMENES POR REALIZAR
                                            $insBeanCrud->setMessageServer("interno");
                                        } else {
                                            //YA REALIZO EL EXAMEN INTERNO
                                            $insBeanCrud->setMessageServer("fin");
                                        }
                                    }

                                } else {
                                    //NO HAY EXAMEN INTERNO POR REALIZAR
                                    $insBeanCrud->setMessageServer("fin");

                                }
                            }

                        } else {
                            //echo ("cumlmino el capitulo"/OBTENER EL EXAMEN DEL CAPITULO);

                            //BUSCAR SI HAY EXAMENES POR REALIZAR DEL CAPITULO CULMINADO
                            $stmt = $this->conexion_db->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE codigotitulo=:IDTitulo and tipo=1");
                            $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                            $stmt->execute();
                            $datos2 = $stmt->fetchAll();
                            foreach ($datos2 as $row2) {
                                if ($row2['CONTADOR'] > 0) {
                                    //SI HAY EXAMEN PARA EL CAPITULO
                                    //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL CAPITULO
                                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:IDTitulo and tipo=1");
                                    $stmt->bindValue(":Cuenta", $leccionMaximo->getCuenta(), PDO::PARAM_STR);
                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos3 = $stmt->fetchAll();
                                    foreach ($datos3 as $row3) {
                                        if ($row3['CONTADOR'] > 0) {
                                            //YA REGISTRARTE EL EXAMEN DEL CAPITULO
                                            $insBeanCrud->setMessageServer("fin");
                                        } else {
                                            //MOSTRAR  EXAMENES POR REALIZAR DEL CAPITULO
                                            $insBeanCrud->setMessageServer("general");
                                        }
                                    }

                                } else {
                                    //NO HAY EXAMEN PARA EL CAPITULO
                                    $insBeanCrud->setMessageServer("fin");
                                }
                            }

                        }

                    }

                } else {
                    //BUSCAR EL MAXIMO SUBTIULO PARA SABER EL EXAMEN DEL CAPITULO
                    $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS CONTADOR FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
                    $stmt->bindValue(":IDlibro", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                    $stmt->execute();
                    $datos1 = $stmt->fetchAll();

                    foreach ($datos1 as $row1) {
                        if ($row1['CONTADOR'] == null || $row1['CONTADOR'] != $leccionMaximo->getSubtitulo()) {

                            // echo ("NO SON IGUALES");
                            //BUSCAR SI HAY EXAMENES POR REALIZAR
                            $stmt = $this->conexion_db->prepare("SELECT COUNT(iddetalle_test) AS CONTADOR FROM `detalle_test` WHERE codigo_subtitulo=:IDlibro and idtest=(SELECT idtest FROM `test` WHERE codigotitulo=:IDTitulo and tipo=2)");
                            $stmt->bindValue(":IDlibro", $leccionMaximo->getSubtitulo(), PDO::PARAM_STR);
                            $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                            $stmt->execute();
                            $datos2 = $stmt->fetchAll();
                            foreach ($datos2 as $row2) {
                                $insBeanPagination->setCountFilter($row2['CONTADOR']);
                                if ($row2['CONTADOR'] > 0) {
                                    //SI HAY EXAMEN INTERNO POR REALIZAR
                                    //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL SUBTITULO INTERNO
                                    $stmt = $this->conexion_db->prepare("SELECT count(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:SubTitulo and tipo=2");
                                    $stmt->bindValue(":Cuenta", $leccionMaximo->getCuenta(), PDO::PARAM_STR);
                                    $stmt->bindValue(":SubTitulo", $leccionMaximo->getSubtitulo(), PDO::PARAM_STR);

                                    $stmt->execute();
                                    $datos3 = $stmt->fetchAll();
                                    foreach ($datos3 as $row3) {

                                        if ($row3['CONTADOR'] == 0) {
                                            //NO REALIZO EL EXAMEN INTERNO
                                            //MOSTRAR  EXAMENES POR REALIZAR
                                            $insBeanCrud->setMessageServer("interno");
                                        } else {
                                            //YA REALIZO EL EXAMEN INTERNO
                                            //  MOSTRAR LECCION DEL SUBTITULO SIGUIENTE
                                            $insBeanCrud->setMessageServer("ok");
                                        }
                                    }

                                } else {
                                    //NO HAY EXAMEN INTERNO POR REALIZAR
                                    $insBeanCrud->setMessageServer("ok");

                                }
                            }

                        } else {
                            //echo ("cumlmino el capitulo"/OBTENER EL EXAMEN DEL CAPITULO);

                            //BUSCAR SI HAY EXAMENES POR REALIZAR DEL CAPITULO CULMINADO
                            $stmt = $this->conexion_db->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE codigotitulo=:IDTitulo and tipo=1");
                            $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                            $stmt->execute();
                            $datos2 = $stmt->fetchAll();
                            foreach ($datos2 as $row2) {
                                if ($row2['CONTADOR'] > 0) {
                                    //SI HAY EXAMEN PARA EL CAPITULO
                                    //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL CAPITULO
                                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:IDTitulo and tipo=1");
                                    $stmt->bindValue(":Cuenta", $leccionMaximo->getCuenta(), PDO::PARAM_STR);
                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos3 = $stmt->fetchAll();
                                    foreach ($datos3 as $row3) {
                                        if ($row3['CONTADOR'] > 0) {
                                            //YA REGISTRARTE EL EXAMEN DEL CAPITULO
                                            //ir al siguiente capitulo
                                            // aumentar capitulo
                                            $insBeanCrud->setMessageServer("ok");
                                        } else {
                                            //MOSTRAR  EXAMENES POR REALIZAR DEL CAPITULO
                                            $insBeanCrud->setMessageServer("general");
                                        }
                                    }

                                } else {
                                    //NO HAY EXAMEN PARA EL CAPITULO
                                    $insBeanCrud->setMessageServer("ok");
                                }
                            }

                        }

                    }
                }

            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function obtener_lecciones_alumno_siguiente_controlador($leccion)
    {
        $insBeanCrud = new BeanCrud();
        $insBeanPagination = new BeanPagination();
        try {
            $leccionMaximo = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "leccion-maximo", $leccion);
            if ($leccionMaximo['countFilter'] == 0) {
                $insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video-minimo", $leccion));
            } else {
                $array = explode(".", $leccionMaximo['list'][0]['subTitulo']);
                if ($leccionMaximo['list'][0]['estado'] == null || $leccionMaximo['list'][0]['estado'] == 0) {

                    $leccion->setSubTitulo($leccionMaximo['list'][0]['subTitulo']);
                    //MOSTRAR EL MISMO SUBTITULO
                    $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video", $leccion);
                    //  MOSTRAR LECCION DEL SUBTITULO SIGUIENTE
                    $insBeanCrud->setBeanPagination($lecciones);
                } else {
                    // devolver el siguiente subtitulo
                    $numerofinal = ($array[3] + 1);
                    if (strlen($numerofinal) == 1) {
                        $numerofinal = "0" . $numerofinal;
                    }

                    $leccion->setSubTitulo($array[0] . "." . $array[1] . "." . $array[2] . "." . $numerofinal);

                    //BUSCAR EL MAXIMO SUBTIULO PARA SABER SI CULMINO EL LIBRO
                    $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS MAXIMO FROM `subtitulo` WHERE codigo_subtitulo LIKE concat('%',:IDlibro,'%')");
                    $stmt->bindValue(":IDlibro", $array[0], PDO::PARAM_STR);
                    $stmt->execute();
                    $variable = $stmt->fetchAll();
                    foreach ($variable as $row) {

                        if ($row['MAXIMO'] == null || $row['MAXIMO'] == $leccionMaximo['list'][0]['subTitulo']) {
                            //culmino CURSO
                            //BUSCAR EL MAXIMO SUBTIULO PARA SABER EL EXAMEN DEL CAPITULO
                            $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS CONTADOR FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
                            $stmt->bindValue(":IDlibro", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                            $stmt->execute();
                            $datos1 = $stmt->fetchAll();
                            foreach ($datos1 as $row1) {

                                if ($row1['CONTADOR'] == null || $row1['CONTADOR'] != $leccionMaximo['list'][0]['subTitulo']) {
                                    // echo ("NO SON IGUALES");
                                    //BUSCAR SI HAY EXAMENES POR REALIZAR
                                    $stmt = $this->conexion_db->prepare("SELECT COUNT(iddetalle_test) AS CONTADOR FROM `detalle_test` WHERE codigo_subtitulo=:IDlibro and idtest=(SELECT idtest FROM `test` WHERE codigotitulo=:IDTitulo and tipo=2)");
                                    $stmt->bindValue(":IDlibro", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);
                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos2 = $stmt->fetchAll();
                                    foreach ($datos2 as $row2) {

                                        $insBeanPagination->setCountFilter($row2['CONTADOR']);
                                        if ($row2['CONTADOR'] > 0) {
                                            //SI HAY EXAMEN INTERNO POR REALIZAR
                                            //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL SUBTITULO INTERNO
                                            $stmt = $this->conexion_db->prepare("SELECT count(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:SubTitulo and tipo=2 and tipo_estado=1");
                                            $stmt->bindValue(":Cuenta", $leccion->getCuenta(), PDO::PARAM_STR);
                                            $stmt->bindValue(":SubTitulo", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);

                                            $stmt->execute();
                                            $datos3 = $stmt->fetchAll();
                                            foreach ($datos3 as $row3) {

                                                if ($row3['CONTADOR'] == 0) {
                                                    //NO REALIZO EL EXAMEN INTERNO
                                                    //MOSTRAR  EXAMENES POR REALIZAR
                                                    $stmt = $this->conexion_db->prepare("SELECT TES.nombre as test_nombre,DET.*, TES.tipo AS tes_tipo, TES.descripcion AS descrip, SUB.nombre AS sub_nombre, TIT.tituloNombre AS tit_nombre FROM `detalle_test`AS DET INNER JOIN `test` AS TES ON TES.idtest=DET.idtest INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=DET.codigo_subtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo WHERE DET.codigo_subtitulo=:IDlibro and TES.codigotitulo=:IDTitulo and TES.tipo=2");
                                                    $stmt->bindValue(":IDlibro", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);
                                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                                    $stmt->execute();
                                                    $datos2 = $stmt->fetchAll();
                                                    foreach ($datos2 as $row2) {
                                                        $insTitulo = new Titulo();
                                                        $insTitulo->setNombre($row2['tit_nombre']);

                                                        $insSubTitulo = new SubTitulo();
                                                        $insSubTitulo->setDescripcion($row2['sub_nombre']);
                                                        $insSubTitulo->setCodigo($row2['codigo_subtitulo']);
                                                        $insSubTitulo->setTitulo($insTitulo->__toString());

                                                        $insTest = new Test();
                                                        $insTest->setIdTest($row2['idtest']);
                                                        $insTest->setNombre($row2['test_nombre']);
                                                        $insTest->setDescripcion($row2['descrip']);
                                                        $insTest->setTipo($row2['tes_tipo']);
                                                        $insDetalleTest = new DetalleTest();
                                                        $insDetalleTest->setIdDetalleTest($row2['iddetalle_test']);
                                                        $insDetalleTest->setSubtitulo($insSubTitulo->__toString());
                                                        $insDetalleTest->setDescripcion($row2['descripcion']);
                                                        $insDetalleTest->setTest($insTest->__toString());
                                                        $insBeanPagination->setList($insDetalleTest->__toString());
                                                        $insBeanCrud->setBeanPagination($insBeanPagination->__toString());

                                                    }
                                                    $insBeanCrud->setMessageServer("interno");
                                                } else {
                                                    //YA REALIZO EL EXAMEN INTERNO FIN
                                                    $insBeanCrud->setMessageServer("fin");
                                                    $insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video-maximo", $leccion));

                                                }
                                            }

                                        } else {
                                            //NO HAY EXAMEN INTERNO POR REALIZAR FIN
                                            $insBeanCrud->setMessageServer("fin");
                                            $insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video-maximo", $leccion));

                                        }
                                    }

                                } else {
                                    //echo ("cumlmino el capitulo"/OBTENER EL EXAMEN DEL CAPITULO);

                                    //BUSCAR SI HAY EXAMENES POR REALIZAR DEL CAPITULO CULMINADO
                                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE codigotitulo=:IDTitulo and tipo=1");
                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos2 = $stmt->fetchAll();
                                    foreach ($datos2 as $row2) {

                                        if ($row2['CONTADOR'] > 0) {
                                            //SI HAY EXAMEN PARA EL CAPITULO
                                            //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL CAPITULO
                                            $stmt = $this->conexion_db->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:IDTitulo and tipo=1  and tipo_estado=1");
                                            $stmt->bindValue(":Cuenta", $leccion->getCuenta(), PDO::PARAM_STR);
                                            $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                            $stmt->execute();
                                            $datos3 = $stmt->fetchAll();
                                            foreach ($datos3 as $row3) {
                                                if ($row3['CONTADOR'] > 0) {
                                                    //YA REGISTRARTE EL EXAMEN DEL CAPITULO FIN
                                                    $insBeanCrud->setMessageServer("fin");
                                                    $insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video-maximo", $leccion));

                                                } else {
                                                    //MOSTRAR  EXAMENES POR REALIZAR DEL CAPITULO
                                                    $stmt = $this->conexion_db->prepare("SELECT DET.*, TES.tipo AS tes_tipo, TES.descripcion AS descrip, SUB.nombre AS sub_nombre, TIT.tituloNombre AS tit_nombre FROM `detalle_test`AS DET INNER JOIN `test` AS TES ON TES.idtest=DET.idtest INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=DET.codigo_subtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo WHERE TES.codigotitulo=:IDTitulo and TES.tipo=1");
                                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                                    $stmt->execute();
                                                    $datos2 = $stmt->fetchAll();
                                                    foreach ($datos2 as $row2) {
                                                        $insTitulo = new Titulo();
                                                        $insTitulo->setNombre($row2['tit_nombre']);

                                                        $insSubTitulo = new SubTitulo();
                                                        $insSubTitulo->setDescripcion($row2['sub_nombre']);
                                                        $insSubTitulo->setCodigo($row2['codigo_subtitulo']);
                                                        $insSubTitulo->setTitulo($insTitulo->__toString());

                                                        $insTest = new Test();
                                                        $insTest->setIdTest($row2['idtest']);
                                                        $insTest->setDescripcion($row2['descrip']);
                                                        $insTest->setTipo($row2['tes_tipo']);
                                                        $insDetalleTest = new DetalleTest();
                                                        $insDetalleTest->setIdDetalleTest($row2['iddetalle_test']);
                                                        $insDetalleTest->setSubtitulo($insSubTitulo->__toString());
                                                        $insDetalleTest->setDescripcion($row2['descripcion']);
                                                        $insDetalleTest->setTest($insTest->__toString());
                                                        $insBeanPagination->setList($insDetalleTest->__toString());
                                                        $insBeanCrud->setBeanPagination($insBeanPagination->__toString());

                                                    }
                                                    $insBeanCrud->setMessageServer("general");

                                                }
                                            }

                                        } else {

                                            //NO HAY EXAMEN PARA EL CAPITULO FIN DEL CURSO FIN
                                            $insBeanCrud->setMessageServer("fin");
                                            $insBeanCrud->setBeanPagination(leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video-maximo", $leccion));
                                        }
                                    }

                                }

                            }

                        } else {
                            //BUSCAR EL MAXIMO SUBTIULO PARA SABER EL EXAMEN DEL CAPITULO
                            $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS CONTADOR FROM `subtitulo` WHERE codigo_subtitulo like CONCAT('%',:IDlibro,'%')");
                            $stmt->bindValue(":IDlibro", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                            $stmt->execute();
                            $datos1 = $stmt->fetchAll();
                            foreach ($datos1 as $row1) {

                                if ($row1['CONTADOR'] == null || $row1['CONTADOR'] != $leccionMaximo['list'][0]['subTitulo']) {
                                    // echo ("NO SON IGUALES");
                                    //BUSCAR SI HAY EXAMENES POR REALIZAR
                                    $stmt = $this->conexion_db->prepare("SELECT COUNT(iddetalle_test) AS CONTADOR FROM `detalle_test` WHERE codigo_subtitulo=:IDlibro and idtest=(SELECT idtest FROM `test` WHERE codigotitulo=:IDTitulo and tipo=2)");
                                    $stmt->bindValue(":IDlibro", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);
                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos2 = $stmt->fetchAll();
                                    foreach ($datos2 as $row2) {

                                        $insBeanPagination->setCountFilter($row2['CONTADOR']);
                                        if ($row2['CONTADOR'] > 0) {
                                            //SI HAY EXAMEN INTERNO POR REALIZAR
                                            //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL SUBTITULO INTERNO
                                            $stmt = $this->conexion_db->prepare("SELECT count(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:SubTitulo and tipo=2 and tipo_estado=1");
                                            $stmt->bindValue(":Cuenta", $leccion->getCuenta(), PDO::PARAM_STR);
                                            $stmt->bindValue(":SubTitulo", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);

                                            $stmt->execute();
                                            $datos3 = $stmt->fetchAll();
                                            foreach ($datos3 as $row3) {

                                                if ($row3['CONTADOR'] == 0) {
                                                    //NO REALIZO EL EXAMEN INTERNO
                                                    //MOSTRAR  EXAMENES POR REALIZAR
                                                    $stmt = $this->conexion_db->prepare("SELECT TES.nombre as test_nombre,DET.*, TES.tipo AS tes_tipo, TES.descripcion AS descrip, SUB.nombre AS sub_nombre, TIT.tituloNombre AS tit_nombre FROM `detalle_test`AS DET INNER JOIN `test` AS TES ON TES.idtest=DET.idtest INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=DET.codigo_subtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo WHERE DET.codigo_subtitulo=:IDlibro and TES.codigotitulo=:IDTitulo and TES.tipo=2");
                                                    $stmt->bindValue(":IDlibro", $leccionMaximo['list'][0]['subTitulo'], PDO::PARAM_STR);
                                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                                    $stmt->execute();
                                                    $datos2 = $stmt->fetchAll();
                                                    foreach ($datos2 as $row2) {
                                                        $insTitulo = new Titulo();
                                                        $insTitulo->setNombre($row2['tit_nombre']);

                                                        $insSubTitulo = new SubTitulo();
                                                        $insSubTitulo->setDescripcion($row2['sub_nombre']);
                                                        $insSubTitulo->setCodigo($row2['codigo_subtitulo']);

                                                        $insSubTitulo->setTitulo($insTitulo->__toString());

                                                        $insTest = new Test();
                                                        $insTest->setIdTest($row2['idtest']);
                                                        $insTest->setDescripcion($row2['descrip']);
                                                        $insTest->setNombre($row2['test_nombre']);
                                                        $insTest->setTipo($row2['tes_tipo']);
                                                        $insDetalleTest = new DetalleTest();
                                                        $insDetalleTest->setIdDetalleTest($row2['iddetalle_test']);
                                                        $insDetalleTest->setSubtitulo($insSubTitulo->__toString());
                                                        $insDetalleTest->setDescripcion($row2['descripcion']);
                                                        $insDetalleTest->setTest($insTest->__toString());
                                                        $insBeanPagination->setList($insDetalleTest->__toString());
                                                        $insBeanCrud->setBeanPagination($insBeanPagination->__toString());

                                                    }
                                                    $insBeanCrud->setMessageServer("interno");
                                                } else {
                                                    //YA REALIZO EL EXAMEN INTERNO
                                                    //  MOSTRAR LECCION DEL SUBTITULO SIGUIENTE
                                                    $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video", $leccion);
                                                    $insBeanCrud->setBeanPagination($lecciones);

                                                }
                                            }

                                        } else {
                                            //NO HAY EXAMEN INTERNO POR REALIZAR
                                            $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video", $leccion);
                                            //  MOSTRAR LECCION DEL SUBTITULO SIGUIENTE
                                            $insBeanCrud->setBeanPagination($lecciones);

                                        }
                                    }

                                } else {
                                    //echo ("cumlmino el capitulo"/OBTENER EL EXAMEN DEL CAPITULO);

                                    //BUSCAR SI HAY EXAMENES POR REALIZAR DEL CAPITULO CULMINADO
                                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE codigotitulo=:IDTitulo and tipo=1");
                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos2 = $stmt->fetchAll();
                                    foreach ($datos2 as $row2) {

                                        if ($row2['CONTADOR'] > 0) {
                                            //SI HAY EXAMEN PARA EL CAPITULO
                                            //BUSCAR SI EL ALUMNO YA REALIZÓ SU EXAMEN DEL CAPITULO
                                            $stmt = $this->conexion_db->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:IDTitulo and tipo=1  and tipo_estado=1");
                                            $stmt->bindValue(":Cuenta", $leccion->getCuenta(), PDO::PARAM_STR);
                                            $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                            $stmt->execute();
                                            $datos3 = $stmt->fetchAll();
                                            foreach ($datos3 as $row3) {
                                                if ($row3['CONTADOR'] > 0) {
                                                    //YA REGISTRARTE EL EXAMEN DEL CAPITULO
                                                    //ir al siguiente capitulo

                                                    // aumentar capitulo
                                                    $numerofinal = ($array[2] + 1);
                                                    if (strlen($numerofinal) == 1) {
                                                        $numerofinal = "0" . $numerofinal;
                                                    }
                                                    $leccion->setSubTitulo($array[0] . "." . $array[1] . "." . $numerofinal . "." . "01");
                                                    $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video", $leccion);
                                                    $insBeanCrud->setBeanPagination($lecciones);

                                                } else {
                                                    //MOSTRAR  EXAMENES POR REALIZAR DEL CAPITULO
                                                    $stmt = $this->conexion_db->prepare("SELECT DET.*, TES.tipo AS tes_tipo, TES.descripcion AS descrip, SUB.nombre AS sub_nombre, TIT.tituloNombre AS tit_nombre FROM `detalle_test`AS DET INNER JOIN `test` AS TES ON TES.idtest=DET.idtest INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=DET.codigo_subtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo WHERE TES.codigotitulo=:IDTitulo and TES.tipo=1");
                                                    $stmt->bindValue(":IDTitulo", $array[0] . "." . $array[1] . "." . $array[2], PDO::PARAM_STR);
                                                    $stmt->execute();
                                                    $datos2 = $stmt->fetchAll();
                                                    foreach ($datos2 as $row2) {
                                                        $insTitulo = new Titulo();
                                                        $insTitulo->setNombre($row2['tit_nombre']);

                                                        $insSubTitulo = new SubTitulo();
                                                        $insSubTitulo->setDescripcion($row2['sub_nombre']);
                                                        $insSubTitulo->setCodigo($row2['codigo_subtitulo']);
                                                        $insSubTitulo->setTitulo($insTitulo->__toString());

                                                        $insTest = new Test();
                                                        $insTest->setIdTest($row2['idtest']);
                                                        $insTest->setDescripcion($row2['descrip']);
                                                        $insTest->setTipo($row2['tes_tipo']);
                                                        $insDetalleTest = new DetalleTest();
                                                        $insDetalleTest->setIdDetalleTest($row2['iddetalle_test']);
                                                        $insDetalleTest->setSubtitulo($insSubTitulo->__toString());
                                                        $insDetalleTest->setDescripcion($row2['descripcion']);
                                                        $insDetalleTest->setTest($insTest->__toString());
                                                        $insBeanPagination->setList($insDetalleTest->__toString());
                                                        $insBeanCrud->setBeanPagination($insBeanPagination->__toString());

                                                    }
                                                    $insBeanCrud->setMessageServer("general");

                                                }
                                            }

                                        } else {

                                            //NO HAY EXAMEN PARA EL CAPITULO
                                            $lecciones = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "subtitulo-video", $leccion);
                                            //  MOSTRAR LECCION DEL SUBTITULO SIGUIENTE
                                            $insBeanCrud->setBeanPagination($lecciones);
                                        }
                                    }

                                }

                            }
                        }

                    }
                }

            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function reporte_lecciones_controlador($leccion)
    {
        $insBeanCrud = null;
        try {
            $lista = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "reporte", $leccion)['list'];
            $insEmpresa = new Empresa();
            $stmt = $this->conexion_db->prepare("SELECT * FROM `empresa`");
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insEmpresa->setTelefono($row['EmpresaTelefono']);
                //$insEmpresa->setYoutube($row['youtube']);
                $insEmpresa->setNombre($row['EmpresaNombre']);
                $insEmpresa->setEmail($row['EmpresaEmail']);
                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                $insEmpresa->setLogo($row['EmpresaLogo']);
                //$insEmpresa->setFacebook($row['facebook']);
            }
            $insBeanCrud = self::HTML_reporte($insEmpresa->__toString(), $lista);

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud;
    }

    public function HTML_reporte($empresa, $data)
    {
        // print_r($data);
        // print_r($empresa);
        $html = '<!DOCTYPE html>
        <html lang="en">

        <head>
        <meta charset="utf-8">
        <title>COMENTARIO</title>
        <link rel="stylesheet" href="' . SERVERURL . 'vistas/subprojects/pdf/comentario.css?v=0.23"     media="all" />

            </head>

            <body>
        <header class="clearfix">
        <div id="logo">
        <img src="' . SERVERURL . 'adjuntos/logoHeader.jpg">
        </div>
        <div id="company">
        <h2 class="name" style="padding-top: 0.6cm;
        text-align: start;
        font-weight: bold;">Club de Lectura para Emprendedores</h2>

        </div>
        </header>
        <main>
            <div id="details" class="clearfix">

                <div id="invoice">
                    <h1 style="font-size: 25px;">' . $data[0]['subTitulo']['libro'] . '</h1>
                    <h2 class="name" style="font-weight: bold;">' . $data[0]['subTitulo']['titulo'] . '</   h2>
                    <div class="address">' . $data[0]['subTitulo']['subTitulo'] . '</div>
                    <!--div class="date">Realizado: 01/06/2014</div-->
                </div>
                <div id="client">
                    <div class="to">Alumno:</div>
                    <h2 class="name"> ' . $data[0]['cuenta']['nombre_completo'] . '</h2>
                    <div class="address">' . $data[0]['cuenta']['ocupacion'] . '</div>
                    <div class="address">' . $data[0]['cuenta']['telefono'] . '</div>
                    <div class="email"><a href="mailto:' . $data[0]['cuenta']['email'] . '">' . $data[0]['cuenta']['email'] . '</a></div>
                </div>
            </div>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th class="no">COMENTARIO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="desc" style="font-size: 21px;">
                        ' . $data[0]['comentario'] . '
                        </td>
                    </tr>

                </tbody>

            </table>

        </main>
        <footer>
            Copyright CLPE © 2020 v 1.0
        </footer>
            </body>

            </html>';
        return $html;
    }

    public function reporte_certificado_controlador($leccion)
    {
        $insBeanCrud = null;
        try {
            $lista = leccionesModelo::datos_lecciones_modelo($this->conexion_db, "reporte-certificado", $leccion)['list'];
            $insEmpresa = new Empresa();
            $stmt = $this->conexion_db->prepare("SELECT * FROM `empresa`");
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insEmpresa->setTelefono($row['EmpresaTelefono']);
                //$insEmpresa->setYoutube($row['youtube']);
                $insEmpresa->setNombre($row['EmpresaNombre']);
                $insEmpresa->setEmail($row['EmpresaEmail']);
                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                $insEmpresa->setLogo($row['EmpresaLogo']);
                //$insEmpresa->setFacebook($row['facebook']);
            }
            $insBeanCrud = self::HTML_reporte_certificado($insEmpresa->__toString(), $lista);

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud;
    }

    public function HTML_reporte_certificado($empresa, $data)
    {
        // print_r($data);
        // print_r($empresa);
        $fecha = explode(" ", $data[0]['fecha']);
        $arrayFecha = explode("-", $fecha[0]);

        $fecha_inicial = explode(" ", $data[0]['cuenta']['fecha_inicial']);
        $arrayFecha_inicial = explode("-", $fecha_inicial[0]);

        $html = '<!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <title>COMENTARIO</title>
            <link rel="stylesheet" href="' . SERVERURL . 'vistas/subprojects/pdf/comentario.css?v=0.25"         media="all" />

        </head>

        <body style="width: 26cm;color: black;height: 20cm;">
            <img style="position: absolute;
            z-index: -1;width: 100%;" src="' . SERVERURL . 'adjuntos/image/marco.png">
            <header class="clearfix" style="border:0 solid transparent;padding-top: 2cm;">
                <div id="logo" style="width: 20%">
                    <img style="width: 100px;height: 100px;" src="' . SERVERURL . 'adjuntos/logoHeader.jpg">
                </div>
                <div id="company" style="width: 80%">
                    <h1 class="name" style="padding-top: 0.4cm;margin-left: 11px;
                    text-align: start;font-family: system-ui;font-size: 31px;
                    font-weight: bold;">CLUB DE LECTURA PARA EMPRENDEDORES</h1>
                    <h2 class="name"
                        style="font-weight: bold; text-align: center;font-family: system-ui;font-size: 26px;        width: 16cm;">
                        CERTIFICA QUE:</h2>
                </div>

            </header>
            <main>
                <div id="details" class="clearfix">

                    <div id="invoice" style="width: 100%;">
                        <h2>' . $data[0]['cuenta']['nombre_completo'] . '</h2>
                        <div class="address" style="font-size: 22px;">Ha participado satisfactoriamente en el       curso de:</div>
                        <h2 class="name" style="font-weight: bold;font-family: system-ui;">LECTURA Y        APLICACIÓN DEL LIBRO</h2>
                        <h2 class="name" style="font-weight: bold;">“Piense y Hágase Rico” de Napoleón Hill –       Nivel básico</h2>
                        <div class="address" style="padding-right: 4cm;padding-top: 7px; padding-left: 4cm;font-size: 22px;      ">Con una duración de 92 horas, realizado de forma virtual desde el ' . $arrayFecha_inicial[2] . ' de  ' . self::Mes($arrayFecha_inicial[1]) . ' del ' . $arrayFecha_inicial[0] . ' hasta el ' . $arrayFecha[2] . ' de  ' . self::Mes($arrayFecha[1]) . ' del ' . $arrayFecha[0] . '
                        </div>
                    </div>
                    <table style="margin-top: 120px;margin-left: 50px;margin-right: 50px;width: 100%;">

                        <tbody>
                            <tr>
                                <td class="desc" style="text-align: center;background: transparent;padding: 0;      ">
                                    <img style="width: 150px;position: absolute;bottom: 4.5cm;left: 5cm;"
                                        src="' . SERVERURL . 'adjuntos/image/FIRMAJHEINER.png">
                                </td>
                                <td class="desc" style="text-align: center;background: transparent;padding: 0;      ">
                                    <img style="width: 90px;position: absolute;bottom: 4.5cm;right: 6.5cm;"
                                        src="' . SERVERURL . 'adjuntos/image/FIRMAVICTOR.png">
                                </td>
                            </tr>
                            <tr>

                                <td class="desc"
                                    style="text-align: center;background: transparent;font-size: 22px;      padding-right: 85px;padding-left: 85px;">
                                    <div style="border-top: 1px solid;">
                                        Jheiner García Armijos
                                    </div>

                                </td>
                                <td class=" desc"
                                    style="text-align: center;background: transparent;font-size: 22px;      padding-right: 85px;padding-left: 85px;">
                                    <div style="border-top: 1px solid;">
                                        Ing. Víctor Mejía Acuña
                                    </div>

                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>


            </main>

        </body>
        </html>';
        return $html;
    }
    private function Mes(int $variable)
    {
        switch ((int) $variable) {
            case 1:
                return "enero";
            case 2:
                return "febrero";
            case 3:
                return "marzo";
            case 4:
                return "abril";
            case 5:
                return "mayo";
            case 6:
                return "junio";
            case 7:
                return "julio";
            case 8:
                return "agosto";
            case 9:
                return "setiembre";
            case 10:
                return "octubre";
            case 11:
                return "noviembre";
            case 12:
                return "diciembre";

            default:
                # code...
                return "";
        }
    }

    public function excel_lecciones_controlador($tipo, $codigo)
    {
        $row = "";

        try {
            $variable = leccionesModelo::datos_lecciones_modelo($this->conexion_db, $tipo, $codigo);

            if ($variable['countFilter'] > 0) {
                $titulo = "TAREAS DE ALUMNOS";
                $row = "<table border='1'>
        <thead>
        <tr>
        <th colspan='8'> LISTA DE $titulo </th>
        </tr>
        <tr>
          <th ></th>
          <th >CÓDIGO</th>
          <th >SUBTÍTULO</th>
          <th >COMENTARIO</th>
          <th >FECHA</th>
          <th >NOMBRES</th>
          <th >APELLIDOS</th>
          <th >OCUPACION/OFICIO</th>
          <th >PAÍS</th>
          <th>TELÉFONO</th>
        </tr>
      </thead>
      <tbody> ";
                $contador = 1;
                foreach ($variable['list'] as $value) {
                    $row = $row . "
            <tr>
            <td>" . ($contador++) . "</td>
              <td>" . $value['subTitulo']['codigo'] . "</td>
              <td>" . $value['subTitulo']['nombre'] . "</td>
              <td>" . $value['comentario'] . "</td>
              <td>" . $value['fecha'] . "</td>
              <td>" . $value['estado']['nombre'] . "</td>
              <td>" . $value['estado']['apellido'] . "</td>
              <td>" . $value['estado']['ocupacion'] . "</td>
              <td>" . $value['estado']['pais'] . "</td>
              <td>" . (string) ($value['estado']['telefono']) . "</td>
            </tr>";
                }
                $row = $row . "</tbody> </table>";

                header("Content-Disposition:attachment;filename=$titulo.xls");

            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $row;
    }
}
