<?php

require_once './modelos/empresaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class empresaControlador extends empresaModelo
{
    public function actualizar_empresa_controlador($Empresa)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $Empresa->setIdEmpresa(mainModel::limpiar_cadena($Empresa->getIdEmpresa()));
            $Empresa->setTelefono(mainModel::limpiar_cadena($Empresa->getTelefono()));
            $Empresa->setVision(mainModel::limpiar_cadena($Empresa->getVision()));
            $Empresa->setNombre(mainModel::limpiar_cadena($Empresa->getNombre()));
            $Empresa->setMision(mainModel::limpiar_cadena($Empresa->getMision()));
            $Empresa->setDireccion(mainModel::limpiar_cadena($Empresa->getDireccion()));
            $Empresa->setTelefonoSegundo(mainModel::limpiar_cadena($Empresa->getTelefonoSegundo()));
            $Empresa->setPrecio(mainModel::limpiar_cadena($Empresa->getPrecio()));
            $Empresa->setFrase(mainModel::limpiar_cadena($Empresa->getFrase()));
            $Empresa->setInstagram(mainModel::limpiar_cadena($Empresa->getInstagram()));

            $libro = empresaModelo::datos_empresa_modelo($this->conexion_db, "unico", $Empresa);
            if ($libro["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No hemos encontrado la empresa");
            } else {
                if (isset($_FILES['txtLogoEmpresa'])) {
                    $original = $_FILES['txtLogoEmpresa'];
                    $nombre = $original['name'];

                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        //5000 KB
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 5000, $original, $nombre, "./adjuntos/");
                        if ($resultado != "") {
                            $Empresa->setLogo($resultado);
                            $stmt = empresaModelo::actualizar_empresa_modelo($this->conexion_db, $Empresa);
                            if ($stmt->execute()) {
                                unlink('./adjuntos/' . $libro["list"][0]['logo']);

                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::datos_empresa_controlador("conteo", 5)["beanPagination"]);
                            } else {
                                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar la empresa");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, No hemos podido subir la imagen");
                        }
                    }

                } else {
                    $Empresa->setLogo($libro["list"][0]['logo']);
                    $stmt = empresaModelo::actualizar_empresa_modelo($this->conexion_db, $Empresa);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::datos_empresa_controlador("conteo", 5)["beanPagination"]);

                    } else {
                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar la empresa");
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
    public function actualizar_mision_empresa_controlador($Empresa)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $Empresa->setIdEmpresa(mainModel::limpiar_cadena($Empresa->getIdEmpresa()));
            $Empresa->setMision(mainModel::limpiar_cadena($Empresa->getMision()));
            $libro = empresaModelo::datos_empresa_modelo($this->conexion_db, "unico", $Empresa);
            if ($libro["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No hemos encontrado la empresa");
            } else {

                $stmt = empresaModelo::actualizar_mision_empresa_modelo($this->conexion_db, $Empresa);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::datos_empresa_controlador("conteo", 5)["beanPagination"]);

                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar la empresa");
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
    public function datos_empresa_controlador($tipo, $codigo)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $codigo = mainModel::limpiar_cadena($codigo);
            if ($tipo == "conteo-publico") {
                $insBeanPagination = new BeanPagination();

                $insBeanPagination->setList(empresaModelo::datos_empresa_modelo($this->conexion_db, "conteo", $codigo)['list'][0]);
                $stmt = $this->conexion_db->prepare("SELECT SUM(contador) AS CONTADOR FROM `visita`");
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    $insBeanPagination->setCountFilter($row['CONTADOR']);
                }
                $insBeanCrud->setBeanPagination($insBeanPagination->__toString());
                $stmt->closeCursor();
                $stmt = null;
            } else {
                $insBeanCrud->setBeanPagination(empresaModelo::datos_empresa_modelo($this->conexion_db, $tipo, $codigo));
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

}
