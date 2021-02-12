<?php

require_once './core/mainModel.php';

class noticiaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_noticia_modelo($conexion, $Noticia)
    {
        $sql = mainModel::__construct()->prepare("INSERT INTO
        `noticia`(titulo,descripcion,imagen)
        VALUES(:Titulo,:Descripcion,:Imagen)");
        $sql->bindValue(":Imagen", $Noticia->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Titulo", $Noticia->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $Noticia->getDescripcion(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_noticia_modelo($conexion, $tipo, $noticia)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idnoticia) AS CONTADOR FROM `noticia` WHERE idnoticia=:IDnoticia");
                    $stmt->bindValue(":IDnoticia", $noticia->getIdnoticia(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `noticia`
                            WHERE idnoticia=:IDnoticia");
                            $stmt->bindValue(":IDnoticia", $noticia->getIdnoticia(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insNoticia = new Noticia();
                                $insNoticia->setIdnoticia($row['idnoticia']);
                                $insNoticia->setDescripcion($row['descripcion']);
                                $insNoticia->setTitulo($row['titulo']);
                                $insNoticia->setImagen($row['imagen']);

                                $insBeanPagination->setList($insNoticia->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idnoticia) AS CONTADOR FROM `noticia`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `noticia`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insNoticia = new Noticia();
                                $insNoticia->setIdnoticia($row['idnoticia']);
                                $insNoticia->setDescripcion($row['descripcion']);
                                $insNoticia->setTitulo($row['titulo']);
                                $insNoticia->setImagen($row['imagen']);
                                $insBeanPagination->setList($insNoticia->__toString());
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
    protected function eliminar_noticia_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM
     `noticia` WHERE   idnoticia=:ID ");
        $sql->bindValue(":ID", $id, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_noticia_modelo($conexion, $Noticia)
    {
        $sql = $conexion->prepare("UPDATE `noticia`
        SET imagen=:Imagen,titulo=:Titulo,descripcion=:Descripcion WHERE idnoticia=:ID");
        $sql->bindValue(":Imagen", $Noticia->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Titulo", $Noticia->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $Noticia->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $Noticia->getIdnoticia(), PDO::PARAM_INT);
        return $sql;
    }

}
