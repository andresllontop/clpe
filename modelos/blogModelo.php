<?php

require_once './core/mainModel.php';

class blogModelo extends mainModel
{

    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_blog_modelo($conexion, $blog)
    {
        $sql = $conexion->prepare("INSERT INTO `blog`
            (titulo,descripcion,archivo,tipoArchivo,resumen,autor,foto,autordescripcion)
             VALUES(:Titulo,:Descripcion,:Imagen,:Tipo,:Resumen,:Autor,:Foto,:DescripAutor)");
        $sql->bindValue(":Titulo", $blog->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $blog->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $blog->getArchivo(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $blog->getTipoArchivo(), PDO::PARAM_INT);
        $sql->bindValue(":Resumen", $blog->getResumen(), PDO::PARAM_STR);
        $sql->bindValue(":Foto", $blog->getFoto(), PDO::PARAM_STR);
        $sql->bindValue(":Autor", $blog->getAutor(), PDO::PARAM_STR);
        $sql->bindValue(":DescripAutor", $blog->getDescripcionAutor(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_blog_modelo($conexion, $tipo, $blog)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idblog) AS CONTADOR FROM `blog` WHERE idblog=:IDblog");
                    $stmt->bindValue(":IDblog", $blog->getIdblog(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `blog` WHERE idblog=:IDblog");
                            $stmt->bindValue(":IDblog", $blog->getIdblog(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBlog = new Blog();
                                $insBlog->setIdBlog($row['idblog']);
                                $insBlog->setTitulo($row['titulo']);
                                $insBlog->setResumen($row['resumen']);
                                $insBlog->setAutor($row['autor']);
                                $insBlog->setFoto($row['foto']);
                                $insBlog->setDescripcionAutor($row['autordescripcion']);
                                $insBlog->setDescripcion($row['descripcion']);
                                $insBlog->setArchivo($row['archivo']);
                                $insBlog->setTipoArchivo($row['tipoArchivo']);
                                $insBlog->setComentario($row['comentario']);
                                $insBeanPagination->setList($insBlog->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idblog) AS CONTADOR FROM `blog`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `blog` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBeanPagination->setCountFilter($row['CONTADOR']);
                                if ($row['CONTADOR'] > 0) {
                                    $stmt = $conexion->prepare("SELECT * FROM `blog` WHERE idblog=:IDblog");
                                    $stmt->bindValue(":IDblog", $blog->getIdblog(), PDO::PARAM_INT);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {
                                        $insBlog = new Blog();
                                        $insBlog->setIdBlog($row['idblog']);
                                        $insBlog->setTitulo($row['titulo']);
                                        $insBlog->setDescripcionAutor($row['autordescripcion']);
                                        $insBlog->setAutor($row['autor']);
                                        $insBlog->setFoto($row['foto']);
                                        $insBlog->setResumen($row['resumen']);
                                        $insBlog->setDescripcion($row['descripcion']);
                                        $insBlog->setArchivo($row['archivo']);
                                        $insBlog->setTipoArchivo($row['tipoArchivo']);
                                        $insBlog->setComentario($row['comentario']);
                                        $insBeanPagination->setList($insBlog->__toString());
                                    }
                                }
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
    protected function eliminar_blog_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM `blog` WHERE idblog=:IDblog ");
        $sql->bindValue(":IDblog", $id, PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_blog_modelo($conexion, $blog)
    {
        $sql = $conexion->prepare("UPDATE `blog`
            SET titulo=:Titulo,descripcion=:Descripcion,
            archivo=:Imagen,tipoArchivo=:Tipo,resumen=:Resumen,autor=:Autor,
            foto=:Foto, autordescripcion=:DescripAutor
            WHERE idblog=:ID");
        $sql->bindValue(":Titulo", $blog->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $blog->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $blog->getArchivo(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $blog->getTipoArchivo(), PDO::PARAM_INT);
        $sql->bindValue(":Resumen", $blog->getResumen(), PDO::PARAM_STR);
        $sql->bindValue(":Foto", $blog->getFoto(), PDO::PARAM_STR);
        $sql->bindValue(":Autor", $blog->getAutor(), PDO::PARAM_STR);
        $sql->bindValue(":DescripAutor", $blog->getDescripcionAutor(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $blog->getIdblog(), PDO::PARAM_INT);

        return $sql;
    }

}
