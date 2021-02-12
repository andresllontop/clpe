<?php

require_once './core/mainModel.php';

class capituloModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_capitulo_modelo($conexion, $Capitulo)
    {
        $sql = $conexion->prepare("INSERT INTO `titulo`
        (codigoTitulo,tituloNombre,libro_codigoLibro,titulo_imagen)
         VALUES(:Codigo,:Nombre,:IDlibro,:Imagen)");
        $sql->bindValue(":Nombre", $Capitulo->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $Capitulo->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":IDlibro", $Capitulo->getLibro(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $Capitulo->getImagen(), PDO::PARAM_STR);

        return $sql;
    }
    protected function datos_capitulo_modelo($conexion, $tipo, $capitulo)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idtitulo) AS CONTADOR FROM `titulo`  WHERE idtitulo=:IDtitulo");
                    $stmt->bindValue(":IDtitulo", $capitulo->getIdTitulo(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `titulo` WHERE idtitulo=:IDtitulo");
                            $stmt->bindValue(":IDtitulo", $capitulo->getIdTitulo(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insLibro = new Libro();
                                $insLibro->setCodigo($row['libro_codigoLibro']);

                                $insCapitulo = new Titulo();
                                $insCapitulo->setIdTitulo($row['idtitulo']);
                                $insCapitulo->setCodigo($row['codigoTitulo']);
                                $insCapitulo->setPdf($row['PDF']);
                                $insCapitulo->setDescripcion($row['tituloDescripcion']);
                                $insCapitulo->setEstado($row['TituloEstado']);
                                $insCapitulo->setNombre($row['tituloNombre']);
                                $insCapitulo->setImagen($row['titulo_imagen']);

                                $insCapitulo->setLibro($insLibro->__toString());
                                $insBeanPagination->setList($insCapitulo->__toString());
                            }

                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $registros = mainModel::limpiar_cadena($capitulo->getRegistro());
                    $pagina = mainModel::limpiar_cadena($capitulo->getPagina());
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;

                    $stmt = $conexion->prepare("SELECT COUNT(idtitulo) AS CONTADOR FROM `titulo`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `titulo` ORDER BY codigoTitulo ASC LIMIT $inicio,$registros");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insLibro = new Libro();
                                $insLibro->setCodigo($row['libro_codigoLibro']);

                                $insCapitulo = new Titulo();
                                $insCapitulo->setIdTitulo($row['idtitulo']);
                                $insCapitulo->setCodigo($row['codigoTitulo']);
                                $insCapitulo->setPdf($row['PDF']);
                                $insCapitulo->setDescripcion($row['tituloDescripcion']);
                                $insCapitulo->setEstado($row['TituloEstado']);
                                $insCapitulo->setNombre($row['tituloNombre']);
                                $insCapitulo->setImagen($row['titulo_imagen']);
                                $insCapitulo->setLibro($insLibro->__toString());
                                $insBeanPagination->setList($insCapitulo->__toString());
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
    protected function eliminar_capitulo_modelo($conexion, $codigo)
    {

        $sql = $conexion->prepare("DELETE FROM `titulo` WHERE
            idtitulo=:IDtitulo ");
        $sql->bindValue(":IDtitulo", $codigo, PDO::PARAM_INT);
        return $sql;
    }

    protected function actualizar_capitulo_modelo($conexion, $Capitulo)
    {

        $sql = $conexion->prepare("UPDATE `titulo`
            SET codigoTitulo=:Codigo,tituloNombre=:Nombre,libro_codigoLibro=:CodigoLibro, titulo_imagen=:Imagen WHERE idtitulo=:ID");
        $sql->bindValue(":Nombre", $Capitulo->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $Capitulo->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":CodigoLibro", $Capitulo->getLibro(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $Capitulo->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $Capitulo->getIdTitulo(), PDO::PARAM_INT);

        return $sql;
    }

}
