<?php

require_once './core/mainModel.php';

class subitemModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_subitem_modelo($conexion, $SubItem)
    {
        $sql = $conexion->prepare("INSERT INTO `subitem` (titulo,detalle,item,imagen,idcurso)
        VALUES(:Titulo,:Detalle,:Tipo,:Imagen,:Curso)");
        $sql->bindValue(":Detalle", $SubItem->getDetalle(), PDO::PARAM_STR);
        $sql->bindValue(":Titulo", $SubItem->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $SubItem->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $SubItem->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Curso", $SubItem->getCurso(), PDO::PARAM_INT);

        return $sql;
    }
    protected function datos_subitem_modelo($conexion, $tipo, $subitem)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idsubitem) AS CONTADOR FROM `subitem` WHERE idsubitem=:IDempresa");
                    $stmt->bindValue(":IDempresa", $subitem->getIdsubitem(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subitem` WHERE idsubitem=:IDempresa");
                            $stmt->bindValue(":IDempresa", $subitem->getIdsubitem(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insSubItem = new SubItem();
                                $insSubItem->setIdsubitem($row['idsubitem']);
                                $insSubItem->setTitulo($row['titulo']);
                                $insSubItem->setDetalle($row['detalle']);
                                $insSubItem->setImagen($row['imagen']);
                                $insSubItem->setTipo($row['item']);
                                $insSubItem->setCurso($row['idcurso']);
                                $insBeanPagination->setList($insSubItem->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "tipo":
                    $stmt = $conexion->prepare("SELECT COUNT(idsubitem) AS CONTADOR FROM `subitem` WHERE item=:IDempresa");
                    $stmt->bindValue(":IDempresa", $subitem->getTipo(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subitem` WHERE item=:IDempresa");
                            $stmt->bindValue(":IDempresa", $subitem->getTipo(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insSubItem = new SubItem();
                                $insSubItem->setIdsubitem($row['idsubitem']);
                                $insSubItem->setTitulo($row['titulo']);
                                $insSubItem->setDetalle($row['detalle']);
                                $insSubItem->setImagen($row['imagen']);
                                $insSubItem->setTipo($row['item']);
                                $insSubItem->setCurso($row['idcurso']);
                                $insBeanPagination->setList($insSubItem->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "curso":
                    $stmt = $conexion->prepare("SELECT COUNT(idsubitem) AS CONTADOR FROM `subitem` WHERE idcurso=:Curso and item=:IDempresa");
                    $stmt->bindValue(":IDempresa", $subitem->getTipo(), PDO::PARAM_INT);
                    $stmt->bindValue(":Curso", $subitem->getCurso(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subitem` WHERE idcurso=:Curso and item=:IDempresa");
                            $stmt->bindValue(":IDempresa", $subitem->getTipo(), PDO::PARAM_INT);
                            $stmt->bindValue(":Curso", $subitem->getCurso(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insSubItem = new SubItem();
                                $insSubItem->setIdsubitem($row['idsubitem']);
                                $insSubItem->setTitulo($row['titulo']);
                                $insSubItem->setDetalle($row['detalle']);
                                $insSubItem->setImagen($row['imagen']);
                                $insSubItem->setTipo($row['item']);
                                $insSubItem->setCurso($row['idcurso']);

                                $insBeanPagination->setList($insSubItem->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idsubitem) AS CONTADOR FROM `idsubitem` ");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subitem`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insSubItem = new SubItem();
                                $insSubItem->setIdsubitem($row['idsubitem']);
                                $insSubItem->setTitulo($row['titulo']);
                                $insSubItem->setDetalle($row['detalle']);
                                $insSubItem->setImagen($row['imagen']);
                                $insSubItem->setTipo($row['item']);

                                $insBeanPagination->setList($insSubItem->__toString());
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
    protected function actualizar_subitem_modelo($conexion, $SubItem)
    {
        $sql = $conexion->prepare("UPDATE `subitem`
        SET titulo=:Titulo,detalle=:Detalle,imagen=:Imagen,item=:Tipo,idcurso=:Curso
        WHERE idsubitem=:ID");
        $sql->bindValue(":Detalle", $SubItem->getDetalle(), PDO::PARAM_STR);
        $sql->bindValue(":Titulo", $SubItem->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $SubItem->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $SubItem->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":ID", $SubItem->getIdsubitem(), PDO::PARAM_INT);
        $sql->bindValue(":Curso", $SubItem->getCurso(), PDO::PARAM_INT);

        return $sql;
    }
    protected function eliminar_subitem_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `subitem` WHERE
        idsubitem=:IDtitulo ");
        $sql->bindValue(":IDtitulo", $codigo);

        return $sql;
    }

}
