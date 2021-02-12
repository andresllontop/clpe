<?php

require_once './core/mainModel.php';

class testimonioModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_testimonio_modelo($conexion, $testimonio)
    {
        $sql = $conexion->prepare("INSERT INTO `testimonio`
        (titulo,descripcion,imagen,enlaceYoutube)
         VALUES(:Nombre,:Descripcion,:Imagen,:Enlace)");
        $sql->bindValue(":Nombre", $testimonio->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $testimonio->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Enlace", $testimonio->getEnlaceYoutube(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $testimonio->getImagen(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_testimonio_modelo($conexion, $tipo, $testimonio)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idtestimonio) AS CONTADOR FROM `testimonio`
                    WHERE idtestimonio=:IDtestimonio");
                    $stmt->bindValue(":IDtestimonio", $testimonio->getIdtestimonio(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `testimonio`
                            WHERE idtestimonio=:IDtestimonio");
                            $stmt->bindValue(":IDtestimonio", $testimonio->getIdtestimonio(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insTestimonio = new Testimonio();
                                $insTestimonio->setIdtestimonio($row['idtestimonio']);
                                $insTestimonio->setTitulo($row['titulo']);
                                $insTestimonio->setImagen($row['imagen']);
                                $insTestimonio->setDescripcion($row['descripcion']);
                                $insTestimonio->setArchivo($row['archivo']);
                                $insTestimonio->setEnlaceYoutube($row['enlaceYoutube']);
                                $insTestimonio->setEstado($row['estado']);
                                $insBeanPagination->setList($insTestimonio->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idtestimonio) AS CONTADOR FROM `testimonio`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `testimonio`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insTestimonio = new Testimonio();
                                $insTestimonio->setIdtestimonio($row['idtestimonio']);
                                $insTestimonio->setTitulo($row['titulo']);
                                $insTestimonio->setImagen($row['imagen']);
                                $insTestimonio->setDescripcion($row['descripcion']);
                                $insTestimonio->setArchivo($row['archivo']);
                                $insTestimonio->setEnlaceYoutube($row['enlaceYoutube']);
                                $insTestimonio->setEstado($row['estado']);
                                $insBeanPagination->setList($insTestimonio->__toString());
                            }
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
    protected function eliminar_testimonio_modelo($conexion, $id)
    {

        $sql = $conexion->prepare("DELETE FROM `testimonio` WHERE
        idtestimonio=:IDblog ");
        $sql->bindValue(":IDblog", $id, PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_testimonio_modelo($conexion, $testimonio)
    {
        $sql = $conexion->prepare("UPDATE `testimonio`
        SET titulo=:Nombre,descripcion=:Descripcion,
        imagen=:Imagen,enlaceYoutube=:Enlace
        WHERE idtestimonio=:ID");
        $sql->bindValue(":Nombre", $testimonio->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $testimonio->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Enlace", $testimonio->getEnlaceYoutube(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $testimonio->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $testimonio->getIdtestimonio(), PDO::PARAM_INT);
        return $sql;
    }

}
