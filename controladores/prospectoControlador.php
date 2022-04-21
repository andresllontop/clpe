<?php

require_once './modelos/prospectoModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/prospecto.php';

class prospectoControlador extends prospectoModelo
{
    public function agregar_prospecto_controlador($Prospecto)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Prospecto->setNombre(mainModel::limpiar_cadena($Prospecto->getNombre()));
            $Prospecto->setCuenta(mainModel::limpiar_cadena($Prospecto->getCuenta()));
            $Prospecto->setDocumento(mainModel::limpiar_cadena($Prospecto->getDocumento()));
            $Prospecto->setPais(mainModel::limpiar_cadena($Prospecto->getPais()));
            $Prospecto->setEmail(mainModel::limpiar_cadena($Prospecto->getEmail()));
            $Prospecto->setEspecialidad(mainModel::limpiar_cadena($Prospecto->getEspecialidad()));
            $Prospecto->setTelefono(mainModel::limpiar_cadena($Prospecto->getTelefono()));
            $Prospecto->setIdFatherProspecto(mainModel::limpiar_cadena($Prospecto->getIdFatherProspecto()));
            $prospectoLista = prospectoModelo::datos_prospecto_modelo($this->conexion_db, 'cuenta', $Prospecto);
            if ($prospectoLista['countFilter'] > 0) {
                $insBeanCrud->setMessageServer('ya se encuentra la cuenta del Usuario registrado para el prospecto');
            } else {
                //$Prospecto->setCuenta($prospectoLista['list'][0]['cuenta']);
                $stmt = prospectoModelo::agregar_prospecto_modelo($this->conexion_db, $Prospecto);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_prospecto_controlador($this->conexion_db, 0, 20, ""));

                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar la prospecto ");
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
    public function datos_prospecto_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(prospectoModelo::datos_prospecto_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_prospecto_controlador($conexion, $inicio, $registros, $filter)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->prepare("SELECT COUNT(idprospecto) AS CONTADOR  FROM `prospecto` WHERE nombre LIKE CONCAT('%',?,'%') ");
            $stmt->bindValue(1, $filter, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);

                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `prospecto` WHERE nombre LIKE CONCAT('%',?,'%')  ORDER BY idprospecto DESC LIMIT ?,? ");
                    $stmt->bindValue(1, $filter, PDO::PARAM_STR);
                    $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insProspecto = new Prospecto();
                        $insProspecto->setIdprospecto($row['idprospecto']);
                        $insProspecto->setDocumento($row['documento']);
                        $insProspecto->setCuenta($row['cuenta']);
                        $insProspecto->setNombre($row['nombre']);
                        $insProspecto->setPais($row['pais']);
                        $insProspecto->setTelefono($row['telefono']);
                        $insProspecto->setEmail($row['email']);
                        $insProspecto->setEspecialidad($row['especialidad']);
                        $insProspecto->setIdFatherProspecto($row['father_idprospecto']);
                        $insBeanPagination->setList($insProspecto->__toString());
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
    public function bean_paginador_prospecto_controlador($pagina, $registros, $filter)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $filter = mainModel::limpiar_cadena($filter);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;

            $insBeanCrud->setBeanPagination(self::paginador_prospecto_controlador($this->conexion_db, $inicio, $registros, $filter));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_prospecto_controlador($Prospecto)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Prospecto->setIdprospecto(mainModel::limpiar_cadena($Prospecto->getIdprospecto()));
            $prospectoLista = prospectoModelo::datos_prospecto_modelo($this->conexion_db, 'unico', $Prospecto);

            if ($prospectoLista['countFilter'] == 0) {
                $insBeanCrud->setMessageServer('No se encuentra la prospecto');
            } else {
                $prospectoLista = prospectoModelo::datos_prospecto_modelo($this->conexion_db, 'father', $Prospecto);
                if ($prospectoLista['countFilter'] > 0) {
                    $insBeanCrud->setMessageServer('Primero debes eliminar a la rama inferior de los prospecto');
                } else {
                    $stmt = prospectoModelo::eliminar_prospecto_modelo($this->conexion_db, $Prospecto->getIdprospecto());

                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer('ok');
                        $insBeanCrud->setBeanPagination(self::paginador_prospecto_controlador($this->conexion_db, 0, 20, ""));

                    } else {
                        $insBeanCrud->setMessageServer('No se eliminó la prospecto');
                    }
                }
            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error!: ' . $th->getMessage() . '<br/>';

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';

        } finally {
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_prospecto_controlador($Prospecto)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Prospecto->setIdprospecto(mainModel::limpiar_cadena($Prospecto->getIdprospecto()));
            $Prospecto->setNombre(mainModel::limpiar_cadena($Prospecto->getNombre()));
            $Prospecto->setPais(mainModel::limpiar_cadena($Prospecto->getPais()));
            $Prospecto->setNombre(mainModel::limpiar_cadena($Prospecto->getNombre()));
            $Prospecto->setCuenta(mainModel::limpiar_cadena($Prospecto->getCuenta()));
            $Prospecto->setDocumento(mainModel::limpiar_cadena($Prospecto->getDocumento()));
            $Prospecto->setEmail(mainModel::limpiar_cadena($Prospecto->getEmail()));
            $Prospecto->setEspecialidad(mainModel::limpiar_cadena($Prospecto->getEspecialidad()));
            $Prospecto->setTelefono(mainModel::limpiar_cadena($Prospecto->getTelefono()));
            $Prospecto->setIdFatherProspecto(mainModel::limpiar_cadena($Prospecto->getIdFatherProspecto()));
            $prospectoLista = prospectoModelo::datos_prospecto_modelo($this->conexion_db, "unico", $Prospecto);

            if ($prospectoLista["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No hemos encontrado la prospecto");
            } else {
                $stmt = prospectoModelo::actualizar_prospecto_modelo($this->conexion_db, $Prospecto);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_prospecto_controlador($this->conexion_db, 0, 20, ""));

                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar la prospecto ");
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
