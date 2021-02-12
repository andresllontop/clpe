<?php

require_once './modelos/noticiaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

class noticiaControlador extends noticiaModelo
{
    public function agregar_noticia_controlador($Noticia)
    {

        $insBeanCrud = new BeanCrud();
        $Noticia->setIdNoticia(mainModel::limpiar_cadena($Noticia->getIdNoticia()));

        $original = $_FILES['Imagen-reg'];
        $nombre = $original['name'];
        if ($original['error'] > 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "Se encontro un error al subir el archivo seleccione otra imagen",
                "Tipo" => "error",

            ];
        } else {
            $permitidos = array("image/png", "image/jpg", "image/jpeg");
            $limite_MB = 1700;
            if (in_array($original['type'], $permitidos) && ($original['size'] <= $limite_MB * 1024)) {
                $array_nombre = explode('.', $nombre);
                $extension = array_pop($array_nombre);
                $array = glob("../adjuntos/slider/" . $array_nombre[0] . "*." . $extension);
                $cantidad = count($array);
                $nombreImagen = $array_nombre[0] . $cantidad . "." . $extension;
                $resultado_guardado = move_uploaded_file($original['tmp_name'], "../adjuntos/slider/" . $nombreImagen);
                if ($resultado_guardado) {
                    $dataAC = [
                        "Descripcion" => $descripcion,
                        "Imagen" => $nombreImagen,
                    ];
                    $guardarnoticia = noticiaModelo::agregar_noticia_modelo($dataAC);
                    if ($guardarnoticia >= 1) {
                        $alerta = [
                            "Alerta" => "limpiar",
                            "Titulo" => "La Noticia Registrado",
                            "Texto" => "la noticia se registro con exito en el sistema",
                            "Tipo" => "success",
                        ];

                    } else {

                        $alerta = [
                            "Alerta" => "simple",
                            "Titulo" => "Ocurrio un error inesperado",
                            "Texto" => "No hemos podido registrar el administrador",
                            "Tipo" => "error",

                        ];
                    }
                } else {
                    $alerta = [
                        "Alerta" => "simple",
                        "Titulo" => "Ocurrio un error inesperado",
                        "Texto" => "Hubo un error al guardar la imagen",
                        "Tipo" => "error",

                    ];

                }

            } else {
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Ocurrio un error inesperado",
                    "Texto" => "Archivo no permitido o excede al tama\00f1o",
                    "Tipo" => "error",

                ];
            }

        }
        return json_encode($alerta);
    }
    public function datos_noticia_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $codigo = mainModel::limpiar_cadena($codigo);
            $insBeanCrud->setBeanPagination(noticiaModelo::datos_noticia_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_noticia_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->prepare("SELECT COUNT(idnoticia) AS CONTADOR  FROM `noticia`");
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `noticia` ORDER BY idnoticia ASC LIMIT ?,?");
                    $stmt->bindParam(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindParam(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $insLibro = new Libro();
                        $insLibro->setIdLibro($row['idlibro']);
                        $insLibro->setCodigo($row['codigo']);
                        $insLibro->setVideo($row['libroVideo']);
                        $insLibro->setNombre($row['nombre']);
                        $insLibro->setImagenOtro($row['desImagen']);
                        $insLibro->setImagen($row['imagen']);
                        $insLibro->setEstado($row['estado']);
                        $insLibro->setDescripcion($row['descripcion']);

                        $insnoticia = new Titulo();
                        $insnoticia->setIdTitulo($row['idtitulo']);
                        $insnoticia->setCodigo($row['codigoTitulo']);
                        $insnoticia->setPdf($row['PDF']);
                        $insnoticia->setDescripcion($row['tituloDescripcion']);
                        $insnoticia->setEstado($row['TituloEstado']);
                        $insnoticia->setNombre($row['tituloNombre']);

                        $insnoticia->setLibro($insLibro->__toString());
                        $insBeanPagination->setList($insnoticia->__toString());
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
    public function bean_paginador_noticia_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $codigo = mainModel::limpiar_cadena($codigo);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_noticia_controlador($this->conexion_db, $inicio, $registros, $codigo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_noticia_controlador()
    {
        $codigo = mainModel::limpiar_cadena($_POST['ID-reg']);
        $lnoticia = noticiaModelo::datos_noticia_modelo("unico", $codigo);
        $guardarAdmin = noticiaModelo::eliminar_noticia_modelo($codigo);
        if ($guardarAdmin >= 1) {
            unlink('./adjuntos/slider/' . $lnoticia[0]['imagen']);
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Adminitrador Eliminado",
                "Texto" => "El noticia se Elimino con \00e9xito en el sistema",
                "Tipo" => "success",
            ];

        } else {

            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "No hemos podido Eliminar el noticia",
                "Tipo" => "error",

            ];
        }
        return json_encode($alerta);

    }
    public function actualizar_noticia_controlador($Noticia)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Noticia->setIdnoticia(mainModel::limpiar_cadena($Noticia->getIdnoticia()));
            $Noticia->setTitulo(mainModel::limpiar_cadena($Noticia->getTitulo()));
            $noticiaLista = noticiaModelo::datos_noticia_modelo($this->conexion_db, "unico", $Noticia);
            if ($noticiaLista["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No hemos encontrado la noticia");
            } else {
                if (isset($_FILES['txtNoticiaInicio'])) {
                    $original = $_FILES['txtNoticiaInicio'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("error en el servidor, Se encontro un error al subir el archivo seleccione otra imagen");
                    } else {
                        //10 MB
                        $resultado_guardado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), (10 * 1024), $original, $nombre, "./adjuntos/slider/");
                        if ($resultado_guardado != "") {
                            $Noticia->setImagen($resultado_guardado);
                            $stmt = noticiaModelo::actualizar_noticia_modelo($this->conexion_db, $Noticia);
                            if ($stmt->execute()) {
                                unlink('./adjuntos/slider/' . $noticiaLista["list"][0]['imagen']);
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::datos_noticia_controlador("conteo", 0));

                            } else {
                                // unlink('./adjuntos/slider/' . $resultado_guardado);
                                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar la noticia cambia el nombre de la imagen");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar la imagen");

                        }

                    }
                } else {
                    $Noticia->setImagen($noticiaLista["list"][0]['imagen']);
                    $stmt = noticiaModelo::actualizar_noticia_modelo($this->conexion_db, $Noticia);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::datos_noticia_controlador("conteo", 0));

                    } else {
                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar la noticia");
                    }
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

}
