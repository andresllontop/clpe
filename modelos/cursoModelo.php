<?php

require_once './core/mainModel.php';

class cursoModelo extends mainModel
{

    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_curso_modelo($conexion, $curso)
    {

        $sql = $conexion->prepare("INSERT INTO `curso`
            (titulo,descripcion,precio,tipo,precio_descuento,imagen,portada,presentacion,video)
             VALUES(:Titulo,:Descripcion,:Precio,:Tipo,:Descuento,:Imagen,:Portada,:Presentacion,:Video)");
        $sql->bindValue(":Titulo", $curso->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $curso->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $curso->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Presentacion", $curso->getPresentacion(), PDO::PARAM_STR);
        $sql->bindValue(":Portada", $curso->getPortada(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $curso->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Precio", $curso->getPrecio(), PDO::PARAM_STR);
        $sql->bindValue(":Descuento", $curso->getDescuento(), PDO::PARAM_STR);
        $sql->bindValue(":Video", $curso->getVideo(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_curso_modelo($conexion, $tipo, $curso)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":

                    $stmt = $conexion->prepare("SELECT COUNT(idcurso) AS CONTADOR FROM `curso` WHERE idcurso=:IDcurso");
                    $stmt->bindValue(":IDcurso", $curso->getIdcurso(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `curso` WHERE idcurso=:IDcurso");
                            $stmt->bindValue(":IDcurso", $curso->getIdcurso(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCurso = new Curso();
                                $insCurso->setIdCurso($row['idcurso']);
                                $insCurso->setTitulo($row['titulo']);
                                $insCurso->setPrecio($row['precio']);
                                $insCurso->setDescripcion($row['descripcion']);
                                $insCurso->setPresentacion($row['presentacion']);
                                $insCurso->setDescuento($row['precio_descuento']);
                                $insCurso->setLibro($row['codigo_libro']);
                                $insCurso->setVideo($row['video']);
                                //TIPO=1 PAGADO ; TIPO=2 MEDIANTE ZOOM;
                                $insCurso->setTipo($row['tipo']);
                                $insCurso->setImagen($row['imagen']);
                                $insCurso->setPortada($row['portada']);
                                $insBeanPagination->setList($insCurso->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idcurso) AS CONTADOR FROM `curso`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `curso` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCurso = new Curso();
                                $insCurso->setIdCurso($row['idcurso']);
                                $insCurso->setTitulo($row['titulo']);
                                $insCurso->setPrecio($row['precio']);
                                $insCurso->setDescripcion($row['descripcion']);
                                $insCurso->setPresentacion($row['presentacion']);
                                $insCurso->setDescuento($row['precio_descuento']);
                                $insCurso->setVideo($row['video']);
                                //TIPO=1 PAGADO ; TIPO=2 MEDIANTE ZOOM;
                                $insCurso->setTipo($row['tipo']);
                                $insCurso->setImagen($row['imagen']);
                                $insCurso->setPortada($row['portada']);
                                $insBeanPagination->setList($insCurso->__toString());
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
    protected function eliminar_curso_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM `curso` WHERE idcurso=:IDcurso ");
        $sql->bindValue(":IDcurso", $id, PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_curso_modelo($conexion, $curso)
    {

        $sql = $conexion->prepare("UPDATE `curso`
            SET titulo=:Titulo,descripcion=:Descripcion,
            imagen=:Imagen,tipo=:Tipo,precio=:Precio,precio_descuento=:Descuento,presentacion=:Presentacion,
            portada=:Portada,video=:Video WHERE idcurso=:ID");
        $sql->bindValue(":Titulo", $curso->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $curso->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $curso->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Portada", $curso->getPortada(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $curso->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Presentacion", $curso->getPresentacion(), PDO::PARAM_STR);
        $sql->bindValue(":Precio", $curso->getPrecio(), PDO::PARAM_STR);
        $sql->bindValue(":Descuento", $curso->getDescuento(), PDO::PARAM_STR);
        $sql->bindValue(":Video", $curso->getVideo(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $curso->getIdcurso(), PDO::PARAM_INT);

        return $sql;
    }

}
