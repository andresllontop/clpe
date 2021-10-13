<?php

require_once './core/mainModel.php';

class libroModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_libro_modelo($conexion, $Libro)
    {

        $sql = $conexion->prepare("INSERT INTO `libro`
            (codigo,nombre,imagen,idcategoria)
             VALUES(:Codigo,:Nombre,:Imagen,:IDcategoria)");
        $sql->bindValue(":Nombre", $Libro->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $Libro->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":IDcategoria", $Libro->getCategoria(), PDO::PARAM_INT);
        $sql->bindValue(":Imagen", $Libro->getImagen(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_libro_modelo($conexion, $tipo, $libro)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idlibro) AS CONTADOR FROM `libro`
                    WHERE idlibro=:Codigo");
                    $stmt->bindValue(":Codigo", $libro->getIdlibro(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `libro`
                            WHERE idlibro=:IDlibro");
                            $stmt->bindValue(":IDlibro", $libro->getIdlibro(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insCategoria = new Categoria();
                                $insCategoria->setIdcategoria($row['idcategoria']);
                                $insLibro = new Libro();
                                $insLibro->setIdLibro($row['idlibro']);
                                $insLibro->setCodigo($row['codigo']);
                                $insLibro->setNombre($row['nombre']);
                                $insLibro->setImagenOtro($row['desImagen']);
                                $insLibro->setImagen($row['imagen']);
                                $insLibro->setEstado($row['estado']);
                                $insLibro->setDescripcion($row['descripcion']);

                                $insLibro->setCategoria($insCategoria->__toString());
                                $insBeanPagination->setList($insLibro->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idlibro) AS CONTADOR FROM `libro`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `libro` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insCategoria = new Categoria();
                                $insCategoria->setIdcategoria($row['idcategoria']);
                                $insLibro = new Libro();
                                $insLibro->setIdLibro($row['idlibro']);
                                $insLibro->setCodigo($row['codigo']);
                                $insLibro->setNombre($row['nombre']);
                                $insLibro->setImagenOtro($row['desImagen']);
                                $insLibro->setImagen($row['imagen']);
                                $insLibro->setEstado($row['estado']);
                                $insLibro->setDescripcion($row['descripcion']);

                                $insLibro->setCategoria($insCategoria->__toString());
                                $insBeanPagination->setList($insLibro->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "cuenta":

                    $stmt = $conexion->prepare("SELECT count(idlecciones) AS CONTADOR FROM `lecciones`  WHERE cuenta_codigocuenta=:Cuenta AND subtitulo_codigosubtitulo LIKE CONCAT('%',:Libro,'%')");
                    $stmt->bindValue(":Cuenta", $libro->getCuenta(), PDO::PARAM_STR);
                    $stmt->bindValue(":Libro", $libro->getCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT MAX(subtitulo_codigosubtitulo) AS subtitulo_codigosubtitulo FROM `lecciones` WHERE cuenta_codigocuenta=:Cuenta AND subtitulo_codigosubtitulo LIKE CONCAT('%',:Libro,'%')");
                            $stmt->bindValue(":Cuenta", $libro->getCuenta(), PDO::PARAM_STR);
                            $stmt->bindValue(":Libro", $libro->getCodigo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($datos as $row1) {
                                if ($row1['subtitulo_codigosubtitulo'] != null && $row1['subtitulo_codigosubtitulo'] != "") {
                                    $stmt = $conexion->prepare("SELECT COUNT(idalbum) AS CONTADOR FROM `album` WHERE (:Subtitulo between desde and hasta) and tipo=1 AND desde LIKE CONCAT('%',:LibroDesde,'%') AND hasta LIKE CONCAT('%',:LibroHasta,'%')");
                                    $stmt->bindValue(":Subtitulo", $row1['subtitulo_codigosubtitulo'], PDO::PARAM_STR);
                                    $stmt->bindValue(":LibroDesde", $libro->getCodigo(), PDO::PARAM_STR);
                                    $stmt->bindValue(":LibroHasta", $libro->getCodigo(), PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {
                                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                                        if ($row['CONTADOR'] > 0) {
                                            $stmt = $conexion->prepare("SELECT COUNT(idlibroCuenta) AS CONTADOR FROM `librocuenta` WHERE cuenta_codigocuenta=:Cuenta AND libro_codigolibro LIKE CONCAT('%',:Libro,'%')");
                                            $stmt->bindValue(":Cuenta", $libro->getCuenta(), PDO::PARAM_STR);
                                            $stmt->bindValue(":Libro", $libro->getCodigo(), PDO::PARAM_STR);
                                            $stmt->execute();
                                            $datos = $stmt->fetchAll();
                                            foreach ($datos as $row) {
                                                $insBeanPagination->setCountFilter($row['CONTADOR']);
                                                if ($row['CONTADOR'] > 0) {
                                                    $stmt = $conexion->prepare("SELECT lib.* FROM `librocuenta` AS libcue INNER JOIN `libro` AS lib ON lib.codigo=libcue.libro_codigoLibro WHERE libcue.cuenta_codigocuenta=:Cuenta AND libro_codigolibro LIKE CONCAT('%',:Libro,'%')");
                                                    $stmt->bindValue(":Cuenta", $libro->getCuenta(), PDO::PARAM_STR);
                                                    $stmt->bindValue(":Libro", $libro->getCodigo(), PDO::PARAM_STR);
                                                    $stmt->execute();
                                                    $datos = $stmt->fetchAll();
                                                    $insLibro = new Libro();
                                                    foreach ($datos as $row) {
                                                        $insLibro->setIdLibro($row['idlibro']);
                                                        $insLibro->setCodigo($row['codigo']);
                                                        $insLibro->setNombre($row['nombre']);
                                                        $insLibro->setImagenOtro($row['desImagen']);
                                                        $insLibro->setImagen($row['imagen']);
                                                        $insLibro->setEstado($row['estado']);
                                                        $insLibro->setDescripcion($row['descripcion']);
                                                        $insLibro->setCategoria($row['idcategoria']);

                                                    }
                                                    $stmt = $conexion->prepare("SELECT video,nombre FROM `album` WHERE (:Subtitulo between desde and hasta) and tipo=1 AND desde LIKE CONCAT('%',:LibroDesde,'%') AND hasta LIKE CONCAT('%',:LibroHasta,'%')");
                                                    $stmt->bindValue(":Subtitulo", $row1['subtitulo_codigosubtitulo'], PDO::PARAM_STR);
                                                    $stmt->bindValue(":LibroDesde", $libro->getCodigo(), PDO::PARAM_STR);
                                                    $stmt->bindValue(":LibroHasta", $libro->getCodigo(), PDO::PARAM_STR);
                                                    $stmt->execute();
                                                    $datos = $stmt->fetchAll();
                                                    foreach ($datos as $row) {
                                                        $insLibro->setList(
                                                            array("video" => "adjuntos/album/" . $row['video'],
                                                                "videonombre" => $row['nombre'],

                                                            ));
                                                    }
                                                    $insBeanPagination->setList($insLibro->__toString());
                                                }
                                            }
                                        } else {
                                            $stmt = $conexion->prepare("SELECT COUNT(idlibroCuenta) AS CONTADOR FROM `librocuenta` WHERE cuenta_codigocuenta=:Cuenta AND libro_codigolibro LIKE CONCAT('%',:Libro,'%')");
                                            $stmt->bindValue(":Cuenta", $libro->getCuenta(), PDO::PARAM_STR);
                                            $stmt->bindValue(":Libro", $libro->getCodigo(), PDO::PARAM_STR);
                                            $stmt->execute();
                                            $datos = $stmt->fetchAll();
                                            foreach ($datos as $row) {
                                                $insBeanPagination->setCountFilter($row['CONTADOR']);
                                                if ($row['CONTADOR'] > 0) {
                                                    $stmt = $conexion->prepare("SELECT lib.* FROM `librocuenta` AS libcue INNER JOIN `libro` AS lib ON lib.codigo=libcue.libro_codigoLibro WHERE libcue.cuenta_codigocuenta=:Cuenta AND libro_codigolibro LIKE CONCAT('%',:Libro,'%')");
                                                    $stmt->bindValue(":Cuenta", $libro->getCuenta(), PDO::PARAM_STR);
                                                    $stmt->bindValue(":Libro", $libro->getCodigo(), PDO::PARAM_STR);
                                                    $stmt->execute();
                                                    $datos = $stmt->fetchAll();
                                                    $insLibro = new Libro();
                                                    foreach ($datos as $row) {
                                                        $insLibro->setIdLibro($row['idlibro']);
                                                        $insLibro->setCodigo($row['codigo']);
                                                        $insLibro->setNombre($row['nombre']);
                                                        $insLibro->setImagenOtro($row['desImagen']);
                                                        $insLibro->setImagen($row['imagen']);
                                                        $insLibro->setEstado($row['estado']);
                                                        $insLibro->setDescripcion($row['descripcion']);

                                                        $insLibro->setCategoria($row['idcategoria']);
                                                    }

                                                    $insBeanPagination->setList($insLibro->__toString());
                                                }
                                            }

                                        }
                                    }
                                }
                            }
                        } else {
                            $stmt = $conexion->prepare("SELECT COUNT(idlibroCuenta) AS CONTADOR FROM `librocuenta` WHERE cuenta_codigocuenta=:Cuenta AND libro_codigolibro =:Libro ");
                            $stmt->bindValue(":Cuenta", $libro->getCuenta(), PDO::PARAM_STR);
                            $stmt->bindValue(":Libro", $libro->getCodigo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBeanPagination->setCountFilter($row['CONTADOR']);
                                if ($row['CONTADOR'] > 0) {
                                    $stmt = $conexion->prepare("SELECT lib.* FROM `librocuenta` AS libcue INNER JOIN `libro` AS lib ON lib.codigo=libcue.libro_codigoLibro WHERE libcue.cuenta_codigocuenta=:Cuenta AND codigo=:Libro");
                                    $stmt->bindValue(":Cuenta", $libro->getCuenta(), PDO::PARAM_STR);
                                    $stmt->bindValue(":Libro", $libro->getCodigo(), PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    $insLibro = new Libro();
                                    foreach ($datos as $row) {
                                        $insLibro->setIdLibro($row['idlibro']);
                                        $insLibro->setCodigo($row['codigo']);
                                        $insLibro->setNombre($row['nombre']);
                                        $insLibro->setImagenOtro($row['desImagen']);
                                        $insLibro->setImagen($row['imagen']);
                                        $insLibro->setEstado($row['estado']);
                                        $insLibro->setDescripcion($row['descripcion']);
                                        $insLibro->setCategoria($row['idcategoria']);
                                    }
                                    $stmt = $conexion->prepare("SELECT video,MIN(desde),nombre FROM `album` WHERE tipo=1 AND desde LIKE CONCAT('%',:LibroDesde,'%') AND hasta LIKE CONCAT('%',:LibroHasta,'%')");
                                    $stmt->bindValue(":LibroDesde", $libro->getCodigo(), PDO::PARAM_STR);
                                    $stmt->bindValue(":LibroHasta", $libro->getCodigo(), PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {
                                        $insLibro->setList(
                                            array("video" => "adjuntos/album/" . $row['video'],
                                                "videonombre" => $row['nombre'],

                                            ));
                                    }
                                    $insBeanPagination->setList($insLibro->__toString());
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
    protected function eliminar_libro_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM libro WHERE
        idlibro=:IDlibro ");
        $sql->bindValue(":IDlibro", $codigo, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_libro_modelo($conexion, $Libro)
    {
        $sql = $conexion->prepare("UPDATE `libro` SET codigo=:Codigo,nombre=:Nombre,imagen=:Imagen  WHERE idlibro=:ID");
        $sql->bindValue(":Nombre", $Libro->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $Libro->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $Libro->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $Libro->getIdLibro(), PDO::PARAM_INT);
        return $sql;
    }

}
