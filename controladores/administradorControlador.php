<?php

require_once './modelos/administradorModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

class administradorControlador extends administradorModelo
{

    public function agregar_administrador_controlador($tipo, $Administrador)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Administrador->setTelefono(mainModel::limpiar_cadena($Administrador->getTelefono()));
            // $Administrador->setOcupacion(mainModel::limpiar_cadena($Administrador->getOcupacion()));
            $Administrador->setNombre(mainModel::limpiar_cadena($Administrador->getNombre()));
            $Administrador->setApellido(mainModel::limpiar_cadena($Administrador->getApellido()));
            $insCuenta = new Cuenta();
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Administrador->getCuenta()->usuario));
            $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($Administrador->getCuenta()->clave)));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Administrador->getCuenta()->email));
            $insCuenta->setPerfil(mainModel::limpiar_cadena($Administrador->getCuenta()->perfil));
            $insCuenta->setEstado(0);
            $insCuenta->setTipo(1);

            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=?");
            $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                if ($row['CONTADOR'] > 0) {
                    $insBeanCrud->setMessageServer("Se encuentra Registrado el Usuario, cambie de Email");
                } else {
                    $stmt = $this->conexion_db->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador` WHERE AdminNombre=? and AdminApellido=?");
                    $stmt->bindValue(1, $Administrador->getNombre(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $Administrador->getApellido(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos2 = $stmt->fetchAll();
                    foreach ($datos2 as $row2) {
                        if ($row2['CONTADOR'] > 0) {
                            $insBeanCrud->setMessageServer("Ya se encuentra registrado, cambie de Nombres y Apellidos");
                        } else {
                            $stmt = $this->conexion_db->query("SELECT MAX(idcuenta) AS MAXIMO FROM `cuenta`");
                            $datos3 = $stmt->fetchAll();
                            foreach ($datos3 as $row3) {
                                if ($row3['MAXIMO'] > 0) {
                                    $cuentacodigo = mainModel::generar_codigo_aleatorio("AC", 7, $row3['MAXIMO'] + 1);
                                    $insCuenta->setCuentaCodigo($cuentacodigo);
                                    $stmt = administradorModelo::agregar_cuenta_modelo($this->conexion_db, $insCuenta);
                                    if ($stmt->execute()) {
                                        $Administrador->setCuenta($cuentacodigo);
                                        $stmt = administradorModelo::agregar_administrador_modelo($this->conexion_db, $Administrador);
                                        if ($stmt->execute()) {
                                            $this->conexion_db->commit();
                                            $insBeanCrud->setMessageServer("ok");
                                            if ($tipo == 1) {
                                                $insBeanCrud->setBeanPagination(self::paginador_administrador_controlador($this->conexion_db, 0, 5, 0));
                                            }

                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido registrar la cuenta");
                                    }
                                }

                            }

                        }
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
    public function datos_administrador_controlador($tipo, $codigo)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(administradorModelo::datos_administrador_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }

    public function reporte_administrador_controlador($tipo, $codigo)
    {
        $row = "";

        try {
            $variable = administradorModelo::datos_administrador_modelo($this->conexion_db, mainModel::limpiar_cadena($tipo), $codigo);
            if ($variable['countFilter'] > 0) {
                $titulo = "";
                switch ($codigo->getCuenta()->getTipo()) {
                    case 2:
                        if ($codigo->getCuenta()->getEstado() == 0) {
                            $titulo = "ALUMNOS NO MATRICULADOS";
                        } else if ($codigo->getCuenta()->getEstado() == 1) {
                            $titulo = "ALUMNOS MATRICULADOS";
                        } else {
                            $titulo = "ALUMNOS";
                        }

                        break;

                    default:
                        $titulo = "PERSONAL ADMINISTRATIVO";
                        break;
                }
                $row = "<table border='1'>
        <thead>
        <tr>
        <th colspan='10'> LISTA DE $titulo </th>
        </tr>
        <tr>
          <th ></th>
          <th >NOMBRES</th>
          <th >APELLIDOS</th>
          <th >OCUPACION/OFICIO</th>
          <th >PAIS</th>
          <th>TELEFONO</th>
          <th >CODIGO USUARIO</th>
          <th >NOMBRE USUARIO</th>
          <th >EMAIL</th>
          <th >MONTO</th>
        </tr>
      </thead>
      <tbody> ";
                $contador = 1;
                foreach ($variable['list'] as $value) {
                    $row = $row . "
            <tr>
            <td>" . ($contador++) . "</td>
              <td>" . $value['nombre'] . "</td>
              <td>" . $value['apellido'] . "</td>
              <td>" . $value['ocupacion'] . "</td>
              <td>" . $value['pais'] . "</td>
              <td>" . $value['telefono'] . "</td>
              <td>" . $value['cuenta']['cuentaCodigo'] . "</td>
              <td>" . $value['cuenta']['usuario'] . "</td>
              <td>" . $value['cuenta']['email'] . "</td>
              <td>" . $value['cuenta']['voucher'] . "</td>
            </tr>";
                }
                $row = $row . "</tbody> </table>";
                header("Content-Disposition: attachment; filename=$titulo.xls");
            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $row;
    }
    public function paginador_administrador_controlador($conexion, $inicio, $registros, $estado)
    {
        $insBeanPagination = new BeanPagination();
        try {
            if ((int) $estado > -1) {
                $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE estado=? and tipo=1 and idcuenta!=1");
                $stmt->bindValue(1, $estado, PDO::PARAM_INT);
            } else {
                $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE tipo=1 and idcuenta!=1");
            }

            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    if ((int) $estado > -1) {
                        $stmt = $conexion->prepare("SELECT * FROM `administrador`  as admmini inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo WHERE cuent.estado=? and cuent.tipo=1 and cuent.idcuenta!=1 ORDER BY admmini.AdminNombre ASC LIMIT ?,?");
                        $stmt->bindValue(1, $estado, PDO::PARAM_INT);
                        $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                        $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                    } else {
                        $stmt = $conexion->prepare("SELECT * FROM `administrador`  as admmini inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo WHERE cuent.tipo=1 and cuent.idcuenta!=1 ORDER BY admmini.AdminNombre ASC LIMIT ?,?");
                        $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                        $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    }

                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insAdministrador = new Administrador();
                        $insCuenta = new Cuenta();
                        $insCuenta->setIdCuenta($row['idcuenta']);
                        $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                        $insCuenta->setUsuario($row['usuario']);
                        $insCuenta->setClave(mainModel::decryption($row['clave']));
                        $insCuenta->setEmail($row['email']);
                        $insCuenta->setEstado($row['estado']);
                        $insCuenta->setTipo($row['tipo']);
                        $insCuenta->setPerfil($row['perfil']);
                        $insCuenta->setFoto($row['foto']);

                        $insAdministrador->setIdAdministrador($row['id']);
                        $insAdministrador->setNombre($row['AdminNombre']);
                        $insAdministrador->setTelefono($row['AdminTelefono']);
                        $insAdministrador->setApellido($row['AdminApellido']);
                        $insAdministrador->setOcupacion($row['AdminOcupacion']);
                        $insAdministrador->setPais($row['pais']);
                        $insAdministrador->setCuenta($insCuenta->__toString());
                        $insBeanPagination->setList($insAdministrador->__toString());

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
    public function bean_paginador_administrador_controlador($pagina, $registros, $estado)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $estado = mainModel::limpiar_cadena($estado);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_administrador_controlador($this->conexion_db, $inicio, $registros, $estado));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_administrador_controlador($Administrador)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Administrador->setIdAdministrador(mainModel::limpiar_cadena($Administrador->getIdAdministrador()));

            $lista = administradorModelo::datos_administrador_modelo($this->conexion_db, "unico", $Administrador);
            if ($lista["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No podemos eliminar porque no existe el alumno registrado");
            } else {
                $stmt = administradorModelo::eliminar_administrador_modelo($this->conexion_db, $Administrador->getIdAdministrador());
                if ($stmt->execute()) {
                    $stmt = administradorModelo::eliminar_cuenta_modelo($this->conexion_db, $lista["list"][0]['cuenta']['idcuenta']);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_administrador_controlador($this->conexion_db, 0, 5, $lista["list"][0]['cuenta']['estado']));
                        if ($lista["list"][0]['cuenta']['foto'] != "") {
                            unlink('./adjuntos/clientes/' . $lista["list"][0]['cuenta']['foto']);
                        }
                    } else {
                        $insBeanCrud->setMessageServer("no se eliminó el alumno");
                    }
                } else {
                    $insBeanCrud->setMessageServer("no se eliminó el alumno");
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
    public function actualizar_administrador_controlador($tipo, $Administrador)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Administrador->setTelefono(mainModel::limpiar_cadena($Administrador->getTelefono()));
            // $Administrador->setOcupacion(mainModel::limpiar_cadena($Administrador->getOcupacion()));
            $Administrador->setNombre(mainModel::limpiar_cadena($Administrador->getNombre()));
            $Administrador->setApellido(mainModel::limpiar_cadena($Administrador->getApellido()));
            $Administrador->setPais(mainModel::limpiar_cadena($Administrador->getPais()));
            $insCuenta = new Cuenta();
           
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Administrador->getCuenta()->usuario));
            $insCuenta->setPerfil(mainModel::limpiar_cadena($Administrador->getCuenta()->perfil));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Administrador->getCuenta()->email));
            // $insCuenta->setEstado(0);
            // $insCuenta->setTipo(2);
           
            $administradorunico = administradorModelo::datos_administrador_modelo($this->conexion_db, "unico", $Administrador);

            if ($administradorunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el usuario");
            } else {
                if (isset($_FILES['txtFoto'])) {
                    $original = $_FILES['txtFoto'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 3700, $original, $nombre, "./adjuntos/clientes/");
                        if ($resultado != "") {
                            $insCuenta->setFoto($resultado);
                            $insCuenta->setIdCuenta($administradorunico["list"][0]['cuenta']['idcuenta']);
                            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                            $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                            $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                if ($row['CONTADOR'] == 0) {
                                    if ($Administrador->getCuenta()->clave == "") {
                                        $insCuenta->setClave($administradorunico["list"][0]['cuenta']['clave']);
                                    } else {
                                        $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($Administrador->getCuenta()->clave)));
                                    }

                                    $stmt = administradorModelo::actualizar_cuenta_modelo($this->conexion_db, $insCuenta);
                                    if ($stmt->execute()) {
                                        $stmt = administradorModelo::actualizar_administrador_modelo($this->conexion_db, $Administrador);
                                        if ($stmt->execute()) {

                                            if ($tipo == 1) {
                                                $insBeanCrud->setBeanPagination(self::paginador_administrador_controlador($this->conexion_db, 0, 15, $administradorunico["list"][0]['cuenta']['estado']));
                                            }
                                            if ($administradorunico["list"][0]['cuenta']['foto'] != "") {
                                                unlink('./adjuntos/clientes/' . $administradorunico["list"][0]['cuenta']['foto']);
                                            }
                                            $this->conexion_db->commit();
                                            $insBeanCrud->setMessageServer("ok");
                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                                    }

                                } else {
                                    $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                                }
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }
                } else {
                    $insCuenta->setFoto($administradorunico["list"][0]['cuenta']['foto']);
                    $insCuenta->setIdCuenta($administradorunico["list"][0]['cuenta']['idcuenta']);
                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                    $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        if ($row['CONTADOR'] == 0) {
                            if ($Administrador->getCuenta()->clave == "") {
                                $insCuenta->setClave($administradorunico["list"][0]['cuenta']['clave']);
                            } else {
                                $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($Administrador->getCuenta()->clave)));
                            }
                            $stmt = administradorModelo::actualizar_cuenta_modelo($this->conexion_db, $insCuenta);
                            if ($stmt->execute()) {
                                $stmt = administradorModelo::actualizar_administrador_modelo($this->conexion_db, $Administrador);
                                if ($stmt->execute()) {
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    if ($tipo == 1) {
                                        $insBeanCrud->setBeanPagination(self::paginador_administrador_controlador($this->conexion_db, 0, 15, $administradorunico["list"][0]['cuenta']['estado']));
                                    }
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                        }
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
    public function actualizar_cuenta_estado_controlador($Cuenta)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cuenta->setCuentaCodigo(mainModel::limpiar_cadena($Cuenta->getCuentaCodigo()));
            $Cuenta->setEstado(mainModel::limpiar_cadena($Cuenta->getEstado()));
            $Cuenta->setIdCuenta(mainModel::limpiar_cadena($Cuenta->getIdCuenta()));

            $cuentaunico = administradorModelo::datos_administrador_modelo($this->conexion_db, "cuenta-unico", $Cuenta);
            if ($cuentaunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el usuario");
            } else {

                $stmt = administradorModelo::actualizar_cuenta_estado_modelo($this->conexion_db, $Cuenta);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_administrador_controlador($this->conexion_db, 0, 5, -1));
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido cambiar de estado al Usuario");
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
