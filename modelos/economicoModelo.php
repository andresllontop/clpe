<?php

require_once './core/mainModel.php';

class economicoModelo extends mainModel
{

    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_economico_modelo($conexion, $economico)
    {
        $sql = $conexion->prepare("INSERT INTO `economico`
            (titulo,descripcion,archivo,tipoArchivo,resumen)
             VALUES(:Titulo,:Descripcion,:Imagen,:Tipo,:Resumen)");
        $sql->bindValue(":Titulo", $economico->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $economico->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $economico->getArchivo(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $economico->getTipoArchivo(), PDO::PARAM_INT);
        $sql->bindValue(":Resumen", $economico->getResumen(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_economico_modelo($conexion, $tipo, $economico)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idhistorial_economico) AS CONTADOR FROM `historial_economico` WHERE idhistorial_economico=:IDeconomico");
                    $stmt->bindValue(":IDeconomico", $economico->getIdeconomico(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `historial_economico` WHERE idhistorial_economico=:IDeconomico");
                            $stmt->bindValue(":IDeconomico", $economico->getIdeconomico(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insEconomico = new Economico();
                                $insEconomico->setIdEconomico($row['idhistorial_economico']);
                                $insEconomico->setNombre($row['nombres']);
                                $insEconomico->setApellido($row['apellidos']);
                                $insEconomico->setTelefono($row['telefono']);
                                $insEconomico->setPais($row['pais']);
                                $insEconomico->setBanco($row['nombre_banco']);
                                $insEconomico->setMoneda($row['moneda']);
                                $insEconomico->setComision($row['comision']);
                                $insEconomico->setPrecio($row['precio']);
                                $insEconomico->setTipo($row['tipo']);
                                $insEconomico->setFecha($row['fecha']);
                                $insBeanPagination->setList($insEconomico->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idhistorial_economico) AS CONTADOR FROM `historial_economico`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `historial_economico` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insEconomico = new Economico();
                                $insEconomico->setIdEconomico($row['idhistorial_economico']);
                                $insEconomico->setNombre($row['nombres']);
                                $insEconomico->setApellido($row['apellidos']);
                                $insEconomico->setTelefono($row['telefono']);
                                $insEconomico->setPais($row['pais']);
                                $insEconomico->setBanco($row['nombre_banco']);
                                $insEconomico->setMoneda($row['moneda']);
                                $insEconomico->setComision($row['comision']);
                                $insEconomico->setPrecio($row['precio']);
                                $insEconomico->setTipo($row['tipo']);
                                $insEconomico->setFecha($row['fecha']);
                                $insBeanPagination->setList($insEconomico->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "general":
                    $stmt = $conexion->prepare("SELECT COUNT(idhistorial_economico) AS CONTADOR FROM `historial_economico` WHERE moneda='PEN'");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT sum(comision) AS comision ,sum(precio) AS precio,moneda FROM `historial_economico` WHERE moneda='PEN'");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insEconomico = new Economico();
                                $insEconomico->setMoneda($row['moneda']);
                                $insEconomico->setComision($row['comision']);
                                $insEconomico->setPrecio($row['precio']);
                                $insBeanPagination->setList($insEconomico->__toString());
                            }

                        }
                    }
                    $stmt = $conexion->prepare("SELECT COUNT(idhistorial_economico) AS CONTADOR FROM `historial_economico` WHERE moneda!='PEN'");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT sum(comision) AS comision ,sum(precio) AS precio,moneda FROM `historial_economico` WHERE moneda!='PEN'");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insEconomico = new Economico();
                                $insEconomico->setMoneda($row['moneda']);
                                $insEconomico->setComision($row['comision']);
                                $insEconomico->setPrecio($row['precio']);
                                $insBeanPagination->setList($insEconomico->__toString());
                            }

                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "moneda":
                    $stmt = $conexion->prepare("SELECT COUNT(idhistorial_economico) AS CONTADOR FROM `historial_economico` WHERE moneda=:Moneda");
                    $stmt->bindValue(":Moneda", $economico->getMoneda(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `historial_economico` WHERE moneda=:Moneda");
                            $stmt->bindValue(":Moneda", $economico->getMoneda(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insEconomico = new Economico();
                                $insEconomico->setIdEconomico($row['idhistorial_economico']);
                                $insEconomico->setNombre($row['nombres']);
                                $insEconomico->setApellido($row['apellidos']);
                                $insEconomico->setTelefono($row['telefono']);
                                $insEconomico->setPais($row['pais']);
                                $insEconomico->setBanco($row['nombre_banco']);
                                $insEconomico->setMoneda($row['moneda']);
                                $insEconomico->setComision($row['comision']);
                                $insEconomico->setPrecio($row['precio']);
                                $insEconomico->setTipo($row['tipo']);
                                $insEconomico->setFecha($row['fecha']);
                                $insEconomico->setVoucher($row['voucher']);
                                $insBeanPagination->setList($insEconomico->__toString());
                            }

                        }
                    }

                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "generalfecha":
                    if ($economico['moneda'] == "PEN") {

                        $stmt = $conexion->prepare("SELECT sum(comision) AS comision ,sum(precio) AS precio,moneda FROM `historial_economico` WHERE moneda='PEN'  and (DATE(fecha) BETWEEN ? AND ? ) ");
                        $stmt->bindValue(1, $economico['fechai'], PDO::PARAM_STR);
                        $stmt->bindValue(2, $economico['fechaf'], PDO::PARAM_STR);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            $insEconomico = new Economico();
                            $insEconomico->setMoneda($row['moneda']);
                            $insEconomico->setComision($row['comision']);
                            $insEconomico->setPrecio($row['precio']);
                            $insBeanPagination->setList($insEconomico->__toString());
                        }

                    } else {

                        $stmt = $conexion->prepare("SELECT sum(comision) AS comision ,sum(precio) AS precio,moneda FROM `historial_economico` WHERE moneda!='PEN'  and (DATE(fecha) BETWEEN ? AND ? ) ");
                        $stmt->bindValue(1, $economico['fechai'], PDO::PARAM_STR);
                        $stmt->bindValue(2, $economico['fechaf'], PDO::PARAM_STR);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            $insEconomico = new Economico();
                            $insEconomico->setMoneda($row['moneda']);
                            $insEconomico->setComision($row['comision']);
                            $insEconomico->setPrecio($row['precio']);
                            $insBeanPagination->setList($insEconomico->__toString());
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
    protected function eliminar_economico_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM `historial_economico` WHERE idhistorial_economico=:IDeconomico ");
        $sql->bindValue(":IDeconomico", $id, PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_economico_modelo($conexion, $economico)
    {
        $sql = $conexion->prepare("UPDATE `economico`
            SET titulo=:Titulo,descripcion=:Descripcion,
            archivo=:Imagen,tipoArchivo=:Tipo,resumen=:Resumen
            WHERE ideconomico=:ID");
        $sql->bindValue(":Titulo", $economico->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $economico->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $economico->getArchivo(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $economico->getTipoArchivo(), PDO::PARAM_INT);
        $sql->bindValue(":Resumen", $economico->getResumen(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $economico->getIdeconomico(), PDO::PARAM_INT);

        return $sql;
    }

}
