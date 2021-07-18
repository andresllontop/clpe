<?php

require_once './core/mainModel.php';

class socialModelo extends mainModel
{

    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_social_modelo($conexion, $social)
    {
        $sql = $conexion->prepare("INSERT INTO `social`
            (titulo,descripcion,frase_curso,frase_testimonio,parametro_curso,archivo,tipo_archivo,imagen_fondo)
             VALUES(:Titulo,:Descripcion,:FraseCurso,:FraseTestimonio,:ParametroCurso,:Archivo,:Tipo,:ImagenFondo)");
        $sql->bindValue(":Titulo", $social->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $social->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":FraseCurso", $social->getFraseCurso(), PDO::PARAM_STR);
        $sql->bindValue(":FraseTestimonio", $social->getFraseTestimonio(), PDO::PARAM_STR);
        $sql->bindValue(":ParametroCurso", $social->getParametroCurso(), PDO::PARAM_STR);
        $sql->bindValue(":ImagenFondo", $social->getImagenFondo(), PDO::PARAM_STR);
        $sql->bindValue(":Archivo", $social->getArchivo(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $social->getTipoArchivo(), PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_social_modelo($conexion, $tipo, $social)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idsocial) AS CONTADOR FROM `social` WHERE idsocial=:IDsocial");
                    $stmt->bindValue(":IDsocial", $social->getIdsocial(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `social` WHERE idsocial=:IDsocial");
                            $stmt->bindValue(":IDsocial", $social->getIdsocial(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insSocial = new Social();
                                $insSocial->setIdSocial($row['idsocial']);
                                $insSocial->setTitulo($row['titulo']);
                                $insSocial->setDescripcion($row['descripcion']);
                                $insSocial->setArchivo($row['archivo']);
                                $insSocial->setParametroCurso($row['parametro_curso']);
                                $insSocial->setFraseCurso($row['frase_curso']);
                                $insSocial->setFraseTestimonio($row['frase_testimonio']);
                                $insSocial->setImagenFondo($row['imagen_fondo']);
                                $insSocial->setTipoArchivo($row['tipo_archivo']);
                                $insBeanPagination->setList($insSocial->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idsocial) AS CONTADOR FROM `social`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `social` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBeanPagination->setCountFilter($row['CONTADOR']);
                                if ($row['CONTADOR'] > 0) {
                                    $stmt = $conexion->prepare("SELECT * FROM `social` WHERE idsocial=:IDsocial");
                                    $stmt->bindValue(":IDsocial", $social->getIdsocial(), PDO::PARAM_INT);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {
                                        $insSocial = new Social();
                                        $insSocial->setIdSocial($row['idsocial']);
                                        $insSocial->setTitulo($row['titulo']);
                                        $insSocial->setDescripcion($row['descripcion']);
                                        $insSocial->setArchivo($row['archivo']);
                                        $insSocial->setParametroCurso($row['parametro_curso']);
                                        $insSocial->setFraseCurso($row['frase_curso']);
                                        $insSocial->setFraseTestimonio($row['frase_testimonio']);
                                        $insSocial->setImagenFondo($row['imagen_fondo']);
                                        $insSocial->setTipoArchivo($row['tipo_archivo']);
                                        $insBeanPagination->setList($insSocial->__toString());
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
    protected function eliminar_social_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM `social` WHERE idsocial=:IDsocial ");
        $sql->bindValue(":IDsocial", $id, PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_social_modelo($conexion, $social)
    {
        $sql = $conexion->prepare("UPDATE `social`
            SET titulo=:Titulo,descripcion=:Descripcion,frase_curso=:FraseCurso,
            frase_testimonio=:FraseTestimonio,parametro_curso=:ParametroCurso,
            imagen_fondo=:ImagenFondo,
            archivo=:Archivo,tipo_archivo=:Tipo WHERE idsocial=:ID");
        $sql->bindValue(":Titulo", $social->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $social->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":FraseCurso", $social->getFraseCurso(), PDO::PARAM_STR);
        $sql->bindValue(":FraseTestimonio", $social->getFraseTestimonio(), PDO::PARAM_STR);
        $sql->bindValue(":ParametroCurso", $social->getParametroCurso(), PDO::PARAM_STR);
        $sql->bindValue(":Archivo", $social->getArchivo(), PDO::PARAM_STR);
        $sql->bindValue(":ImagenFondo", $social->getImagenFondo(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $social->getTipoArchivo(), PDO::PARAM_INT);
        $sql->bindValue(":ID", $social->getIdsocial(), PDO::PARAM_INT);

        return $sql;
    }

}
