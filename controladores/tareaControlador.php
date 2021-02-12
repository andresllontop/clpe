<?php
require_once './modelos/tareaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
require_once './classes/principal/subtitulo.php';
require_once './classes/principal/titulo.php';
class tareaControlador extends tareaModelo
{
    public function datos_tarea_controlador($tipo, $codigo)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);

            $insBeanCrud->setBeanPagination(tareaModelo::datos_tarea_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }

    public function paginador_tarea_controlador($conexion, $inicio, $registros, $codigo, $titulo)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->prepare("SELECT COUNT(idtarea) AS CONTADOR  FROM `tarea` WHERE cuenta=?");
            $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT cuest.imagen as cuest_imagen,sub.*, lec.idtarea,lec.tipo,lec.fecha,tit.tituloNombre,tit.codigoTitulo FROM `tarea` as lec inner join `subtitulo` as sub ON sub.codigo_subtitulo=lec.codigo_subtitulo inner join `titulo` as tit ON tit.idtitulo=sub.titulo_idtitulo left join `test` as cuest on cuest.subtitulo_codigo_test = lec.codigo_subtitulo WHERE lec.cuenta=? and lec.codigo_subtitulo like concat('%',?,'%') ORDER BY lec.codigo_subtitulo ASC LIMIT ?,?");
                    $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
                    $stmt->bindValue(2, $titulo, PDO::PARAM_STR);
                    $stmt->bindValue(3, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(4, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    $contador = 0;
                    foreach ($datos as $row) {
                        $contador++;
                        $insTitulo = new Titulo();
                        $insTitulo->setNombre($row['tituloNombre']);
                        $insTitulo->setCodigo($row['codigoTitulo']);

                        $insSubTitulo = new SubTitulo();
                        $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                        $insSubTitulo->setPdf($row['subtituloPDF']);
                        $insSubTitulo->setNombre($row['nombre']);
                        $insSubTitulo->setDescripcion($row['descripcion']);
                        if ($row['tipo'] == 2) {
                            $insSubTitulo->setImagen($row['cuest_imagen']);
                        } else {
                            $insSubTitulo->setImagen($row['subtitulo_imagen']);
                        }
                        $insSubTitulo->setTitulo($insTitulo->__toString());
                        $insTarea = new Tarea();
                        $insTarea->setIdtarea($row['idtarea']);
                        $insTarea->setTipo($row['tipo']);
                        $insTarea->setFecha($row['fecha']);

                        $insTarea->setSubTitulo($insSubTitulo->__toString());
                        $insBeanPagination->setList($insTarea->__toString());
                    }
                    $insBeanPagination->setCountFilter($contador);
                }
            }

            $stmt->closeCursor(); // this is not even required
            $stmt = null; // doing this is mandatory for connection to get closed

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";
        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";
        }
        return $insBeanPagination->__toString();
    }
    public function bean_paginador_tarea_controlador($pagina, $registros, $codigo, $titulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $codigo = mainModel::limpiar_cadena($codigo);
            $titulo = mainModel::limpiar_cadena($titulo);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_tarea_controlador($this->conexion_db, $inicio, $registros, $codigo, $titulo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }

    public function paginador_tarea_administrador_controlador($conexion, $inicio, $registros, $filtro)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->prepare("SELECT COUNT(tar.idtarea) AS CONTADOR  FROM `tarea` AS tar INNER JOIN `administrador` as admmini ON admmini.Cuenta_Codigo=tar.cuenta inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo WHERE cuent.tipo=2 and cuent.idcuenta!=1 and (AdminNombre like concat('%',?,'%') OR AdminApellido like concat('%',?,'%')) ORDER BY admmini.AdminNombre ASC LIMIT ?,?");
            $stmt->bindValue(1, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(2, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(3, $inicio, PDO::PARAM_INT);
            $stmt->bindValue(4, $registros, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT cuest.imagen as cuest_imagen,sub.*, lec.idtarea,lec.tipo,lec.fecha,tit.tituloNombre,tit.codigoTitulo FROM `tarea` as lec inner join `subtitulo` as sub ON sub.codigo_subtitulo=lec.codigo_subtitulo inner join `titulo` as tit ON tit.idtitulo=sub.titulo_idtitulo left join `test` as cuest on cuest.subtitulo_codigo_test = lec.codigo_subtitulo WHERE lec.cuenta=? and lec.codigo_subtitulo like concat('%',?,'%') ORDER BY lec.codigo_subtitulo ASC LIMIT ?,?");
                    $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
                    $stmt->bindValue(2, $titulo, PDO::PARAM_STR);
                    $stmt->bindValue(3, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(4, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    $contador = 0;
                    foreach ($datos as $row) {
                        $contador++;
                        $insTitulo = new Titulo();
                        $insTitulo->setNombre($row['tituloNombre']);
                        $insTitulo->setCodigo($row['codigoTitulo']);

                        $insSubTitulo = new SubTitulo();
                        $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                        $insSubTitulo->setPdf($row['subtituloPDF']);
                        $insSubTitulo->setNombre($row['nombre']);
                        $insSubTitulo->setDescripcion($row['descripcion']);
                        if ($row['tipo'] == 2) {
                            $insSubTitulo->setImagen($row['cuest_imagen']);
                        } else {
                            $insSubTitulo->setImagen($row['subtitulo_imagen']);
                        }
                        $insSubTitulo->setTitulo($insTitulo->__toString());
                        $insTarea = new Tarea();
                        $insTarea->setIdtarea($row['idtarea']);
                        $insTarea->setTipo($row['tipo']);
                        $insTarea->setFecha($row['fecha']);

                        $insTarea->setSubTitulo($insSubTitulo->__toString());
                        $insBeanPagination->setList($insTarea->__toString());
                    }
                    $insBeanPagination->setCountFilter($contador);
                }
            }

            $stmt->closeCursor(); // this is not even required
            $stmt = null; // doing this is mandatory for connection to get closed

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";
        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";
        }
        return $insBeanPagination->__toString();
    }
    public function bean_paginador_tarea_administrador_controlador($pagina, $registros, $filtro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $filtro = mainModel::limpiar_cadena($filtro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_tarea_administrador_controlador($this->conexion_db, $inicio, $registros, $filtro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
}
