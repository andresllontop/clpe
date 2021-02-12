<?php

require_once './core/mainModel.php';

class respuestaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_respuesta_modelo($conexion, $Respuesta)
    {
        $sql = $conexion->prepare("INSERT INTO `respuesta`(fecha,estado,idtest,codigo_cuenta,tipo,respuesta_codigo,tipo_estado) VALUES(?,?,?,?,?,?,0)");
        $sql->bindValue(1, $Respuesta->getFecha(), PDO::PARAM_STR);
        $sql->bindValue(2, $Respuesta->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(3, $Respuesta->getTest(), PDO::PARAM_INT);
        $sql->bindValue(4, $Respuesta->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(5, $Respuesta->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(6, $Respuesta->getTitulo(), PDO::PARAM_STR);

        return $sql;

    }

    protected function datos_respuesta_modelo($conexion, $tipo, $respuesta)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE idrespuesta=:IDrespuesta");
                    $stmt->bindValue(":IDrespuesta", $respuesta->getIdRespuesta(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `respuesta`
                            WHERE idrespuesta=:IDrespuesta");
                            $stmt->bindValue(":IDrespuesta", $respuesta->getIdRespuesta(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insRespuesta = new Respuesta();
                                $insRespuesta->setIdRespuesta($row['idrespuesta']);
                                $insRespuesta->setTitulo($row['respuesta_codigo']);
                                $insRespuesta->setEstado($row['estado']);
                                $insRespuesta->setTipo($row['tipo']);
                                $insRespuesta->setFecha($row['fecha']);
                                $insRespuesta->setCuenta($row['codigo_cuenta']);
                                $insRespuesta->setTest($row['idtest']);
                                $insRespuesta->getCuenta($row['codigo_cuenta']);
                                $insBeanPagination->setList($insRespuesta->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo-subtitulo":
                    $pagina = mainModel::limpiar_cadena($respuesta->getPagina());
                    $registros = mainModel::limpiar_cadena($respuesta->getRegistro());
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
                    $stmt = $conexion->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and tipo=2");
                    $stmt->bindValue(":Cuenta", $respuesta->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT tar.estado,tar.idtarea,cuest.nombre as cuestionario,cuest.cantidad_preguntas,sub.nombre,sub.codigo_subtitulo,res.fecha,res.tipo,res.idtest,res.idrespuesta FROM `respuesta` AS res INNER JOIN `subtitulo` AS sub ON sub.codigo_subtitulo=res.respuesta_codigo inner join `tarea` as tar on tar.fecha = res.fecha inner join `test` as cuest on cuest.subtitulo_codigo_test = res.respuesta_codigo WHERE res.codigo_cuenta=:Cuenta and res.tipo=2 and tar.tipo=2 ORDER BY res.respuesta_codigo ASC LIMIT :inicio,:regi");
                            $stmt->bindValue(":inicio", $inicio, PDO::PARAM_INT);
                            $stmt->bindValue(":regi", $registros, PDO::PARAM_INT);
                            $stmt->bindValue(":Cuenta", $respuesta->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setNombre($row['nombre']);

                                $insRespuesta = new Respuesta();
                                $insRespuesta->setIdRespuesta($row['idrespuesta']);
                                $insRespuesta->setFecha($row['fecha']);
                                $insRespuesta->setTipo($row['tipo']);
                                $insRespuesta->setTest(array("idtest" => $row['idtest'],
                                    "cantidadpreguntas" => $row['cantidad_preguntas'], "nombre" => $row['cuestionario']));
                                $insRespuesta->setEstado(array("estadotarea" => $row['estado'],
                                    "idtarea" => $row['idtarea']));
                                $insRespuesta->setTitulo($insSubTitulo->__toString());
                                $insBeanPagination->setList($insRespuesta->__toString());
                            }

                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo-titulo":
                    $pagina = mainModel::limpiar_cadena($respuesta->getPagina());
                    $registros = mainModel::limpiar_cadena($respuesta->getRegistro());
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
                    $stmt = $conexion->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and tipo=1");
                    $stmt->bindValue(":Cuenta", $respuesta->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT tar.estado,tar.idtarea,cuest.nombre as cuestionario,cuest.cantidad_preguntas,sub.tituloNombre,sub.codigoTitulo,res.fecha,res.tipo,res.idtest,res.idrespuesta FROM `respuesta` AS res INNER JOIN `titulo` AS sub ON sub.codigoTitulo=res.respuesta_codigo inner join `test` as cuest on cuest.codigotitulo = res.respuesta_codigo inner join `tarea` as tar on tar.fecha = res.fecha WHERE res.codigo_cuenta=:Cuenta and res.tipo=1 and cuest.tipo=1 and tar.tipo=1  ORDER BY res.respuesta_codigo ASC LIMIT :inicio,:regi");
                            $stmt->bindValue(":inicio", $inicio, PDO::PARAM_INT);
                            $stmt->bindValue(":regi", $registros, PDO::PARAM_INT);
                            $stmt->bindValue(":Cuenta", $respuesta->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insTitulo = new Titulo();
                                $insTitulo->setCodigo($row['codigoTitulo']);
                                $insTitulo->setNombre($row['tituloNombre']);

                                $insRespuesta = new Respuesta();
                                $insRespuesta->setIdRespuesta($row['idrespuesta']);
                                $insRespuesta->setFecha($row['fecha']);
                                $insRespuesta->setTipo($row['tipo']);
                                $insRespuesta->setTest(array("idtest" => $row['idtest'],
                                    "cantidadpreguntas" => $row['cantidad_preguntas'], "nombre" => $row['cuestionario']));
                                $insRespuesta->setEstado(array("estadotarea" => $row['estado'],
                                    "idtarea" => $row['idtarea']));
                                $insRespuesta->setTitulo($insTitulo->__toString());
                                $insBeanPagination->setList($insRespuesta->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case 'reporte':
                    $stmt = $conexion->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and tipo=:Tipo and respuesta_codigo=:Codigo");
                    $stmt->bindValue(":Cuenta", $respuesta->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(":Tipo", $respuesta->getTipo(), PDO::PARAM_INT);
                    $stmt->bindValue(":Codigo", $respuesta->getTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT  det.pregunta_descripcion,det.descripcion AS det_descripcion ,sub.nombre,sub.codigo_subtitulo,res.fecha,res.tipo FROM `respuesta` AS res INNER JOIN `detalle_respuesta` as det ON det.idrespuesta=res.idrespuesta INNER JOIN `subtitulo` AS sub ON sub.codigo_subtitulo=det.codigosubtitulo WHERE res.codigo_cuenta=:Cuenta  and res.tipo=:Tipo and res.respuesta_codigo=:Codigo ORDER BY det.codigosubtitulo ASC");
                            $stmt->bindValue(":Cuenta", $respuesta->getCuenta(), PDO::PARAM_STR);
                            $stmt->bindValue(":Tipo", $respuesta->getTipo(), PDO::PARAM_INT);
                            $stmt->bindValue(":Codigo", $respuesta->getTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insRespuesta = new Respuesta();
                                $insRespuesta->setFecha($row['fecha']);
                                $insRespuesta->setTipo($row['tipo']);

                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setNombre($row['nombre']);

                                $insDetalleTest = new DetalleTest();
                                $insDetalleTest->setDescripcion($row['pregunta_descripcion']);

                                $insDetalleRespuesta = new DetalleRespuesta();
                                $insDetalleRespuesta->setDescripcion($row['det_descripcion']);
                                $insDetalleRespuesta->setSubtitulo($insSubTitulo->__toString());
                                $insDetalleRespuesta->setRespuesta($insRespuesta->__toString());
                                $insDetalleRespuesta->setTest($insDetalleTest->__toString());

                                $insBeanPagination->setList($insDetalleRespuesta->__toString());
                            }
                        }
                    }

                    $stmt->closeCursor(); // this is not even required
                    $stmt = null;
                    break;
                case "reporte-subtitulo":

                    $stmt = $conexion->prepare("SELECT ate.*,sub.nombre AS subtitulo_nombre ,tit.tituloNombre AS titulo_nombre, lib.nombre AS libro_nombre,cuen.email,cuen.foto FROM `administrador` AS ate INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo = ate.Cuenta_Codigo INNER JOIN `respuesta` AS lec ON lec.codigo_cuenta = ate.Cuenta_Codigo INNER JOIN `subtitulo` AS sub ON sub.codigo_subtitulo = lec.respuesta_codigo INNER JOIN `titulo` AS tit ON tit.idtitulo = sub.titulo_idtitulo INNER JOIN `libro` AS lib ON lib.codigo = tit.libro_codigoLibro WHERE lec.codigo_cuenta=:Cuenta and lec.respuesta_codigo=:Subtitulo");
                    $stmt->bindValue(":Cuenta", $respuesta->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(":Subtitulo", $respuesta->getTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($datos as $row) {

                        $insRespuesta = new Respuesta();
                        $insRespuesta->setCuenta(array(
                            "nombre_completo" => $row['AdminNombre'] . " " . $row['AdminApellido'],
                            "telefono" => $row['AdminTelefono'],
                            "ocupacion" => $row['AdminOcupacion'],
                            "email" => $row['email'],
                            "foto" => $row['foto'],
                        ));
                        $insRespuesta->setTitulo(array(
                            "libro" => $row['libro_nombre'],
                            "titulo" => $row['titulo_nombre'],
                            "subTitulo" => $row['subtitulo_nombre'],
                        ));
                        $insBeanPagination->setList($insRespuesta->__toString());
                    }

                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "reporte-titulo":
                    $stmt = $conexion->prepare("SELECT ate.*,tit.tituloNombre AS titulo_nombre, lib.nombre AS libro_nombre,cuen.email,cuen.foto FROM `administrador` AS ate INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo = ate.Cuenta_Codigo INNER JOIN `respuesta` AS lec ON lec.codigo_cuenta = ate.Cuenta_Codigo INNER JOIN `titulo` AS tit ON tit.codigoTitulo = lec.respuesta_codigo INNER JOIN `libro` AS lib ON lib.codigo = tit.libro_codigoLibro WHERE lec.codigo_cuenta=:Cuenta and lec.respuesta_codigo=:Subtitulo");
                    $stmt->bindValue(":Cuenta", $respuesta->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(":Subtitulo", $respuesta->getTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($datos as $row) {

                        $insRespuesta = new Respuesta();
                        $insRespuesta->setCuenta(array(
                            "nombre_completo" => $row['AdminNombre'] . " " . $row['AdminApellido'],
                            "telefono" => $row['AdminTelefono'],
                            "ocupacion" => $row['AdminOcupacion'],
                            "email" => $row['email'],
                            "foto" => $row['foto'],
                        ));
                        $insRespuesta->setTitulo(array(
                            "libro" => $row['libro_nombre'],
                            "titulo" => $row['titulo_nombre'],
                            "subTitulo" => "",
                        ));
                        $insBeanPagination->setList($insRespuesta->__toString());
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
    protected function eliminar_respuesta_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `respuesta` WHERE idrespuesta=:Codigo ");
        $sql->bindValue(":Codigo", $codigo);
        return $sql;
    }
    protected function actualizar_respuesta_modelo($conexion, $Respuesta)
    {
        $sql = $conexion->prepare("UPDATE `respuesta` SET respuestaEstado=? WHERE cuenta_codigoCuenta=? and idrespuesta=?");
        // $sql->bindParam(1, $Respuesta->getTitulo(), PDO::PARAM_STR);
        // $sql->bindParam(2, $Respuesta->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(1, $Respuesta->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(2, $Respuesta->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(3, $Respuesta->getIdRespuesta(), PDO::PARAM_INT);
        return $sql;

    }
    protected function actualizar_respuesta_tarea_modelo($conexion, $Respuesta)
    {
        $sql = $conexion->prepare("UPDATE `tarea` SET estado=? WHERE idtarea=?");
        $sql->bindValue(1, $Respuesta->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(2, $Respuesta->getCuenta(), PDO::PARAM_INT);
        return $sql;

    }
}
