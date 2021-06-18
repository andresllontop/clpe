<?php

require_once './core/mainModel.php';

class mensajeModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_mensaje_modelo($conexion, $Mensaje)
    {
        $sql = $conexion->prepare("INSERT INTO `mensaje`
        (titulo,descripcion,mensajeEstado,cuenta_codigoCuenta)
         VALUES(?,?,?,?)");
        $sql->bindValue(1, $Mensaje->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(2, $Mensaje->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(3, $Mensaje->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(4, $Mensaje->getCuenta(), PDO::PARAM_STR);

        return $sql;

    }
    protected function datos_mensaje_modelo($conexion, $tipo, $mensaje)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idmensaje) AS CONTADOR FROM `mensaje` WHERE idmensaje=:IDmensaje");
                    $stmt->bindValue(":IDmensaje", $mensaje->getIdMensaje(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `mensaje`
                            WHERE idmensaje=:IDmensaje");
                            $stmt->bindValue(":IDmensaje", $mensaje->getIdMensaje(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insMensaje = new Mensaje();
                                $insMensaje->setIdMensaje($row['idmensaje']);
                                $insMensaje->setTitulo($row['titulo']);
                                $insMensaje->setEstado($row['mensajeEstado']);
                                $insMensaje->setDescripcion($row['descripcion']);
                                $insMensaje->getCuenta($row['cuenta_codigoCuenta']);
                                $insBeanPagination->setList($insMensaje->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idmensaje) AS CONTADOR FROM `mensaje`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `mensaje`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insMensaje = new Mensaje();
                                $insMensaje->setIdMensaje($row['idmensaje']);
                                $insMensaje->setTitulo($row['titulo']);
                                $insMensaje->setEstado($row['mensajeEstado']);
                                $insMensaje->setDescripcion($row['descripcion']);
                                $insMensaje->getCuenta($row['cuenta_codigoCuenta']);
                                $insBeanPagination->setList($insMensaje->__toString());
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
                case "home":
                    $stmt = $conexion->prepare("SELECT COUNT(idmensaje) AS CONTADOR FROM `mensaje` WHERE mensajeEstado=0");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setList(array("countMensaje" => $row['CONTADOR']));

                    }
                    $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE tipo=2 and estado=0 and precio_curso=0");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setList(array("countAlumno" => $row['CONTADOR']));

                    }
                    $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE tipo=2 and estado=1");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setList(array("countAlumnoActivo" => $row['CONTADOR']));

                    }
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador` WHERE Estado=0 or (Estado IS NULL)");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setList(array("countAlumnoEstadoActivo" => $row['CONTADOR']));

                    }
                    $stmt = $conexion->prepare("SELECT COUNT(idtarea) AS CONTADOR FROM `tarea` WHERE tipo=0 and estado=0");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setList(array("countTarea" => $row['CONTADOR']));

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
    protected function eliminar_mensaje_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `mensaje` WHERE idmensaje=:Codigo ");
        $sql->bindValue(":Codigo", $codigo);
        return $sql;
    }
    protected function actualizar_mensaje_modelo($conexion, $Mensaje)
    {
        $sql = $conexion->prepare("UPDATE `mensaje` SET mensajeEstado=? WHERE cuenta_codigoCuenta=? and idmensaje=?");
        // $sql->bindParam(1, $Mensaje->getTitulo(), PDO::PARAM_STR);
        // $sql->bindParam(2, $Mensaje->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(1, $Mensaje->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(2, $Mensaje->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(3, $Mensaje->getIdMensaje(), PDO::PARAM_INT);
        return $sql;

    }
}
