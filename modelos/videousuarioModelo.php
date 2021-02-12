<?php

require_once './core/mainModel.php';

class videousuarioModelo extends mainModel
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function agregar_videousuario_modelo($datos)
    {

        $sql = parent::__construct()->prepare("INSERT INTO videousuario
        (comentario,video,subtitulo_codigosubtitulo,cuenta_codigoCuenta)
         VALUES(:Comentario,:Video,:SubTituloCodigo,:CuentaCodigo)");
        $sql->bindValue(":Comentario", $datos['Comentario']);
        $sql->bindValue(":Video", $datos['Video']);
        $sql->bindValue(":SubTituloCodigo", $datos['SubtituloCodigo']);
        $sql->bindValue(":CuentaCodigo", $datos['CuentaCodigo']);
        $sql->execute();
        $resultado = $sql->rowCount();
        $sql->closeCursor(); //cerrar tabla virtual
        $this->conexion_db = null; //cerrar la conexion
        return $resultado;
    }
    protected function agregar_videousuarioLeccion_modelo($datos)
    {
        $sql = parent::__construct()->prepare("INSERT INTO lecciones
        (subtitulo_codigosubtitulo,cuenta_codigocuenta)
         VALUES(:SubTituloCodigo,:CuentaCodigo)");
        $sql->bindValue(":SubTituloCodigo", $datos['SubtituloCodigo']);
        $sql->bindValue(":CuentaCodigo", $datos['CuentaCodigo']);
        $sql->execute();
        $resultado = $sql->rowCount();
        $sql->closeCursor(); //cerrar tabla virtual
        $this->conexion_db = null; //cerrar la conexion
        return $resultado;
    }
    protected function datos_videousuario_modelo($tipo, $codigo)
    {

        if ($tipo == "unico") {
            $sql = parent::__construct()->prepare("SELECT * FROM `videousuario`
             WHERE idvideoUsuario=:IDvideousuario");
            $sql->bindValue(":IDvideousuario", $codigo);
        } elseif ($tipo == "conteo") {
            $sql = parent::__construct()->prepare("SELECT * FROM `videousuario` ");
        }

        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
        $sql->closeCursor(); //cerrar tabla virtual
        return $resultado;
        $this->conexion_db = null;
    }
    protected function eliminar_leccion_modelo($codigo, $subtitulo)
    {
        $sql = parent::__construct()->prepare("DELETE FROM `lecciones` WHERE
        cuenta_codigocuenta=:IDvideousuario  AND subtitulo_codigosubtitulo=:Subtitulo ");
        $sql->bindValue(":IDvideousuario", $codigo);
        $sql->bindValue(":Subtitulo", $subtitulo);
        $sql->execute();
        $resultado = $sql->rowCount();
        $sql->closeCursor(); //cerrar tabla virtual
        return $resultado;
        $this->conexion_db = null; //cerrar la conexion
    }
    protected function eliminar_videousuario_modelo($codigo)
    {
        $sql = parent::__construct()->prepare("DELETE FROM `videousuario` WHERE
        idvideoUsuario=:IDvideousuario ");
        $sql->bindValue(":IDvideousuario", $codigo);
        $sql->execute();
        $resultado = $sql->rowCount();
        $sql->closeCursor(); //cerrar tabla virtual
        return $resultado;
        $this->conexion_db = null; //cerrar la conexion
    }

}
