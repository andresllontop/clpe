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
