<?php

require_once './core/mainModel.php';

class recursoModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_recurso_modelo($conexion, $Recurso)
    {
        $sql = $conexion->prepare("INSERT INTO `recurso`
            (codigo_subtitulo,nombre,disponible,imagen) VALUES(:Codigo,:Nombre,:Disponible,:Imagen)");
        $sql->bindValue(":Nombre", $Recurso->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $Recurso->getSubTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $Recurso->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Disponible", $Recurso->getDisponible(), PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_recurso_modelo($conexion, $tipo, $Recurso)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idrecurso) AS CONTADOR FROM `recurso` WHERE idrecurso=:Codigo");
                    $stmt->bindValue(":Codigo", $Recurso->getIdRecurso(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `recurso` WHERE idrecurso=:Codigo");
                            $stmt->bindValue(":Codigo", $Recurso->getIdRecurso(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insRecurso = new Recurso();
                                $insRecurso->setIdRecurso($row['idrecurso']);
                                $insRecurso->setImagen($row['imagen']);
                                $insRecurso->setNombre($row['nombre']);
                                $insRecurso->setDisponible($row['disponible']);

                                $insBeanPagination->setList($insRecurso->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":

                    $pagina = mainModel::limpiar_cadena((int) $Recurso->getPagina());
                    $registros = mainModel::limpiar_cadena((int) $Recurso->getRegistro());
                    $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
                    $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
                    $stmt = $conexion->prepare("SELECT MAX(subtitulo_codigosubtitulo) AS CONTADOR FROM `lecciones` WHERE cuenta_codigocuenta = :Cuenta AND subtitulo_codigosubtitulo LIKE CONCAT('%',:Code,'%')");
                    $stmt->bindValue(":Cuenta", $Recurso->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(":Code", $Recurso->getSubTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos1 = $stmt->fetchAll();

                    foreach ($datos1 as $row1) {

                        if ($row1['CONTADOR'] != null) {
                            $array = explode(".", $row1['CONTADOR']);
                            //BUSCAR EL MAXIMO SUBTIULO del titulo
                            $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS MAXIMO FROM `subtitulo` WHERE codigo_subtitulo  LIKE CONCAT('%',:Code,'%')");
                            $stmt->bindValue(":Code", $Recurso->getSubTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $variable = $stmt->fetchAll();

                            foreach ($variable as $row4) {

                                if ($row4['MAXIMO'] == $row1['CONTADOR']) {
                                    //aumenta titulo
                                    $array[2]++;
                                    if (strlen($array[2]) == 1) {
                                        $array[2] = "0" . $array[2];
                                    }
                                    $array[3] = "01";
                                } else {
                                    //aumenta subtitulo
                                    $array[3]++;
                                    if (strlen($array[3]) == 1) {
                                        $array[3] = "0" . $array[3];
                                    }
                                }

                            }

                            $row1['CONTADOR'] = $array[0] . "." . $array[1] . "." . $array[2] . "." . $array[3];
                            $stmt = $conexion->prepare("SELECT COUNT(idrecurso) AS CONTADOR FROM `recurso` WHERE codigo_subtitulo LIKE CONCAT('%',:Code,'%')");
                            $stmt->bindValue(":Code", $Recurso->getSubTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();

                            foreach ($datos as $row) {
                                $insBeanPagination->setCountFilter($row['CONTADOR']);
                                if ($row['CONTADOR'] > 0) {
                                    $stmt = $conexion->prepare("SELECT b.*,s.nombre AS nombre_subtitulo,s.idsubtitulo FROM `recurso` AS b inner join `subtitulo` AS s ON s.codigo_subtitulo=b.codigo_subtitulo  WHERE(b.codigo_subtitulo <=? and b.disponible=1) or (b.codigo_subtitulo =? and b.disponible=0) AND b.codigo_subtitulo LIKE CONCAT('%',?,'%') ORDER BY  b.codigo_subtitulo  ASC LIMIT ?,?");
                                    $stmt->bindValue(1, $row1['CONTADOR'], PDO::PARAM_STR);
                                    $stmt->bindValue(2, $row1['CONTADOR'], PDO::PARAM_STR);
                                    $stmt->bindValue(3, $Recurso->getSubTitulo(), PDO::PARAM_STR);
                                    $stmt->bindValue(4, $inicio, PDO::PARAM_INT);
                                    $stmt->bindValue(5, $registros, PDO::PARAM_INT);

                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {

                                        $insSubTitulo = new SubTitulo();
                                        $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                        $insSubTitulo->setNombre($row['nombre_subtitulo']);
                                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);

                                        $insRecurso = new Recurso();
                                        $insRecurso->setIdRecurso($row['idrecurso']);
                                        $insRecurso->setImagen($row['imagen']);
                                        $insRecurso->setNombre($row['nombre']);

                                        $insRecurso->setSubTitulo($insSubTitulo->__toString());
                                        $insBeanPagination->setList($insRecurso->__toString());
                                    }
                                }
                            }
                        } else {
                            //BUSCAR EL minimo SUBTIULO del libro
                            $stmt = $this->conexion_db->prepare("SELECT MIN(codigo_subtitulo) AS MAXIMO FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
                            $stmt->bindValue(":IDlibro", $Recurso->getSubTitulo() . ".N01.", PDO::PARAM_STR);
                            $stmt->execute();
                            $variable = $stmt->fetchAll();
                            foreach ($variable as $row4) {
                                $row1['CONTADOR'] = $row4['MAXIMO'];
                            }
                            $stmt = $conexion->prepare("SELECT COUNT(idrecurso) AS CONTADOR FROM `recurso` WHERE codigo_subtitulo LIKE CONCAT('%',:Code,'%')");
                            $stmt->bindValue(":Code", $Recurso->getSubTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBeanPagination->setCountFilter($row['CONTADOR']);
                                if ($row['CONTADOR'] > 0) {
                                    $stmt = $conexion->prepare("SELECT b.*,s.nombre AS nombre_subtitulo,s.idsubtitulo FROM `recurso` AS b inner join `subtitulo` AS s ON s.codigo_subtitulo=b.codigo_subtitulo  WHERE(b.codigo_subtitulo <=? and b.disponible=1) or (b.codigo_subtitulo =? and b.disponible=0) AND b.codigo_subtitulo LIKE CONCAT('%',?,'%') ORDER BY  b.codigo_subtitulo  ASC LIMIT ?,?");
                                    $stmt->bindValue(1, $row1['CONTADOR'], PDO::PARAM_STR);
                                    $stmt->bindValue(2, $row1['CONTADOR'], PDO::PARAM_STR);
                                    $stmt->bindValue(3, $Recurso->getSubTitulo(), PDO::PARAM_STR);
                                    $stmt->bindValue(4, $inicio, PDO::PARAM_INT);
                                    $stmt->bindValue(5, $registros, PDO::PARAM_INT);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {

                                        $insSubTitulo = new SubTitulo();
                                        $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                                        $insSubTitulo->setNombre($row['nombre_subtitulo']);
                                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);

                                        $insRecurso = new Recurso();
                                        $insRecurso->setIdRecurso($row['idrecurso']);
                                        $insRecurso->setImagen($row['imagen']);
                                        $insRecurso->setNombre($row['nombre']);

                                        $insRecurso->setSubTitulo($insSubTitulo->__toString());
                                        $insBeanPagination->setList($insRecurso->__toString());
                                    }
                                }
                            }
                        }
                    }

                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "detalle":
                    $stmt = $conexion->prepare("SELECT COUNT(iddetalle_recurso) AS CONTADOR FROM `detalle_recurso` WHERE idrecurso=:Codigo");
                    $stmt->bindValue(":Codigo", $Recurso->getIdRecurso(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT r.* FROM `detalle_recurso` AS c INNER JOIN `recurso` AS r ON c.idrecurso=r.idrecurso WHERE c.idrecurso=:Codigo");
                            $stmt->bindValue(":Codigo", $Recurso->getIdRecurso(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insRecurso = new Recurso();
                                $insRecurso->setIdRecurso($row['idrecurso']);
                                $insRecurso->setImagen($row['imagen']);
                                $insRecurso->setNombre($row['nombre']);
                                $insRecurso->setDisponible($row['disponible']);

                                $insBeanPagination->setList($insRecurso->__toString());
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
    protected function eliminar_recurso_modelo($conexion, $Recurso)
    {
        $sql = $conexion->prepare("DELETE FROM `recurso`
            WHERE idrecurso=:ID ");
        $sql->bindValue(":ID", $Recurso, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_recurso_modelo($conexion, $Recurso)
    {
        $sql = $conexion->prepare("UPDATE `recurso` SET
        codigo_subtitulo=:Codigo,imagen=:Imagen,nombre=:Nombre,disponible=:Disponible
         WHERE idrecurso=:ID ");
        $sql->bindValue(":Nombre", $Recurso->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $Recurso->getSubTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $Recurso->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Disponible", $Recurso->getDisponible(), PDO::PARAM_INT);
        $sql->bindValue(":ID", $Recurso->getIdRecurso(), PDO::PARAM_INT);
        return $sql;
    }

}
