<?php

require_once './modelos/librocuentaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
require_once './classes/principal/cliente.php';
require_once './classes/principal/libro.php';
class librocuentaControlador extends librocuentaModelo
{
    public function agregar_librocuenta_controlador($data)
    {
        return librocuentaModelo::agregar_librocuenta_modelo($data);
    }
    public function datos_librocuenta_controlador($tipo, $codigo)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);

            $insBeanCrud->setBeanPagination(librocuentaModelo::datos_librocuenta_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_librocuenta_controlador($conexion, $inicio, $registros, $estado, $filtro)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $contador = 0;
            $stmt = $conexion->prepare("SELECT * FROM `administrador`  as adm INNER JOIN `cuenta` AS cuent ON cuent.CuentaCodigo=adm.Cuenta_Codigo INNER JOIN `librocuenta` AS licuent ON licuent.cuenta_codigocuenta=cuent.CuentaCodigo WHERE  cuent.tipo=2 and licuent.estado_certificado=? and (adm.AdminNombre like concat('%',?,'%') OR adm.AdminApellido like concat('%',?,'%') OR adm.AdminTelefono like concat('%',?,'%') OR cuent.email like concat('%',?,'%')) ORDER BY adm.AdminNombre ASC LIMIT ?,?");
            $stmt->bindValue(1, $estado, PDO::PARAM_INT);
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
                $insLibroCuenta = new LibroCuenta();
                $insLibroCuenta->setIdlibroCuenta($row['idcuenta']);
                $insLibroCuenta->setLibro($row['libro_codigoLibro']);
                $insLibroCuenta->setCertificado($row['certificado']);
                $insLibroCuenta->setEstadoCertificado($row['estado_certificado']);
                $insLibroCuenta->setFinalizacion($row['finalizacion']);
                $insCliente = new Cliente();
                $insCuenta = new Cuenta();
                $insCuenta->setIdCuenta($row['idcuenta']);
                $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                $insCuenta->setUsuario($row['usuario']);
                $insCuenta->setClave(mainModel::decryption($row['clave']));
                $insCuenta->setEmail($row['email']);
                $insCuenta->setEstado($row['estado']);
                $insCuenta->setTipo($row['tipo']);
                $insCuenta->setFoto($row['foto']);
                $insCuenta->setPrecio($row['precio_curso']);
                $insCuenta->setVoucher($row['voucher']);

                $insCliente->setIdCliente($row['id']);
                $insCliente->setNombre($row['AdminNombre']);
                $insCliente->setTelefono($row['AdminTelefono']);
                $insCliente->setApellido($row['AdminApellido']);
                $insCliente->setOcupacion($row['AdminOcupacion']);
                $insCliente->setPais($row['pais']);
                $insCliente->setCuenta($insCuenta->__toString());
                $insLibroCuenta->setCliente($insCliente->__toString());
                $insBeanPagination->setList($insLibroCuenta->__toString());

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
    public function bean_paginador_librocuenta_controlador($pagina, $registros, $estado, $filtro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $estado = mainModel::limpiar_cadena($estado);
            $filtro = mainModel::limpiar_cadena($filtro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_librocuenta_controlador($this->conexion_db, $inicio, $registros, $estado, $filtro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }

    public function eliminar_librocuenta_controlador($codigo)
    {
        return librocuentaModelo::eliminar_librocuenta_modelo($codigo);

    }
}
