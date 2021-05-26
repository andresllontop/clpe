<?php

require_once './core/mainModel.php';

class leccionesModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_lecciones_modelo($conexion, $datos)
    {
        $sql = $conexion->prepare("INSERT INTO `lecciones` (cuenta_codigocuenta,subtitulo_codigosubtitulo,video,comentario,tipo_estado) VALUES(:Cuenta,:Subtitulo,:Video,:Comentario,:Estado)");
        $sql->bindValue(":Cuenta", $datos->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(":Subtitulo", $datos->getSubTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Video", $datos->getVideo(), PDO::PARAM_STR);
        $sql->bindValue(":Comentario", $datos->getComentario(), PDO::PARAM_STR);
        $sql->bindValue(":Estado", 0, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_lecciones_modelo($conexion, $datos)
    {

        $sql = $conexion->prepare("UPDATE `tarea` SET estado=:Estado WHERE idtarea=:ID");
        $sql->bindValue(":Estado", $datos->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(":ID", $datos->getCuenta(), PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_lecciones_alumno_modelo($conexion, $datos)
    {

        $sql = $conexion->prepare("UPDATE `lecciones` SET comentario=:Comentario,video=:Video WHERE cuenta_codigocuenta=:Cuenta and subtitulo_codigosubtitulo=:Subtitulo");
        $sql->bindValue(":Cuenta", $datos->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(":Subtitulo", $datos->getSubTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Video", $datos->getVideo(), PDO::PARAM_STR);
        $sql->bindValue(":Comentario", $datos->getComentario(), PDO::PARAM_STR);

        return $sql;
    }
    protected function datos_lecciones_modelo($conexion, $tipo, $Leccion)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idlecciones) AS CONTADOR FROM `lecciones` WHERE idlecciones=:IDlibro");
                    $stmt->bindValue(":IDlibro", $Leccion->getIdleccion(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `lecciones` WHERE idlecciones=:IDlibro");
                            $stmt->bindValue(":IDlibro", $Leccion->getIdleccion(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {
                                $insLeccion = new Leccion();
                                $insLeccion->setIdleccion($row['idlecciones']);
                                $insLeccion->setVideo($row['video']);
                                $insLeccion->setComentario($row['comentario']);
                                $insLeccion->setSubTitulo($row['subtitulo_codigosubtitulo']);
                                $insLeccion->setCuenta($row['cuenta_codigocuenta']);

                                $insBeanPagination->setList($insLeccion->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $pagina = mainModel::limpiar_cadena($Leccion->getPagina());
                    $registros = mainModel::limpiar_cadena($Leccion->getRegistro());
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
                    $stmt = $conexion->prepare("SELECT COUNT(idlecciones) AS CONTADOR FROM `lecciones` where cuenta_codigocuenta=:Cuenta");
                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    $contador = 0;
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {

                            $stmt = $conexion->prepare("SELECT sub.*, lec.idlecciones,lec.video,lec.comentario,tar.fecha,tar.estado,tar.idtarea FROM `lecciones` as lec inner join `tarea` as tar ON tar.codigo_subtitulo=lec.subtitulo_codigosubtitulo inner join `subtitulo` as sub ON sub.codigo_subtitulo=lec.subtitulo_codigosubtitulo WHERE tar.tipo=0 and lec.cuenta_codigocuenta=:Cuenta and tar.cuenta=:Cuenta group by lec.subtitulo_codigosubtitulo ORDER BY tar.fecha DESC LIMIT :Pagina,:Registro");
                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->bindValue(":Pagina", $inicio, PDO::PARAM_INT);
                            $stmt->bindValue(":Registro", $registros, PDO::PARAM_INT);
                            $stmt->execute();
                            //   print_r($Leccion);
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                                $insLeccion->setEstado(array(
                                    "estadotarea" => $row['estado'],
                                    "idtarea" => $row['idtarea']));
                                $insLeccion->setFecha($row['fecha']);
                                $insLeccion->setSubTitulo($insSubTitulo->__toString());
                                $insBeanPagination->setList($insLeccion->__toString());
                            }

                            $stmt = $conexion->prepare("SELECT count(idtarea) as totalrefor FROM `tarea` where  tipo=1 and estado=0 and cuenta=:Cuenta");
                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBeanPagination->setList(array("reforsamiento" => $row['totalrefor']));
                            }
                            $stmt = $conexion->prepare("SELECT count(idtarea) as totalinterno FROM `tarea` where tipo=2  and estado=0 and cuenta=:Cuenta");

                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBeanPagination->setList(array("interno" => $row['totalinterno']));
                            }
                        }
                    }

                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "codigo":

                    $stmt = $conexion->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo` WHERE codigovideo_subtitulo=:IDlibro");
                    $stmt->bindValue(":IDlibro", $Leccion->getCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `video_subtitulo` WHERE codigovideo_subtitulo=:IDlibro");
                            $stmt->bindValue(":IDlibro", $Leccion->getCodigo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {

                                $insLeccion = new Leccion();
                                $insLeccion->setIdLeccion($row['idvideo_subtitulo']);
                                $insLeccion->setCodigo($row['codigovideo_subtitulo']);
                                $insLeccion->setNombre($row['nombreVideo']);
                                $insLeccion->setImagen($row['imagen']);

                                $insLeccion->setSubTitulo($row['subtitulo_codigosubtitulo']);
                                $insBeanPagination->setList($insLeccion->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;

                case "subtitulo-video":

                    $stmt = $conexion->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo`  WHERE subtitulo_codigosubtitulo =?");
                    $stmt->bindValue(1, $Leccion->getSubTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT VID_SUB.*,SUB.nombre,SUB.titulo_idtitulo,SUB.subtituloPDF,SUB.subtitulo_imagen,TIT.codigoTitulo,TIT.tituloNombre,TIT.libro_codigoLibro,LIB.nombre AS libro_nombre,LIB.imagen AS libro_imagen FROM `video_subtitulo` AS VID_SUB INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=VID_SUB.subtitulo_codigosubtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo INNER JOIN `libro` AS LIB ON LIB.codigo=TIT.libro_codigoLibro WHERE VID_SUB.subtitulo_codigosubtitulo = :Codigo_subtitulo ORDER BY VID_SUB.codigovideo_subtitulo");
                            $stmt->bindValue(":Codigo_subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row3) {

                                $insLibro = new Libro();
                                $insLibro->setCodigo($row3['libro_codigoLibro']);
                                $insLibro->setNombre($row3['libro_nombre']);
                                $insLibro->setImagen($row3['libro_imagen']);

                                $insCapitulo = new Titulo();
                                $insCapitulo->setIdTitulo($row3['titulo_idtitulo']);
                                $insCapitulo->setCodigo($row3['codigoTitulo']);
                                $insCapitulo->setNombre($row3['tituloNombre']);
                                //$insCapitulo->setImagen($row['titulo_imagen']);

                                $insCapitulo->setLibro($insLibro->__toString());

                                $insSubTitulo = new SubTitulo();
                                //$insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row3['subtitulo_codigosubtitulo']);
                                $insSubTitulo->setPdf($row3['subtituloPDF']);
                                $insSubTitulo->setNombre($row3['nombre']);
                                $insSubTitulo->setImagen($row3['subtitulo_imagen']);

                                $insSubTitulo->setTitulo($insCapitulo->__toString());

                                $insVideoSubTitulo = new VideoSubTitulo();
                                $insVideoSubTitulo->setIdVideoSubTitulo($row3['idvideo_subtitulo']);
                                $insVideoSubTitulo->setCodigo($row3['codigovideo_subtitulo']);
                                $insVideoSubTitulo->setNombre($row3['nombreVideo']);
                                $insVideoSubTitulo->setImagen($row3['imagen']);
                                $insVideoSubTitulo->setSubTitulo($insSubTitulo->__toString());
                                $insBeanPagination->setList($insVideoSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "subtitulo-video-minimo":
                    $stmt = $conexion->query("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo`");
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->query("SELECT MIN(subtitulo_codigosubtitulo) AS CONTADOR FROM `video_subtitulo`");
                            $datos2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos2 as $row2) {
                                $stmt = $conexion->prepare("SELECT VID_SUB.*,
                                SUB.nombre,SUB.titulo_idtitulo,SUB.subtituloPDF,SUB.subtitulo_imagen,
                                TIT.codigoTitulo,TIT.tituloNombre,TIT.libro_codigoLibro,
                                LIB.nombre AS libro_nombre,LIB.imagen AS libro_imagen FROM `video_subtitulo` AS VID_SUB INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=VID_SUB.subtitulo_codigosubtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo INNER JOIN `libro` AS LIB ON LIB.codigo=TIT.libro_codigoLibro WHERE VID_SUB.subtitulo_codigosubtitulo = :Codigo_subtitulo ORDER BY VID_SUB.codigovideo_subtitulo");
                                $stmt->bindValue(":Codigo_subtitulo", $row2['CONTADOR'], PDO::PARAM_STR);
                                $stmt->execute();
                                $datos3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($datos3 as $row3) {

                                    $insLibro = new Libro();
                                    $insLibro->setCodigo($row3['libro_codigoLibro']);
                                    $insLibro->setNombre($row3['libro_nombre']);
                                    $insLibro->setImagen($row3['libro_imagen']);

                                    $insCapitulo = new Titulo();
                                    $insCapitulo->setIdTitulo($row3['titulo_idtitulo']);
                                    $insCapitulo->setCodigo($row3['codigoTitulo']);
                                    $insCapitulo->setNombre($row3['tituloNombre']);
                                    //$insCapitulo->setImagen($row['titulo_imagen']);

                                    $insCapitulo->setLibro($insLibro->__toString());

                                    $insSubTitulo = new SubTitulo();
                                    //$insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                    $insSubTitulo->setCodigo($row3['subtitulo_codigosubtitulo']);
                                    $insSubTitulo->setPdf($row3['subtituloPDF']);
                                    $insSubTitulo->setNombre($row3['nombre']);
                                    $insSubTitulo->setImagen($row3['subtitulo_imagen']);

                                    $insSubTitulo->setTitulo($insCapitulo->__toString());

                                    $insVideoSubTitulo = new VideoSubTitulo();
                                    $insVideoSubTitulo->setIdVideoSubTitulo($row3['idvideo_subtitulo']);
                                    $insVideoSubTitulo->setCodigo($row3['codigovideo_subtitulo']);
                                    $insVideoSubTitulo->setNombre($row3['nombreVideo']);
                                    $insVideoSubTitulo->setImagen($row3['imagen']);
                                    $insVideoSubTitulo->setSubTitulo($insSubTitulo->__toString());
                                    $insBeanPagination->setList($insVideoSubTitulo->__toString());
                                }

                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "subtitulo-video-maximo":
                    $stmt = $conexion->query("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo`");
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->query("SELECT max(subtitulo_codigosubtitulo) AS CONTADOR FROM `video_subtitulo`");
                            $datos2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos2 as $row2) {
                                $stmt = $conexion->prepare("SELECT VID_SUB.*,
                                    SUB.nombre,SUB.titulo_idtitulo,SUB.subtituloPDF,SUB.subtitulo_imagen,
                                    TIT.codigoTitulo,TIT.tituloNombre,TIT.libro_codigoLibro,
                                    LIB.nombre AS libro_nombre,LIB.imagen AS libro_imagen FROM `video_subtitulo` AS VID_SUB INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=VID_SUB.subtitulo_codigosubtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo INNER JOIN `libro` AS LIB ON LIB.codigo=TIT.libro_codigoLibro WHERE VID_SUB.subtitulo_codigosubtitulo = :Codigo_subtitulo ORDER BY VID_SUB.codigovideo_subtitulo");
                                $stmt->bindValue(":Codigo_subtitulo", $row2['CONTADOR'], PDO::PARAM_STR);
                                $stmt->execute();
                                $datos3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($datos3 as $row3) {

                                    $insLibro = new Libro();
                                    $insLibro->setCodigo($row3['libro_codigoLibro']);
                                    $insLibro->setNombre($row3['libro_nombre']);
                                    $insLibro->setImagen($row3['libro_imagen']);

                                    $insCapitulo = new Titulo();
                                    $insCapitulo->setIdTitulo($row3['titulo_idtitulo']);
                                    $insCapitulo->setCodigo($row3['codigoTitulo']);
                                    $insCapitulo->setNombre($row3['tituloNombre']);
                                    //$insCapitulo->setImagen($row['titulo_imagen']);

                                    $insCapitulo->setLibro($insLibro->__toString());

                                    $insSubTitulo = new SubTitulo();
                                    //$insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                    $insSubTitulo->setCodigo($row3['subtitulo_codigosubtitulo']);
                                    $insSubTitulo->setPdf($row3['subtituloPDF']);
                                    $insSubTitulo->setNombre($row3['nombre']);
                                    $insSubTitulo->setImagen($row3['subtitulo_imagen']);

                                    $insSubTitulo->setTitulo($insCapitulo->__toString());

                                    $insVideoSubTitulo = new VideoSubTitulo();
                                    $insVideoSubTitulo->setIdVideoSubTitulo($row3['idvideo_subtitulo']);
                                    $insVideoSubTitulo->setCodigo($row3['codigovideo_subtitulo']);
                                    $insVideoSubTitulo->setNombre($row3['nombreVideo']);
                                    $insVideoSubTitulo->setImagen($row3['imagen']);
                                    $insVideoSubTitulo->setSubTitulo($insSubTitulo->__toString());
                                    $insBeanPagination->setList($insVideoSubTitulo->__toString());
                                }

                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "leccion-maximo":
                    $stmt = $conexion->prepare("SELECT count(idlecciones) AS CONTADOR FROM `lecciones`  WHERE cuenta_codigocuenta=:Cuenta");
                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT MAX(subtitulo_codigosubtitulo) AS subtitulo_codigosubtitulo FROM `lecciones` WHERE cuenta_codigocuenta=:Cuenta");
                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {
                                $stmt = $conexion->prepare("SELECT tipo_estado FROM `lecciones` WHERE cuenta_codigocuenta=:Cuenta and subtitulo_codigosubtitulo=:Subtitulo");
                                $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                                $stmt->bindValue(":Subtitulo", $row['subtitulo_codigosubtitulo'], PDO::PARAM_STR);
                                $stmt->execute();
                                $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($datos as $row2) {
                                    $insLeccion = new Leccion();
                                    $insLeccion->setSubTitulo($row['subtitulo_codigosubtitulo']);
                                    $insLeccion->setEstado($row2['tipo_estado']);
                                    $insBeanPagination->setList($insLeccion->__toString());
                                }
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "anteriorleccion":
                    $stmt = $conexion->prepare("SELECT count(idlecciones) AS CONTADOR FROM `lecciones` WHERE cuenta_codigocuenta=:Cuenta and subtitulo_codigosubtitulo=:Subtitulo");
                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(":Subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT VID_SUB.*,
                                SUB.nombre,SUB.titulo_idtitulo,SUB.subtituloPDF,SUB.subtitulo_imagen,
                                TIT.codigoTitulo,TIT.tituloNombre,TIT.libro_codigoLibro,
                                LIB.nombre AS libro_nombre,LIB.imagen AS libro_imagen FROM `video_subtitulo` AS VID_SUB INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=VID_SUB.subtitulo_codigosubtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo INNER JOIN `libro` AS LIB ON LIB.codigo=TIT.libro_codigoLibro WHERE VID_SUB.subtitulo_codigosubtitulo = :Codigo_subtitulo ORDER BY VID_SUB.codigovideo_subtitulo");
                            $stmt->bindValue(":Codigo_subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos3 as $row3) {

                                $insLibro = new Libro();
                                $insLibro->setCodigo($row3['libro_codigoLibro']);
                                $insLibro->setNombre($row3['libro_nombre']);
                                $insLibro->setImagen($row3['libro_imagen']);

                                $insCapitulo = new Titulo();
                                $insCapitulo->setIdTitulo($row3['titulo_idtitulo']);
                                $insCapitulo->setCodigo($row3['codigoTitulo']);
                                $insCapitulo->setNombre($row3['tituloNombre']);
                                //$insCapitulo->setImagen($row['titulo_imagen']);

                                $insCapitulo->setLibro($insLibro->__toString());

                                $insSubTitulo = new SubTitulo();
                                //$insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row3['subtitulo_codigosubtitulo']);
                                $insSubTitulo->setPdf($row3['subtituloPDF']);
                                $insSubTitulo->setNombre($row3['nombre']);
                                $insSubTitulo->setImagen($row3['subtitulo_imagen']);

                                $insSubTitulo->setTitulo($insCapitulo->__toString());

                                $insVideoSubTitulo = new VideoSubTitulo();
                                $insVideoSubTitulo->setIdVideoSubTitulo($row3['idvideo_subtitulo']);
                                $insVideoSubTitulo->setCodigo($row3['codigovideo_subtitulo']);
                                $insVideoSubTitulo->setNombre($row3['nombreVideo']);
                                $insVideoSubTitulo->setImagen($row3['imagen']);
                                $insVideoSubTitulo->setSubTitulo($insSubTitulo->__toString());
                                $insBeanPagination->setList($insVideoSubTitulo->__toString());
                            }

                        } else {
                            $stmt = $conexion->prepare("SELECT MAX(subtitulo_codigosubtitulo) AS subtitulo_codigosubtitulo FROM `lecciones` WHERE cuenta_codigocuenta=:Cuenta");
                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos2 as $row2) {

                                if ($row2['subtitulo_codigosubtitulo'] != null) {
                                    $arrayCodigo = explode(".", $row2['subtitulo_codigosubtitulo']);
                                    // aumentar subtitulo
                                    $numerofinal = ($arrayCodigo[3] + 1);
                                    if (strlen($numerofinal) == 1) {
                                        $numerofinal = "0" . $numerofinal;
                                    }
                                    if ($arrayCodigo[0] . "." . $arrayCodigo[1] . "." . $arrayCodigo[2] . "." . $numerofinal == $Leccion->getSubTitulo()) {
                                        $stmt = $conexion->prepare("SELECT VID_SUB.*, SUB.nombre,SUB.titulo_idtitulo,SUB.subtituloPDF,SUB.subtitulo_imagen,TIT.codigoTitulo,TIT.tituloNombre,TIT.libro_codigoLibro,LIB.nombre AS libro_nombre,LIB.imagen AS libro_imagen FROM `video_subtitulo` AS VID_SUB INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=VID_SUB.subtitulo_codigosubtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo INNER JOIN `libro` AS LIB ON LIB.codigo=TIT.libro_codigoLibro WHERE VID_SUB.subtitulo_codigosubtitulo = :Codigo_subtitulo ORDER BY VID_SUB.codigovideo_subtitulo");
                                        $stmt->bindValue(":Codigo_subtitulo", $arrayCodigo[0] . "." . $arrayCodigo[1] . "." . $arrayCodigo[2] . "." . $numerofinal, PDO::PARAM_STR);
                                        $stmt->execute();
                                        $datos3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($datos3 as $row3) {

                                            $insBeanPagination->setCountFilter(2);
                                            $insLibro = new Libro();
                                            $insLibro->setCodigo($row3['libro_codigoLibro']);
                                            $insLibro->setNombre($row3['libro_nombre']);
                                            $insLibro->setImagen($row3['libro_imagen']);

                                            $insCapitulo = new Titulo();
                                            $insCapitulo->setIdTitulo($row3['titulo_idtitulo']);
                                            $insCapitulo->setCodigo($row3['codigoTitulo']);
                                            $insCapitulo->setNombre($row3['tituloNombre']);
                                            //$insCapitulo->setImagen($row['titulo_imagen']);

                                            $insCapitulo->setLibro($insLibro->__toString());

                                            $insSubTitulo = new SubTitulo();
                                            //$insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                            $insSubTitulo->setCodigo($row3['subtitulo_codigosubtitulo']);
                                            $insSubTitulo->setPdf($row3['subtituloPDF']);
                                            $insSubTitulo->setNombre($row3['nombre']);
                                            $insSubTitulo->setImagen($row3['subtitulo_imagen']);

                                            $insSubTitulo->setTitulo($insCapitulo->__toString());

                                            $insVideoSubTitulo = new VideoSubTitulo();
                                            $insVideoSubTitulo->setIdVideoSubTitulo($row3['idvideo_subtitulo']);
                                            $insVideoSubTitulo->setCodigo($row3['codigovideo_subtitulo']);
                                            $insVideoSubTitulo->setNombre($row3['nombreVideo']);
                                            $insVideoSubTitulo->setImagen($row3['imagen']);
                                            $insVideoSubTitulo->setSubTitulo($insSubTitulo->__toString());
                                            $insBeanPagination->setList($insVideoSubTitulo->__toString());

                                        }
                                    } else {
                                        // aumentar titulo
                                        $numerofinal = ($arrayCodigo[2] + 1);
                                        if (strlen($numerofinal) == 1) {
                                            $numerofinal = "0" . $numerofinal;
                                        }
                                        if ($arrayCodigo[0] . "." . $arrayCodigo[1] . "." . $numerofinal . ".01" == $Leccion->getSubTitulo()) {
                                            $stmt = $conexion->prepare("SELECT VID_SUB.*, SUB.nombre,SUB.titulo_idtitulo,SUB.subtituloPDF,SUB.subtitulo_imagen, TIT.codigoTitulo,TIT.tituloNombre,TIT.libro_codigoLibro, LIB.nombre AS libro_nombre,LIB.imagen AS libro_imagen FROM  `video_subtitulo` AS VID_SUB INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=VID_SUB.subtitulo_codigosubtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo INNER JOIN `libro` AS LIB ON LIB.codigo=TIT.libro_codigoLibro WHERE VID_SUB.subtitulo_codigosubtitulo = :Codigo_subtitulo ORDER BY VID_SUB.codigovideo_subtitulo");
                                            $stmt->bindValue(":Codigo_subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                                            $stmt->execute();
                                            $datos5 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($datos5 as $row5) {

                                                $insBeanPagination->setCountFilter(2);
                                                $insLibro = new Libro();
                                                $insLibro->setCodigo($row5['libro_codigoLibro']);
                                                $insLibro->setNombre($row5['libro_nombre']);
                                                $insLibro->setImagen($row5['libro_imagen']);

                                                $insCapitulo = new Titulo();
                                                $insCapitulo->setIdTitulo($row5['titulo_idtitulo']);
                                                $insCapitulo->setCodigo($row5['codigoTitulo']);
                                                $insCapitulo->setNombre($row5['tituloNombre']);
                                                //$insCapitulo->setImagen($row['titulo_imagen']);

                                                $insCapitulo->setLibro($insLibro->__toString());

                                                $insSubTitulo = new SubTitulo();
                                                //$insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                                $insSubTitulo->setCodigo($row5['subtitulo_codigosubtitulo']);
                                                $insSubTitulo->setPdf($row5['subtituloPDF']);
                                                $insSubTitulo->setNombre($row5['nombre']);
                                                $insSubTitulo->setImagen($row5['subtitulo_imagen']);

                                                $insSubTitulo->setTitulo($insCapitulo->__toString());

                                                $insVideoSubTitulo = new VideoSubTitulo();
                                                $insVideoSubTitulo->setIdVideoSubTitulo($row5['idvideo_subtitulo']);
                                                $insVideoSubTitulo->setCodigo($row5['codigovideo_subtitulo']);
                                                $insVideoSubTitulo->setNombre($row5['nombreVideo']);
                                                $insVideoSubTitulo->setImagen($row5['imagen']);
                                                $insVideoSubTitulo->setSubTitulo($insSubTitulo->__toString());
                                                $insBeanPagination->setList($insVideoSubTitulo->__toString());

                                            }
                                        } else {
                                            $insBeanPagination->setCountFilter(0);
                                        }

                                    }

                                } else {
                                    $insBeanPagination->setCountFilter(0);
                                }

                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "reporte":

                    $stmt = $conexion->prepare("SELECT count(idlecciones) AS CONTADOR FROM `lecciones`  WHERE cuenta_codigocuenta=:Cuenta and subtitulo_codigosubtitulo=:Subtitulo ");
                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(":Subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT ate.*,lec.comentario,sub.nombre AS subtitulo_nombre ,tit.tituloNombre AS titulo_nombre, lib.nombre AS libro_nombre,cuen.email,cuen.foto FROM `administrador` AS ate INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo = ate.Cuenta_Codigo INNER JOIN `lecciones` AS lec ON lec.cuenta_codigocuenta = ate.Cuenta_Codigo INNER JOIN `subtitulo` AS sub ON sub.codigo_subtitulo = lec.subtitulo_codigosubtitulo INNER JOIN `titulo` AS tit ON tit.idtitulo = sub.titulo_idtitulo INNER JOIN `libro` AS lib ON lib.codigo = tit.libro_codigoLibro WHERE lec.cuenta_codigocuenta=:Cuenta and lec.subtitulo_codigosubtitulo=:Subtitulo");
                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->bindValue(":Subtitulo", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {

                                $insLeccion = new Leccion();
                                $insLeccion->setComentario($row['comentario']);
                                $insLeccion->setCuenta(array(
                                    "nombre_completo" => $row['AdminNombre'] . " " . $row['AdminApellido'],
                                    "telefono" => $row['AdminTelefono'],
                                    "ocupacion" => $row['AdminOcupacion'],
                                    "email" => $row['email'],
                                    "foto" => $row['foto'],
                                ));
                                $insLeccion->setSubTitulo(array(
                                    "libro" => $row['libro_nombre'],
                                    "titulo" => $row['titulo_nombre'],
                                    "subTitulo" => $row['subtitulo_nombre'],
                                ));
                                $insBeanPagination->setList($insLeccion->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "reporte-certificado":

                    $stmt = $conexion->prepare("SELECT count(idcertificado) AS CONTADOR FROM `certificado`  WHERE cuenta=:Cuenta ");
                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `certificado` WHERE cuenta=:Cuenta ");
                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {
                                $insLeccion = new Leccion();
                                $insLeccion->setCuenta(array(
                                    "nombre_completo" => $row['nombre'],
                                    "fecha_inicial" => $row['fecha_inicial'],
                                ));
                                $insLeccion->setFecha($row['fecha']);

                                $insBeanPagination->setList($insLeccion->__toString());
                            }
                        }
                    }

                    $stmt->closeCursor();
                    $stmt = null;
                    break;

                case "subtitulo-titulo":

                    $stmt = $conexion->prepare("SELECT COUNT(idsubtitulo) AS CONTADOR FROM `subtitulo`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subtitulo` AS sub INNER JOIN `titulo` AS tit ON tit.idtitulo = sub.titulo_idtitulo ORDER BY sub.codigo_subtitulo");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insTitulo = new Titulo();
                                $insTitulo->setIdTitulo($row['idtitulo']);
                                $insTitulo->setCodigo($row['codigoTitulo']);
                                $insTitulo->setNombre($row['tituloNombre']);

                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setPdf($row['subtituloPDF']);
                                //$insSubTitulo->setDescripcion($row['descripcion']);
                                $insSubTitulo->setNombre($row['nombre']);
                                //$insSubTitulo->setImagen($row['subtitulo_imagen']);
                                $insSubTitulo->setTitulo($insTitulo->__toString());
                                $insBeanPagination->setList($insSubTitulo->__toString());
                            }
                        }
                    }
                    //CUESTIONARIO
                    $stmt = $conexion->prepare("SELECT count(idtest) AS CONTADOR FROM `test` ");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `test`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {
                                if ($row['tipo'] == 1) {
                                    $insBeanPagination->setList(array("nombre" => $row['nombre'],
                                        "estado" => $row['tipo'],
                                        "titulo" => "",
                                        "pdf" => "",
                                        "idsubTitulo" => "",
                                        "codigo" => $row['codigotitulo'] . '.40',
                                        "descripcion" => "",
                                        "imagen" => "",

                                    ));

                                } else {
                                    $insBeanPagination->setList(array("nombre" => $row['nombre'],
                                        "estado" => $row['tipo'],
                                        "titulo" => "",
                                        "pdf" => "",
                                        "idsubTitulo" => "",
                                        "codigo" => $row['subtitulo_codigo_test'],
                                        "descripcion" => "",
                                        "imagen" => "",

                                    ));

                                }

                            }
                        }
                    }
                    //RECURSO
                    $stmt = $conexion->prepare("SELECT count(idrecurso) AS CONTADOR FROM `recurso` ");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `recurso`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {
                                $insBeanPagination->setList(array("nombre" => $row['nombre'],
                                    "disponible" => $row['disponible'],
                                    "codigo" => $row['codigo_subtitulo'],
                                ));

                            }
                        }
                    }
                    break;
                case "excel-lecciones":
                    $stmt = $conexion->prepare("SELECT COUNT(idtarea) AS CONTADOR FROM `tarea` WHERE tipo=0");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    $contador = 0;
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $contador++;
                            $stmt = $conexion->prepare("SELECT
                            sub.codigo_subtitulo,
                            sub.nombre,
                            lec.comentario,
                            tar.fecha,
                            tar.estado,
                            cli.AdminNombre,
                            cli.AdminApellido,
                            cli.AdminOcupacion,
                            cli.AdminTelefono,
                            cli.pais
                        FROM
                            `lecciones` AS lec
                        INNER JOIN `tarea` AS tar ON
                            tar.codigo_subtitulo = lec.subtitulo_codigosubtitulo
                            INNER JOIN `administrador` AS cli ON
                            cli.Cuenta_Codigo = lec.cuenta_codigocuenta
                            INNER JOIN `subtitulo` AS sub ON
                            sub.codigo_subtitulo = lec.subtitulo_codigosubtitulo
                        WHERE
                            tar.tipo = 0
                        ORDER BY
                            lec.subtitulo_codigosubtitulo");

                            $stmt->execute();
                            //   print_r($Leccion);
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($datos as $row) {
                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setNombre($row['nombre']);

                                $insLeccion = new Leccion();
                                $insLeccion->setComentario($row['comentario']);
                                $insLeccion->setEstado(array(
                                    "estadotarea" => $row['estado'],
                                    "nombre" => $row['AdminNombre'],
                                    "apellido" => $row['AdminApellido'],
                                    "ocupacion" => $row['AdminOcupacion'],
                                    "telefono" => $row['AdminTelefono'],
                                    "pais" => $row['pais']));
                                $insLeccion->setFecha($row['fecha']);
                                $insLeccion->setSubTitulo($insSubTitulo->__toString());
                                $insBeanPagination->setList($insLeccion->__toString());
                            }

                        }
                    }
                    $insBeanPagination->setCountFilter($contador);
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
    protected function eliminar_lecciones_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `lecciones` WHERE
        idlecciones=:IDtitulo");
        $sql->bindValue(":IDtitulo", $codigo);
        return $sql;
    }
    protected function eliminar_video_lecciones_modelo($conexion, $codigo)
    {

        $sql = $conexion->prepare("UPDATE `lecciones` SET video=:Video WHERE idlecciones=:ID");
        $sql->bindValue(":Video", null);
        $sql->bindValue(":ID", $codigo, PDO::PARAM_INT);

        return $sql;
    }

}
