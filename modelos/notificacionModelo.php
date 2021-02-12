<?php

require_once './core/mainModel.php';

class notificacionModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_notificacion_modelo($conexion, $Notificacion)
    {
        $sql = $conexion->prepare("INSERT INTO `notificacion`
        (descripcion,rango_inicial,rango_final,tipo)
         VALUES(?,?,?,?)");
        $sql->bindValue(1, $Notificacion->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(2, $Notificacion->getRangoInicial(), PDO::PARAM_INT);
        $sql->bindValue(3, $Notificacion->getRangoFinal(), PDO::PARAM_INT);
        $sql->bindValue(4, $Notificacion->getTipo(), PDO::PARAM_INT);

        return $sql;

    }
    protected function datos_notificacion_modelo($conexion, $tipo, $notificacion)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idnotificacion) AS CONTADOR FROM `notificacion` WHERE  idnotificacion=:IDnotificacion ");
                    $stmt->bindValue(":IDnotificacion", $notificacion->getIdNotificacion(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `notificacion` WHERE idnotificacion=:IDnotificacion");
                            $stmt->bindValue(":IDnotificacion", $notificacion->getIdNotificacion(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insNotificacion = new Notificacion();
                                $insNotificacion->setIdNotificacion($row['idnotificacion']);
                                $insNotificacion->setRangoInicial($row['rango_inicial']);
                                $insNotificacion->setRangoFinal($row['rango_final']);
                                $insNotificacion->setDescripcion($row['descripcion']);
                                $insNotificacion->setTipo($row['tipo']);
                                $insBeanPagination->setList($insNotificacion->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idnotificacion) AS CONTADOR FROM `notificacion`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `notificacion`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insNotificacion = new Notificacion();
                                $insNotificacion->setIdNotificacion($row['idnotificacion']);
                                $insNotificacion->setRangoInicial($row['rango_inicial']);
                                $insNotificacion->setRangoFinal($row['rango_final']);
                                $insNotificacion->setDescripcion($row['descripcion']);
                                $insNotificacion->setTipo($row['tipo']);
                                $insBeanPagination->setList($insNotificacion->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "empresa":
                    $stmt = $conexion->prepare("SELECT COUNT(idempresa) AS CONTADOR FROM `empresa`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `empresa`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insEmpresa = new Empresa();
                                $insEmpresa->setIdEmpresa($row['idempresa']);
                                $insEmpresa->setTelefono($row['EmpresaTelefono']);
                                $insEmpresa->setVision($row['vision']);
                                $insEmpresa->setYoutube($row['youtube']);
                                $insEmpresa->setNombre($row['EmpresaNombre']);
                                $insEmpresa->setMision($row['mision']);
                                $insEmpresa->setEmail($row['EmpresaEmail']);
                                $insEmpresa->setDescripcion($row['descripcion']);
                                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                                $insEmpresa->setLogo($row['EmpresaLogo']);
                                $insEmpresa->setEnlace($row['Enlace']);
                                $insEmpresa->setTelefonoSegundo($row['EmpresaTelefono2']);
                                $insEmpresa->setFacebook($row['facebook']);
                                $insEmpresa->setPrecio($row['precio']);

                                $insBeanPagination->setList($insEmpresa->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "tarea":
                    //
                    $hoy = date('Y-m-d H:i:s');
                    $stmt = $conexion->prepare("SELECT *,MIN(fecha) FROM `notificacion` WHERE fecha >= ? and tipo=2");
                    $stmt->bindValue(1, $hoy);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        if ($row["idnotificacion"] != null) {
                            $insNotificacion = new Notificacion();
                            $insNotificacion->setIdNotificacion($row['idnotificacion']);
                            $insNotificacion->setFecha($row['fecha']);
                            $insNotificacion->setDescripcion($row['descripcion']);
                            $insNotificacion->setTipo($row['tipo']);
                            $insNotificacion->setRangoInicial($row['rango_inicial']);
                            $insNotificacion->setRangoFinal($row['rango_final']);
                            $insBeanPagination->setList($insNotificacion->__toString());
                        }

                    }
                    //
                    $stmt = $conexion->prepare("SELECT COUNT(idtarea) AS CONTADOR FROM `tarea` WHERE  cuenta=:CuentaCodigo");
                    $stmt->bindValue(":CuentaCodigo", $notificacion->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {

                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT MAX(idtarea),DATE(fecha) AS fecha FROM `tarea` WHERE  cuenta=:CuentaCodigo");
                            $stmt->bindValue(":CuentaCodigo", $notificacion->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                if ($row['fecha'] != null) {
                                    $contadorFecha = date_diff(date_create($row['fecha']), date_create(date('Y-m-d')));
                                    $valorRango = $contadorFecha->format('%a');
                                    $stmt = $conexion->prepare("SELECT COUNT(idnotificacion) AS CONTADOR FROM `notificacion` WHERE ( ? BETWEEN rango_inicial AND rango_final ) and tipo=1");
                                    $stmt->bindValue(1, $valorRango, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $datos2 = $stmt->fetchAll();
                                    foreach ($datos2 as $row2) {
                                        $insBeanPagination->setCountFilter($row2['CONTADOR']);
                                        if ($row2['CONTADOR'] > 0) {
                                            $stmt = $conexion->prepare("SELECT * FROM `notificacion` WHERE (? BETWEEN rango_inicial AND rango_final) and tipo=1");
                                            $stmt->bindValue(1, $valorRango, PDO::PARAM_INT);
                                            $stmt->execute();
                                            $datos = $stmt->fetchAll();
                                            foreach ($datos as $row) {
                                                $insNotificacion = new Notificacion();
                                                $insNotificacion->setIdNotificacion($row['idnotificacion']);
                                                $insNotificacion->setRangoInicial($row['rango_inicial']);
                                                $insNotificacion->setRangoFinal($row['rango_final']);
                                                $insNotificacion->setDescripcion($row['descripcion']);
                                                $insNotificacion->setTipo($row['tipo']);
                                                $insBeanPagination->setList($insNotificacion->__toString());
                                            }

                                        }
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
    protected function eliminar_notificacion_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `notificacion` WHERE idnotificacion=:Codigo ");
        $sql->bindValue(":Codigo", $codigo);
        return $sql;
    }
    protected function actualizar_notificacion_modelo($conexion, $Notificacion)
    {
        $sql = $conexion->prepare("UPDATE `notificacion` SET descripcion=?, rango_inicial=?, rango_final=?, tipo=?  WHERE idnotificacion=?");
        $sql->bindValue(1, $Notificacion->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(2, $Notificacion->getRangoInicial(), PDO::PARAM_INT);
        $sql->bindValue(3, $Notificacion->getRangoFinal(), PDO::PARAM_INT);
        $sql->bindValue(4, $Notificacion->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(5, $Notificacion->getIdNotificacion(), PDO::PARAM_INT);
        return $sql;

    }
}
