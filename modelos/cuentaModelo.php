<?php

require_once './core/mainModel.php';

class cuentaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_cuenta_modelo($conexion, $cuenta)
    {

        $sql = $conexion->prepare("INSERT INTO `cuenta`    (CuentaCodigo,usuario,clave,email,estado,tipo,voucher)
         VALUES(?,?,?,?,?,?,?)");
        $sql->bindValue(1, $cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getClave(), PDO::PARAM_STR);

        $sql->bindValue(4, $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getEstado(), PDO::PARAM_STR);
        $sql->bindValue(6, $cuenta->getTipo(), PDO::PARAM_STR);
        $sql->bindValue(7, $cuenta->getVoucher(), PDO::PARAM_STR);
        return $sql;

    }
    protected function eliminar_cuenta_modelo($conexion, $codigo)
    {
        $sql = $this->conexion_db->prepare("DELETE FROM cuenta WHERE
         cuenta_codigoCuenta=:Codigo ");
        $sql->bindValue(":Codigo", $codigo);
        $sql->execute();
        $resultado = $sql->rowCount();
        $sql->closeCursor(); //cerrar tabla virtual
        return $resultado;
        $this->conexion_db = null; //cerrar la conexion
    }
}
