<?php

    require_once './core/mainModel.php';
class declaracionModelo extends mainModel
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function agregar_declaracion_modelo($datos)
    {
        $sql = parent::__construct()->prepare("INSERT INTO audio
        (cuenta_codigo,codigo_subtitulo,nombreAudio)
         VALUES(:Codigo,:CodigoSubtitulo,:Nombre)");
        $sql->bindValue(":Codigo", $datos['Codigo']);
        $sql->bindValue(":CodigoSubtitulo", $datos['CodigoSubtitulo']);
        $sql->bindValue(":Nombre", $datos['Nombre']);

        $sql->execute();
        $resultado = $sql->rowCount();
        $sql->closeCursor(); //cerrar tabla virtual
        $this->conexion_db = null; //cerrar la conexion
        return $resultado;
    }
    protected function datos_declaracion_modelo($tipo, $codigo)
    {

        if ($tipo == "unico") {
            $sql = parent::__construct()->prepare("SELECT * FROM audio WHERE idaudio=:Codigo");
            $sql->bindValue(":Codigo", $codigo);
        } elseif ($tipo == "ultimo") {
            $sql = parent::__construct()->prepare("SELECT * FROM audio
            WHERE cuenta_codigo=:Codigo ORDER BY codigo_subtitulo DESC LIMIT 1 ");
            $sql->bindValue(":Codigo", $codigo);
        } elseif ($tipo == "conteo") {
            $sql = parent::__construct()->prepare("SELECT * FROM audio");
        }

        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
        $sql->closeCursor(); //cerrar tabla virtual
        return $resultado;
        $this->conexion_db = null;
    }
    protected function eliminar_declaracion_modelo($codigo)
    {
        $sql = parent::__construct()->prepare("DELETE FROM declaracion WHERE
        codigo=:IDdeclaracion ");
        $sql->bindValue(":IDdeclaracion", $codigo);
        $sql->execute();
        $resultado = $sql->rowCount();
        $sql->closeCursor(); //cerrar tabla virtual
        return $resultado;
        $this->conexion_db = null; //cerrar la conexion
    }

}
