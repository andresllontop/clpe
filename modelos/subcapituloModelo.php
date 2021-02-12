<?php

require_once './core/mainModel.php';

class subcapituloModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_subcapitulo_modelo($conexion, $SubTitulo)
    {

        $sql = $conexion->prepare("INSERT INTO `subtitulo` (codigo_subtitulo,nombre,subtituloPDF,titulo_idtitulo,subtitulo_imagen) VALUES(:Codigo,:Nombre,:PDF,:IDtitulo,:Imagen)");
        $sql->bindValue(":Nombre", $SubTitulo->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $SubTitulo->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":IDtitulo", $SubTitulo->getTitulo(), PDO::PARAM_INT);
        $sql->bindValue(":PDF", $SubTitulo->getPdf(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $SubTitulo->getImagen(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_subcapitulo_modelo($conexion, $tipo, $SubTitulo)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":

                    $stmt = $conexion->prepare("SELECT COUNT(idsubtitulo) AS CONTADOR FROM `subtitulo`
                    WHERE idsubtitulo=:IDsubtitulo");
                    $stmt->bindValue(":IDsubtitulo", $SubTitulo->getIdSubTitulo(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subtitulo` WHERE idsubtitulo=:IDsubtitulo");
                            $stmt->bindValue(":IDsubtitulo", $SubTitulo->getIdSubTitulo(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setPdf($row['subtituloPDF']);
                                $insSubTitulo->setDescripcion($row['descripcion']);
                                $insSubTitulo->setNombre($row['nombre']);
                                $insSubTitulo->setImagen($row['subtitulo_imagen']);

                                $insSubTitulo->setTitulo($row['titulo_idtitulo']);
                                $insBeanPagination->setList($insSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $registros = mainModel::limpiar_cadena($SubTitulo->getRegistro());
                    $pagina = mainModel::limpiar_cadena($SubTitulo->getPagina());
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;

                    $stmt = $conexion->prepare("SELECT COUNT(idsubtitulo) AS CONTADOR FROM `subtitulo`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subtitulo` ORDER BY codigo_subtitulo ASC LIMIT $inicio,$registros");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setPdf($row['subtituloPDF']);
                                $insSubTitulo->setDescripcion($row['descripcion']);
                                $insSubTitulo->setNombre($row['nombre']);
                                $insSubTitulo->setImagen($row['subtitulo_imagen']);
                                $insSubTitulo->setTitulo($row['titulo_idtitulo']);
                                $insBeanPagination->setList($insSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "subtitulo-titulo":

                    $stmt = $conexion->prepare("SELECT COUNT(idsubtitulo) AS CONTADOR FROM `subtitulo`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subtitulo` AS sub INNER JOIN `titulo` AS tit ON tit.idtitulo = sub.titulo_idtitulo ORDER BY sub.codigo_subtitulo");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insTitulo = new Titulo();
                                $insTitulo->setIdTitulo($row['idtitulo']);
                                $insTitulo->setCodigo($row['codigoTitulo']);
                                $insTitulo->setNombre($row['tituloNombre']);

                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setPdf($row['subtituloPDF']);
                                //$insSubTitulo->setDescripcion($row['descripcion']);
                                $insSubTitulo->setNombre($row['nombre']);
                                //$insSubTitulo->setImagen($row['subtitulo_imagen']);
                                $insSubTitulo->setTitulo($insTitulo->__toString());
                                $insBeanPagination->setList($insSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "codigo":
                    $stmt = $conexion->prepare("SELECT COUNT(idsubtitulo) AS CONTADOR FROM `subtitulo` WHERE codigo_subtitulo=:Codigo");
                    $stmt->bindValue(":Codigo", $SubTitulo->getCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subtitulo`
                            WHERE codigo_subtitulo=:Codigo");
                            $stmt->bindValue(":Codigo", $SubTitulo->getCodigo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setPdf($row['subtituloPDF']);
                                $insSubTitulo->setDescripcion($row['descripcion']);
                                $insSubTitulo->setNombre($row['nombre']);
                                $insSubTitulo->setImagen($row['subtitulo_imagen']);
                                $insSubTitulo->setTitulo($row['titulo_idtitulo']);
                                $insBeanPagination->setList($insSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "codigo-actualizar":
                    $stmt = $conexion->prepare("SELECT COUNT(idsubtitulo) AS CONTADOR FROM `subtitulo` WHERE codigo_subtitulo=:Codigo and idsubtitulo!=:ID");
                    $stmt->bindValue(":Codigo", $SubTitulo->getCodigo(), PDO::PARAM_STR);
                    $stmt->bindValue(":ID", $SubTitulo->getIdSubTitulo(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subtitulo` WHERE codigo_subtitulo=:Codigo and idsubtitulo!=:ID");
                            $stmt->bindValue(":Codigo", $SubTitulo->getCodigo(), PDO::PARAM_STR);
                            $stmt->bindValue(":ID", $SubTitulo->getIdSubTitulo(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setPdf($row['subtituloPDF']);
                                $insSubTitulo->setDescripcion($row['descripcion']);
                                $insSubTitulo->setNombre($row['nombre']);
                                $insSubTitulo->setImagen($row['subtitulo_imagen']);
                                $insSubTitulo->setTitulo($row['titulo_idtitulo']);
                                $insBeanPagination->setList($insSubTitulo->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "restriccion":
                    $stmt = $conexion->prepare("SELECT COUNT(rest.idrecurso) AS CONTADOR FROM `recurso` AS rest WHERE rest.codigo_subtitulo=:Codigo");
                    $stmt->bindValue(":Codigo", $SubTitulo->getCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT subt.* FROM `recurso` AS rest INNER JOIN `subtitulo` as subt ON rest.codigo_subtitulo=subt.codigo_subtitulo
                              WHERE rest.codigo_subtitulo=:Codigo");
                            $stmt->bindValue(":Codigo", $SubTitulo->getCodigo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insTitulo = new Titulo();
                                $insTitulo->setIdTitulo($row['titulo_idtitulo']);
                                if (isset($row['codigoTitulo'])) {
                                    $insTitulo->setCodigo($row['codigoTitulo']);
                                    $insTitulo->setPdf($row['PDF']);
                                    $insTitulo->setNombre($row['tituloNombre']);
                                    $insTitulo->setEstado($row['TituloEstado']);
                                    $insTitulo->setDescripcion($row['tituloDescripcion']);
                                }

                                $insSubTitulo = new SubTitulo();
                                $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                                $insSubTitulo->setPdf($row['subtituloPDF']);
                                $insSubTitulo->setDescripcion($row['descripcion']);
                                $insSubTitulo->setNombre($row['nombre']);
                                $insSubTitulo->setImagen($row['subtitulo_imagen']);
                                $insSubTitulo->setTitulo($insTitulo->__toString());
                                $insBeanPagination->setList($insSubTitulo->__toString());
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
    protected function eliminar_subcapitulo_modelo($conexion, $SubTitulo)
    {
        $sql = $conexion->prepare("DELETE FROM `subtitulo` WHERE
        idsubtitulo=:IDtitulo ");
        $sql->bindValue(":IDtitulo", $SubTitulo, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_subcapitulo_modelo($conexion, $SubTitulo)
    {
        $sql = $conexion->prepare("UPDATE `subtitulo` SET codigo_subtitulo=:Codigo,nombre=:Nombre,subtituloPDF=:PDF,titulo_idtitulo=:IDtitulo,subtitulo_imagen=:Imagen WHERE idsubtitulo=:ID");
        $sql->bindValue(":Nombre", $SubTitulo->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $SubTitulo->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":IDtitulo", $SubTitulo->getTitulo(), PDO::PARAM_INT);
        $sql->bindValue(":PDF", $SubTitulo->getPdf(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $SubTitulo->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $SubTitulo->getIdSubTitulo(), PDO::PARAM_INT);
        return $sql;
    }

}
