<?php

require_once './core/mainModel.php';

class resumenModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_resumen_modelo($conexion, $datos)
    {
        $sql = $conexion->prepare("INSERT INTO resumen
        (idresumen,descripcion,subtitulo_idsubtitulo,libro_idlibro)
         VALUES(:ID,:Descripcion,:IDsubtitulo,:codigoCuenta)");
        $sql->bindValue(":ID", $datos['ID']);
        $sql->bindValue(":Descripcion", $datos['Descripcion']);
        $sql->bindValue(":IDsubtitulo", $datos['IDsubtitulo']);
        $sql->bindValue(":codigoCuenta", $datos['codigoCuenta']);
        return $sql;
    }
    protected function datos_resumen_modelo($conexion, $tipo, $codigo)
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
    protected function eliminar_resumen_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM resumen WHERE
        idresumen=:IDresumen ");
        $sql->bindValue(":IDresumen", $codigo);
        return $sql;
    }

}
