<?php

require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
require_once './modelos/vendedorModelo.php';

class vendedorControlador extends vendedorModelo
{
    public function agregar_vendedor_controlador($Vendedor)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Vendedor->setNombre(mainModel::limpiar_cadena($Vendedor->getNombre()));
            $Vendedor->setApellido(mainModel::limpiar_cadena($Vendedor->getApellido()));
            $Vendedor->setTipo(mainModel::limpiar_cadena($Vendedor->getTipo()));
            $Vendedor->setEmpresa(mainModel::limpiar_cadena($Vendedor->getEmpresa()));
            $Vendedor->setTelefono(mainModel::limpiar_cadena($Vendedor->getTelefono()));
            $Vendedor->setPais(mainModel::limpiar_cadena($Vendedor->getPais()));
            $Vendedor->setCodigo(mainModel::limpiar_cadena($Vendedor->getCodigo()));
            $stmt = vendedorModelo::agregar_vendedor_modelo($this->conexion_db, $Vendedor);
            if ($stmt->execute()) {
                $this->conexion_db->commit();
                $insBeanCrud->setMessageServer("ok");
                $insBeanCrud->setBeanPagination(self::paginador_vendedor_controlador($this->conexion_db, 0, 20));

            } else {
                $insBeanCrud->setMessageServer("No hemos podido registrar el vendedor");
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
    public function datos_vendedor_controlador($tipo, $Vendedor)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(vendedorModelo::datos_vendedor_modelo($this->conexion_db, $tipo, $Vendedor));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_vendedor_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->query("SELECT COUNT(idvendedor) AS CONTADOR FROM `vendedor`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `vendedor` ORDER BY nombre ASC LIMIT ?,? ");
                    $stmt->bindParam(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindParam(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $insVendedor = new Vendedor();
                        $insVendedor->setIdVendedor($row['idvendedor']);
                        $insVendedor->setNombre($row['nombre']);
                        $insVendedor->setApellido($row['apellido']);
                        $insVendedor->setTipo($row['tipo']);
                        $insVendedor->setTelefono($row['telefono']);
                        $insVendedor->setEmpresa($row['empresa']);
                        $insVendedor->setPais($row['pais']);
                        $insVendedor->setCodigo($row['codigo']);
                        $insBeanPagination->setList($insVendedor->__toString());
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
    public function bean_paginador_vendedor_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_vendedor_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_vendedor_controlador($Vendedor)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Vendedor->setIdVendedor(mainModel::limpiar_cadena($Vendedor->getIdVendedor()));
            $vendedor = vendedorModelo::datos_vendedor_modelo($this->conexion_db, "unico", $Vendedor);
            if ($vendedor["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el vendedor");
            } else {
                $stmt = vendedorModelo::eliminar_vendedor_modelo($this->conexion_db, mainModel::limpiar_cadena($Vendedor->getIdVendedor()));
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_vendedor_controlador($this->conexion_db, 0, 20));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el vendedor");
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
    public function actualizar_vendedor_controlador($Vendedor)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Vendedor->setIdVendedor(mainModel::limpiar_cadena($Vendedor->getIdVendedor()));
            $Vendedor->setNombre(mainModel::limpiar_cadena($Vendedor->getNombre()));
            $Vendedor->setApellido(mainModel::limpiar_cadena($Vendedor->getApellido()));
            $Vendedor->setTipo(mainModel::limpiar_cadena($Vendedor->getTipo()));
            $Vendedor->setEmpresa(mainModel::limpiar_cadena($Vendedor->getEmpresa()));
            $Vendedor->setTelefono(mainModel::limpiar_cadena($Vendedor->getTelefono()));
            $Vendedor->setPais(mainModel::limpiar_cadena($Vendedor->getPais()));
            $Vendedor->setCodigo(mainModel::limpiar_cadena($Vendedor->getCodigo()));

            $vendedor = vendedorModelo::datos_vendedor_modelo($this->conexion_db, "unico", $Vendedor);

            if ($vendedor["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el Vendedor");
            } else {
                $stmt = vendedorModelo::actualizar_vendedor_modelo($this->conexion_db, $Vendedor);
                if ($stmt->execute()) {

                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_vendedor_controlador($this->conexion_db, 0, 20));
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido actualizar el vendedor");
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
