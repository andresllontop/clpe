<?php

require_once './modelos/bitacoraModelo.php';
require_once './classes/other/beanCrud.php';

require_once './classes/other/beanPagination.php';
class bitacoraControlador extends bitacoraModelo
{

    public function datos_bitacora_controlador($tipo, $codigo)
    {
        $tipo = mainModel::limpiar_cadena($tipo);
        return bitacoraModelo::datos_bitacora_modelo($tipo, $codigo);

    }
    public function paginador_bitacora_controlador($conexion, $inicio, $registros, $estado, $filtro)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $contador = 0;
            if ($estado != "") {
                $estado = date("d-m-Y", strtotime(date("d-m-Y") . "- 3 days"));
                $estado = new DateTime($estado);
                $estado = $estado->format('Y-m-d');
                $estado = " and date(bi.fecha_inicio) <= '$estado'";
            }

            $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `bitacora` WHERE tipo=2");
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT bi.estado,bi.fecha_fin,bi.fecha_inicio,bi.id as bi_id ,bi.tipo, cli.*,cuen.email FROM `bitacora` as bi inner join `cuenta` as cuen on cuen.CuentaCodigo= bi.cuenta_codigoCuenta inner join `administrador` as cli on cli.Cuenta_Codigo=cuen.CuentaCodigo WHERE cuen.tipo=2 and ( cli.AdminNombre like concat('%',?,'%') OR cli.AdminApellido like concat('%',?,'%') OR cli.AdminTelefono like concat('%',?,'%') OR cuen.email like concat('%',?,'%')) $estado ORDER BY bi.fecha_inicio ASC LIMIT ?,?");
                    $stmt->bindParam(1, $filtro, PDO::PARAM_STR);
                    $stmt->bindParam(2, $filtro, PDO::PARAM_STR);
                    $stmt->bindParam(3, $filtro, PDO::PARAM_STR);
                    $stmt->bindParam(4, $filtro, PDO::PARAM_STR);
                    $stmt->bindParam(5, $inicio, PDO::PARAM_INT);
                    $stmt->bindParam(6, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $contador++;
                        $insAdministrador = new Administrador();
                        $insCuenta = new Cuenta();
                        $insCuenta->setCuentaCodigo($row['Cuenta_Codigo']);
                        $insCuenta->setEmail($row['email']);

                        $insAdministrador->setIdAdministrador($row['id']);
                        $insAdministrador->setNombre($row['AdminNombre']);
                        $insAdministrador->setTelefono($row['AdminTelefono']);
                        $insAdministrador->setApellido($row['AdminApellido']);
                        $insAdministrador->setOcupacion($row['AdminOcupacion']);
                        $insAdministrador->setPais($row['pais']);
                        $insAdministrador->setCuenta($insCuenta->__toString());

                        $insBitacora = new Bitacora();
                        $insBitacora->setIdbitacora($row['bi_id']);
                        $insBitacora->setEstado($row['estado']);
                        $insBitacora->setFecha_Inicio($row['fecha_inicio']);
                        $insBitacora->setFecha_Fin($row['fecha_fin']);
                        $insBitacora->setTipo($row['tipo']);
                        $insBitacora->setCuenta($insAdministrador->__toString());
                        $insBeanPagination->setList($insBitacora->__toString());
                    }
                    $insBeanPagination->setCountFilter($contador);
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
    public function bean_paginador_bitacora_controlador($pagina, $registros, $estado, $filtro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $estado = mainModel::limpiar_cadena($estado);
            $filtro = mainModel::limpiar_cadena($filtro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_bitacora_controlador($this->conexion_db, $inicio, $registros, $estado, $filtro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_bitacora_controlador($Bitacora)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Bitacora->setIdBitacora(mainModel::limpiar_cadena($Bitacora->getIdBitacora()));

            $lista = bitacoraModelo::datos_bitacora_modelo($this->conexion_db, "unico", $Bitacora);
            if ($lista["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra la bitacora");
            } else {
                $stmt = bitacoraModelo::eliminar_bitacora_modelo($this->conexion_db, $Bitacora->getIdBitacora());
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_bitacora_controlador($this->conexion_db, 0, 5, $lista["list"][0]['estado']));
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido eliminar la bitacora");
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

    }public function actualizar_bitacora_controlador($Bitacora)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $datatime = date("Y/m/d H:i:s");
            $this->conexion_db->beginTransaction();
            $Bitacora->setEstado(mainModel::limpiar_cadena($Bitacora->getEstado()));
            $Bitacora->setIdbitacora(mainModel::limpiar_cadena($Bitacora->getIdbitacora()));
            $Bitacora->setFecha_Fin($datatime);
            $Bitacora->setCuenta(mainModel::limpiar_cadena($Bitacora->getCuenta()));
            $bitacoraunico = bitacoraModelo::datos_bitacora_modelo($this->conexion_db, "unico", $Bitacora);
            if ($bitacoraunico["countFilter"] == 0) {

                $insBeanCrud->setMessageServer("no se encuentra a Bitacora");
            } else {

                $stmt = bitacoraModelo::actualizar_bitacora_modelo($this->conexion_db, $Bitacora);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido actualizar la Bitacora");
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
    public function eliminar_bitacoraCuenta_controlador($cuenta)
    {
        $guardarAdmin = bitacoraModelo::eliminar_bitacoraCuenta_modelo(mainModel::limpiar_cadena($cuenta));
        return $guardarAdmin;

    }

}
