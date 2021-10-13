<?php

require_once './core/mainModel.php';

class librocuentaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_librocuenta_modelo($conexion, $datos)
    {
        $sql = $conexion->prepare("INSERT INTO `librocuenta`
        (libro_codigoLibro,cuenta_codigocuenta)
         VALUES(:CodigoLibro,:codigoCuenta)");
        $sql->bindValue(1, $datos['Libro'], PDO::PARAM_STR);
        $sql->bindValue(2, $datos['Cuenta'], PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_librocuenta_modelo($conexion, $tipo, $codigo)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "alumno":
                    $stmt = $conexion->prepare("SELECT COUNT(idlibroCuenta) AS CONTADOR FROM `librocuenta` WHERE cuenta_codigocuenta=:Codigo");
                    $stmt->bindValue(":Codigo", $codigo->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {

                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT lib.* FROM `librocuenta` AS lib_cuent INNER JOIN `libro` AS lib ON lib_cuent.libro_codigolibro=lib.codigo
                            WHERE lib_cuent.cuenta_codigocuenta=:Codigo");
                            $stmt->bindValue(":Codigo", $codigo->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insLibroCuenta = new LibroCuenta();
                                $insLibro = new Libro();
                                $insLibro->setIdLibro($row['idlibro']);
                                $insLibro->setCodigo($row['codigo']);
                                $insLibro->setNombre($row['nombre']);
                                $insLibro->setDescripcion($row['descripcion']);
                                $insLibro->setImagen($row['imagen']);

                                $insLibroCuenta->setLibro($insLibro->__toString());
                                $insBeanPagination->setList($insLibroCuenta->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "unico":

                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {

                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":

                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {

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
    protected function eliminar_librocuenta_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `librocuenta` WHERE
        cuenta_codigocuenta=? ");
        $sql->bindValue(1, $codigo);
        return $sql;
    }

}
