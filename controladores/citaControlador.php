<?php

require_once './modelos/citaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/cliente.php';
require_once './classes/principal/subtitulo.php';
class citaControlador extends citaModelo
{
    public function agregar_cita_controlador($Cita)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cita->setFechaSolicitud(date('Y-m-d H:i:s'));
            $Cita->setEstadoSolicitud(mainModel::limpiar_cadena($Cita->getEstadoSolicitud()));
            $Cita->setSubtitulo(mainModel::limpiar_cadena($Cita->getSubtitulo()));
            $Cita->setCliente(mainModel::limpiar_cadena($Cita->getCliente()));
            $Cita->setTipo(mainModel::limpiar_cadena($Cita->getTipo()));
            $Cita->setAsunto(mainModel::limpiar_cadena($Cita->getAsunto()));
            $stmt = citaModelo::agregar_cita_modelo($this->conexion_db, $Cita);
            if ($stmt->execute()) {
                $this->conexion_db->commit();
                $insBeanCrud->setMessageServer("ok");
                $insBeanCrud->setBeanPagination(self::paginador_cita_controlador($this->conexion_db, 0, 20, $Cita->getCliente()));

            } else {

                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar la cita ");
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
    public function datos_cita_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $codigo = mainModel::limpiar_cadena($codigo);
            $insBeanCrud->setBeanPagination(citaModelo::datos_cita_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_cita_controlador($conexion, $inicio, $registros, $filter)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(idcita) AS CONTADOR  FROM `cita` WHERE cliente=?");
            $stmt->bindValue(1, $filter, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);

                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT cit.*,admis.AdminNombre,admis.AdminApellido,subt.nombre as subt_nombre FROM `cita` as cit INNER JOIN `administrador` as admis ON admis.Cuenta_Codigo=cit.cliente LEFT JOIN `subtitulo` as subt ON subt.codigo_subtitulo=cit.subtitulo WHERE cit.cliente=? ORDER BY cit.idcita ASC LIMIT ?,?");
                    $stmt->bindValue(1, $filter, PDO::PARAM_STR);
                    $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insCliente = new Cliente();
                        $insCliente->setNombre($row['AdminNombre']);
                        $insCliente->setApellido($row['AdminApellido']);
                        $insCliente->setCuenta($row['cliente']);

                        $insSubtitulo = new SubTitulo();
                        $insSubtitulo->setNombre($row['subt_nombre']);
                        $insSubtitulo->setCodigo($row['subtitulo']);

                        $inscita = new Cita();
                        $inscita->setIdcita($row['idcita']);
                        $inscita->setTipo($row['tipo']);
                        $inscita->setAsunto($row['asunto']);
                        $inscita->setEstadoSolicitud($row['estado_solicitud']);
                        $inscita->setFechaSolicitud($row['fecha_solicitud']);
                        $inscita->setFechaProgramada($row['fecha_rogramada']);
                        $inscita->setFechaAtendida($row['fecha_atendida']);
                        $inscita->setFechaAceptacion($row['fecha_aceptacion']);

                        $inscita->setCliente($insCliente->__toString());
                        $inscita->setSubtitulo($insSubtitulo->__toString());
                        $insBeanPagination->setList($inscita->__toString());
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
    public function bean_paginador_cita_controlador($pagina, $registros, $filter)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $filter = mainModel::limpiar_cadena($filter);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;

            $insBeanCrud->setBeanPagination(self::paginador_cita_controlador($this->conexion_db, $inicio, $registros, $filter));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_cita_controlador($Cita)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cita->setIdcita(mainModel::limpiar_cadena($Cita->getIdcita()));
            $citaLista = citaModelo::datos_cita_modelo($this->conexion_db, 'unico', $Cita);

            if ($citaLista['countFilter'] == 0) {
                $insBeanCrud->setMessageServer('No se encuentra la cita');
            } else {

                $stmt = citaModelo::eliminar_cita_modelo($this->conexion_db, $Cita->getIdcita());

                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer('ok');
                    $insBeanCrud->setBeanPagination(self::paginador_cita_controlador($this->conexion_db, 0, 20, $citaLista['list'][0]['cliente']));

                } else {
                    $insBeanCrud->setMessageServer('No se eliminó la cita');
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
    public function actualizar_cita_controlador($Cita)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cita->setIdcita(mainModel::limpiar_cadena($Cita->getIdcita()));
            $Cita->setEstadoSolicitud(mainModel::limpiar_cadena($Cita->getEstadoSolicitud()));

            $citaLista = citaModelo::datos_cita_modelo($this->conexion_db, "unico", $Cita);
            if ($citaLista["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No hemos encontrado la cita");
            } else {
                $Cita->setFechaAtendida(date('Y-m-d H:i:s'));

                $stmt = citaModelo::actualizar_cita_modelo($this->conexion_db, $Cita);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_cita_controlador($this->conexion_db, 0, 20, $citaLista['list'][0]['cliente']));

                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar la cita ");
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
    public function bean_paginador_tarea_cliente_controlador($pagina, $registros, $filtro, $libro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $filtro = mainModel::limpiar_cadena($filtro);
            $libro = mainModel::limpiar_cadena($libro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_cliente_tarea_controlador($this->conexion_db, $inicio, $registros, $filtro, $libro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_cliente_tarea_controlador($conexion, $inicio, $registros, $filtro, $libro)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT tar.cuenta FROM `tarea` as tar inner join `administrador` as admmini ON tar.cuenta=admmini.Cuenta_Codigo inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo left join `certificado` as cer ON cer.cuenta=admmini.Cuenta_Codigo WHERE cer.idcertificado is null and tar.tipo=0 and cuent.tipo=2 and cuent.idcuenta!=1 and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') OR cuent.email like concat('%',?,'%') OR admmini.AdminTelefono like concat('%',?,'%') OR admmini.pais like concat('%',?,'%')) AND (tar.codigo_subtitulo LIKE CONCAT('%',?,'%')) GROUP BY tar.cuenta ");
            $stmt->bindValue(1, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(2, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(3, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(4, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(5, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(6, $libro, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            $insBeanPagination->setCountFilter(count($datos));
            if (count($datos) > 0) {
                $stmt = $conexion->prepare("SELECT cit.*,admmini.*,cuent.*,sum(CASE WHEN tar.estado = 0 THEN 1 ELSE 0 end) as totalestado,sum(tar.estado) as totalnoestado FROM `tarea` as tar inner join `administrador` as admmini ON tar.cuenta=admmini.Cuenta_Codigo inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo left join `certificado` as cer ON cer.cuenta=admmini.Cuenta_Codigo left join `cita` as cit ON cit.cliente=tar.cuenta WHERE cer.idcertificado is null and tar.tipo=0 and cuent.tipo=2 and cuent.idcuenta!=1 and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') OR cuent.email like concat('%',?,'%') OR admmini.AdminTelefono like concat('%',?,'%')OR admmini.pais like concat('%',?,'%')) AND (tar.codigo_subtitulo LIKE CONCAT('%',?,'%')) GROUP BY tar.cuenta ORDER BY max(tar.fecha) DESC LIMIT ?,? ");
                $stmt->bindValue(1, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(2, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(3, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(4, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(5, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(6, $libro, PDO::PARAM_STR);
                $stmt->bindValue(7, $inicio, PDO::PARAM_INT);
                $stmt->bindValue(8, $registros, PDO::PARAM_INT);

                $stmt->execute();
                $datos = $stmt->fetchAll();

                foreach ($datos as $row) {

                    $insCliente = new Cliente();
                    $insCuenta = new Cuenta();
                    $insCuenta->setIdCuenta($row['idcuenta']);
                    $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                    //$insCuenta->setUsuario($row['usuario']);
                    //$insCuenta->setClave(mainModel::decryption($row['clave']));
                    $insCuenta->setEmail($row['email']);
                    $insCuenta->setEstado($row['estado']);
                    // $insCuenta->setTipo($row['tipo']);
                    $insCuenta->setFoto($row['foto']);
                    //$insCuenta->setPrecio($row['precio_curso']);
                    // $insCuenta->setVoucher($row['voucher']);

                    $insCliente->setIdCliente($row['id']);
                    $insCliente->setNombre($row['AdminNombre']);
                    $insCliente->setTelefono($row['AdminTelefono']);
                    $insCliente->setApellido($row['AdminApellido']);
                    //$insCliente->setOcupacion($row['AdminOcupacion']);
                    $insCliente->setPais($row['pais']);
                    $insCliente->setTarea(array("totalestado" => $row['totalestado'],
                        "totalnoestado" => $row['totalnoestado'],
                    ));
                    $insCliente->setCuenta($insCuenta->__toString());
                    $insBeanPagination->setList($insCliente->__toString());

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
}
