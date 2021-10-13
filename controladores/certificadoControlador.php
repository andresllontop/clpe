<?php

require_once './modelos/certificadoModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/principal/cliente.php';
require_once './classes/principal/cuenta.php';
require_once './classes/other/beanPagination.php';

class certificadoControlador extends certificadoModelo
{

    public function agregar_certificado_controlador($Certificado)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Certificado->setIndicador(mainModel::limpiar_cadena($Certificado->getIndicador()));
            $Certificado->setCuenta(mainModel::limpiar_cadena($Certificado->getCuenta()));
            $Certificado->setNombre(mainModel::limpiar_cadena($Certificado->getNombre()));

            $stmt = $this->conexion_db->prepare("SELECT max(fecha) AS fecha_final,min(fecha) AS fecha_inicial FROM `tarea` WHERE cuenta=:Cuenta");
            $stmt->bindValue(":Cuenta", $Certificado->getCuenta(), PDO::PARAM_STR);
            $stmt->execute();
            $datos2 = $stmt->fetchAll();
            foreach ($datos2 as $row2) {
                if ($row2['fecha_final'] != null) {
                    $Certificado->setFecha($row2['fecha_final']);
                    $Certificado->setFechaInicial($row2['fecha_inicial']);
                    $stmt = certificadoModelo::agregar_certificado_modelo($this->conexion_db, $Certificado);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();

                        $insBeanCrud->setBeanPagination(certificadoModelo::datos_certificado_modelo($this->conexion_db, "unico-alumno", $Certificado));
                        $insBeanCrud->setMessageServer("ok");
                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                    }
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido registrar los datos, porque no realizaste las lecciones.");
                }

            }

            $stmt->closeCursor();
            $stmt = null;

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
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function datos_certificado_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(certificadoModelo::datos_certificado_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_certificado_controlador($conexion, $inicio, $registros, $estado, $filtro, $libro)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $contador = 0;
            if ($estado == 0) {
                $stmt = $conexion->prepare("SELECT admmini.id,admmini.AdminNombre,admmini.AdminOcupacion, admmini.AdminApellido, admmini.AdminTelefono,cuent.foto,cuent.email,cer.estado,cer.fecha,cuent.CuentaCodigo FROM `administrador`  as admmini inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo inner join `librocuenta` as licuent ON cuent.CuentaCodigo=licuent.cuenta_codigocuenta left join `certificado` as cer ON cer.cuenta=admmini.Cuenta_Codigo WHERE (licuent.libro_codigoLibro like CONCAT('%',?,'%')) and (cer.estado is null) and cuent.tipo=2 and cuent.idcuenta!=1 and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') OR admmini.AdminTelefono like concat('%',?,'%') OR cuent.email like concat('%',?,'%')) ORDER BY admmini.AdminNombre ASC LIMIT ?,?");
            } else {
                $stmt = $conexion->prepare("SELECT admmini.id,admmini.AdminOcupacion,admmini.AdminNombre, admmini.AdminApellido, admmini.AdminTelefono,cuent.email,cuent.foto,cer.estado,cer.fecha,cuent.CuentaCodigo FROM `administrador`  as admmini inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo inner join `librocuenta` as licuent ON cuent.CuentaCodigo=licuent.cuenta_codigocuenta left join `certificado` as cer ON cer.cuenta=admmini.Cuenta_Codigo WHERE (licuent.libro_codigoLibro like CONCAT('%',?,'%')) and (cer.estado is not null) and cuent.tipo=2 and cuent.idcuenta!=1 and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') OR admmini.AdminTelefono like concat('%',?,'%') OR cuent.email like concat('%',?,'%')) ORDER BY admmini.AdminNombre ASC LIMIT ?,?");
            }
            $stmt->bindValue(1, $libro, PDO::PARAM_STR);
            $stmt->bindValue(2, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(3, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(4, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(5, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(6, $inicio, PDO::PARAM_INT);
            $stmt->bindValue(7, $registros, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $contador++;
                $insCliente = new Cliente();
                $insCuenta = new Cuenta();
                $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                $insCuenta->setEmail($row['email']);
                $insCuenta->setEstado($row['estado']);
                $insCuenta->setTipo($row['fecha']);
                $insCuenta->setFoto($row['foto']);

                $insCliente->setIdCliente($row['id']);
                $insCliente->setNombre($row['AdminNombre']);
                $insCliente->setTelefono($row['AdminTelefono']);
                $insCliente->setOcupacion($row['AdminOcupacion']);
                $insCliente->setApellido($row['AdminApellido']);
                $insCliente->setCuenta($insCuenta->__toString());
                $insBeanPagination->setList($insCliente->__toString());
            }
            $insBeanPagination->setCountFilter($contador);
            $stmt->closeCursor(); // this is not even required
            $stmt = null; // doing this is mandatory for connection to get closed

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";
        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";
        }
        return $insBeanPagination->__toString();
    }
    public function bean_paginador_certificado_controlador($pagina, $registros, $estado, $filtro, $libro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $estado = mainModel::limpiar_cadena($estado);
            $filtro = mainModel::limpiar_cadena($filtro);
            $registros = mainModel::limpiar_cadena($registros);
            $libro = mainModel::limpiar_cadena($libro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_certificado_controlador($this->conexion_db, $inicio, $registros, $estado, $filtro, $libro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_certificado_controlador($Certificado)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $certificado = certificadoModelo::datos_certificado_modelo($this->conexion_db, "cuenta", $Certificado);
            if ($certificado["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el certificado");
            } else {
                $stmt = certificadoModelo::eliminar_certificado_modelo($this->conexion_db, mainModel::limpiar_cadena($Certificado->getCuenta()));

                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_certificado_controlador($this->conexion_db, 0, 20, 1, ""));

                } else {
                    $insBeanCrud->setMessageServer("se elimino el certificado");
                }
                $stmt->closeCursor();
                $stmt = null;
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
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }

    public function actualizar_certificado_controlador($Certificado)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Certificado->setIdcertificado(mainModel::limpiar_cadena($Certificado->getIdcertificado()));
            $Certificado->setIndicador(mainModel::limpiar_cadena($Certificado->getIndicador()));
            $Certificado->setCuenta(mainModel::limpiar_cadena($Certificado->getCuenta()));
            $Certificado->setNombre(mainModel::limpiar_cadena($Certificado->getNombre()));
            $lcertificado = certificadoModelo::datos_certificado_modelo($this->conexion_db, "unico", $Certificado);
            if ($lcertificado["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el certificado");
            } else {
                $stmt = certificadoModelo::actualizar_certificado_modelo($this->conexion_db, $Certificado);

                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(certificadoModelo::datos_certificado_modelo($this->conexion_db, "unico-alumno", $Certificado));

                } else {
                    $insBeanCrud->setMessageServer("No hemos podido actualizar el certificado");
                }
                $stmt->closeCursor();
                $stmt = null;

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
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
}
