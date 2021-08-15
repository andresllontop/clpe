<?php

require_once './core/mainModel.php';

class tareaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function datos_tarea_modelo($conexion, $tipo, $Leccion)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo` WHERE idvideo_subtitulo=:IDlibro");
                    $stmt->bindValue(":IDlibro", $Leccion->getIdLeccion(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `video_subtitulo` WHERE idvideo_subtitulo=:IDlibro");
                            $stmt->bindValue(":IDlibro", $Leccion->getIdLeccion(), PDO::PARAM_INT);
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
                case "conteo":
                    $pagina = mainModel::limpiar_cadena($Leccion->getPagina());
                    $registros = mainModel::limpiar_cadena($Leccion->getRegistro());
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
                    $stmt = $conexion->prepare("SELECT COUNT(idtarea) AS CONTADOR  FROM `tarea` WHERE cuenta=? and tipo>0");
                    $stmt->bindValue(1, $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT sub.*, lec.idtarea,lec.tipo,lec.fecha,tit.tituloNombre,tit.codigoTitulo FROM `tarea` as lec inner join `subtitulo` as sub ON sub.codigo_subtitulo=lec.codigo_subtitulo inner join `titulo` as tit ON tit.idtitulo=sub.titulo_idtitulo WHERE lec.cuenta=? and lec.tipo>0 ORDER BY lec.codigo_subtitulo ASC LIMIT ?,?");
                            $stmt->bindValue(1, $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                            $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();

                            foreach ($datos as $row) {
                                $insTitulo = new Titulo();
                                $insTitulo->setNombre($row['tituloNombre']);
                                $insTitulo->setCodigo($row['codigoTitulo']);

                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setPdf($row['subtituloPDF']);
                                $insSubTitulo->setNombre($row['nombre']);
                                $insSubTitulo->setDescripcion($row['descripcion']);
                                $insSubTitulo->setImagen($row['subtitulo_imagen']);
                                $insSubTitulo->setTitulo($insTitulo->__toString());

                                $insTarea = new Tarea();
                                $insTarea->setIdtarea($row['idtarea']);
                                $insTarea->setTipo($row['tipo']);
                                $insTarea->setFecha($row['fecha']);

                                $insTarea->setSubTitulo($insSubTitulo->__toString());
                                $insBeanPagination->setList($insTarea->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;

                case "conteo-alumno":
                    $pagina = mainModel::limpiar_cadena($Leccion->getPagina());
                    $registros = mainModel::limpiar_cadena($Leccion->getRegistro());
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
                    $stmt = $conexion->prepare("SELECT COUNT(tar.idtarea) AS CONTADOR  FROM `tarea` AS tar INNER JOIN `administrador` AS admmini ON tar.cuenta=admmini.Cuenta_Codigo left join `certificado` as cer ON cer.cuenta=admmini.Cuenta_Codigo WHERE cer.idcertificado is null and tar.tipo=0  and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') ) GROUP BY tar.cuenta");
                    $stmt->bindValue(1, $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    $insBeanPagination->setCountFilter(count($datos));
                    if (count($datos) > 0) {
                        $stmt = $conexion->prepare("SELECT sum(CASE WHEN lec.estado = 0 THEN 1 ELSE 0 end) as totalestado,sum(lec.estado) as totalnoestado, sub.*,admmini.AdminApellido as apellido,admmini.AdminNombre as nombre_alumno, lec.idtarea,lec.tipo,lec.fecha,tit.tituloNombre,tit.codigoTitulo FROM `tarea` as lec INNER JOIN `administrador` AS admmini ON lec.cuenta=admmini.Cuenta_Codigo inner join `subtitulo` as sub ON sub.codigo_subtitulo=lec.codigo_subtitulo inner join `titulo` as tit ON tit.idtitulo=sub.titulo_idtitulo WHERE (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') ) and lec.tipo=0 GROUP BY lec.cuenta ORDER BY max(lec.fecha) ASC LIMIT ?,?");
                        $stmt->bindValue(1, $Leccion->getCuenta(), PDO::PARAM_STR);
                        $stmt->bindValue(2, $Leccion->getCuenta(), PDO::PARAM_STR);
                        $stmt->bindValue(3, $inicio, PDO::PARAM_INT);
                        $stmt->bindValue(4, $registros, PDO::PARAM_INT);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();

                        foreach ($datos as $row) {
                            $insTitulo = new Titulo();
                            $insTitulo->setNombre($row['tituloNombre']);
                            $insTitulo->setCodigo($row['codigoTitulo']);

                            $insSubTitulo = new SubTitulo();
                            $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                            $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                            $insSubTitulo->setNombre($row['nombre']);
                            $insSubTitulo->setTitulo($insTitulo->__toString());

                            $insTarea = new Tarea();
                            $insTarea->setIdtarea($row['idtarea']);
                            $insTarea->setTipo($row['tipo']);
                            $insTarea->setFecha($row['fecha']);
                            $insTarea->setCuenta($row['nombre_alumno']);
                            $insTarea->setPagina($row['apellido']);
                            $insTarea->setRegistro(array("totalestado" => $row['totalestado'],
                                "totalnoestado" => $row['totalnoestado'],
                            ));
                            $insTarea->setSubTitulo($insSubTitulo->__toString());
                            $insBeanPagination->setList($insTarea->__toString());
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
                            $stmt = $conexion->prepare("SELECT VID_SUB.*,
                                SUB.nombre,SUB.titulo_idtitulo,SUB.subtituloPDF,SUB.subtitulo_imagen,
                                TIT.codigoTitulo,TIT.tituloNombre,TIT.libro_codigoLibro,
                                LIB.nombre AS libro_nombre,LIB.imagen AS libro_imagen FROM `video_subtitulo` AS VID_SUB INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=VID_SUB.subtitulo_codigosubtitulo INNER JOIN `titulo` AS TIT ON TIT.idtitulo=SUB.titulo_idtitulo INNER JOIN `libro` AS LIB ON LIB.codigo=TIT.libro_codigoLibro WHERE VID_SUB.subtitulo_codigosubtitulo = :Codigo_subtitulo ORDER BY VID_SUB.codigovideo_subtitulo");
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
                case "leccion-maximo":
                    $stmt = $conexion->prepare("SELECT count(idtarea) AS CONTADOR FROM `tarea`  WHERE cuenta_codigocuenta=:Cuenta");
                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT MAX(subtitulo_codigosubtitulo) AS subtitulo_codigosubtitulo FROM `tarea`  WHERE cuenta_codigocuenta = :Cuenta");
                            $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {
                                $insLeccion = new Leccion();
                                $insLeccion->setSubTitulo($row['subtitulo_codigosubtitulo']);
                                $insBeanPagination->setList($insLeccion->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "titulo":
                    $pagina = mainModel::limpiar_cadena($Leccion->getPagina());
                    $registros = mainModel::limpiar_cadena($Leccion->getRegistro());
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
                    $stmt = $conexion->prepare("SELECT count(idtarea) AS CONTADOR FROM `tarea`  WHERE cuenta=:Cuenta AND codigo_subtitulo LIKE CONCAT('%',:Code,'%')");
                    $stmt->bindValue(":Cuenta", $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(":Code", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        $contador = 0;
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT tar.*,ti.codigoTitulo,ti.tituloNombre,ti.titulo_imagen  FROM `tarea`as tar inner join `subtitulo` as sub on sub.codigo_subtitulo=tar.codigo_subtitulo inner join `titulo` as ti on ti.idtitulo=sub.titulo_idtitulo  WHERE tar.cuenta=? AND tar.codigo_subtitulo LIKE CONCAT('%',?,'%') group by sub.titulo_idtitulo ASC LIMIT ?,?");
                            $stmt->bindValue(1, $Leccion->getCuenta(), PDO::PARAM_STR);
                            $stmt->bindValue(2, $Leccion->getSubTitulo(), PDO::PARAM_STR);
                            $stmt->bindValue(3, $inicio, PDO::PARAM_INT);
                            $stmt->bindValue(4, $registros, PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row) {
                                $contador++;
                                $insTitulo = new Titulo();
                                $insTitulo->setNombre($row['tituloNombre']);
                                $insTitulo->setCodigo($row['codigoTitulo']);
                                $insTitulo->setImagen($row['titulo_imagen']);

                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setTitulo($insTitulo->__toString());
                                $insTarea = new Tarea();
                                $insTarea->setIdtarea($row['idtarea']);
                                $insTarea->setTipo($row['tipo']);
                                $insTarea->setFecha($row['fecha']);

                                $insTarea->setSubTitulo($insSubTitulo->__toString());
                                $insBeanPagination->setList($insTarea->__toString());
                            }
                            $insBeanPagination->setCountFilter($contador);
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "tarea-cantidad":

                    $stmt = $conexion->prepare("SELECT COUNT(idtarea) AS CONTADOR  FROM `tarea` WHERE cuenta=:CuentaCodigo and tipo=0 AND codigo_subtitulo LIKE CONCAT('%',:Code,'%')");
                    $stmt->bindValue(":CuentaCodigo", $Leccion->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(":Code", $Leccion->getSubTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);

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
    protected function eliminar_tarea_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM tarea WHERE
        idtitulo=:IDtitulo ");
        $sql->bindValue(":IDtitulo", $codigo);
        return $sql;
    }

}
