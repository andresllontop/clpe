<?php

require_once './modelos/ajustecitaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/cliente.php';
require_once './classes/principal/subtitulo.php';
class ajustecitaControlador extends ajustecitaModelo
{
    public function agregar_ajustecita_controlador($Ajustecita)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Ajustecita->setSubtitulo(mainModel::limpiar_cadena($Ajustecita->getSubtitulo()));
            $Ajustecita->setTipo(mainModel::limpiar_cadena($Ajustecita->getTipo()));
            $stmt = ajustecitaModelo::agregar_ajustecita_modelo($this->conexion_db, $Ajustecita);
            if ($stmt->execute()) {
                $this->conexion_db->commit();
                $insBeanCrud->setMessageServer("ok");
                $insBeanCrud->setBeanPagination(self::paginador_ajustecita_controlador($this->conexion_db, 0, 20, ''));

            } else {

                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar la ajustecita ");
            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function datos_ajustecita_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $codigo = mainModel::limpiar_cadena($codigo);
            $insBeanCrud->setBeanPagination(ajustecitaModelo::datos_ajustecita_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_ajustecita_controlador($conexion, $inicio, $registros, $filter)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(idajuste_cita) AS CONTADOR  FROM `ajuste_cita` where  subtitulo LIKE CONCAT('%',?,'%') ");
            $stmt->bindValue(1, $filter, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);

                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT cit.*,subt.nombre as subt_nombre FROM `ajuste_cita` as cit INNER JOIN `subtitulo` as subt ON subt.codigo_subtitulo=cit.subtitulo where cit.subtitulo LIKE CONCAT('%',?,'%') ORDER BY cit.idajuste_cita ASC LIMIT ?,?");
                    $stmt->bindValue(1, $filter, PDO::PARAM_STR);
                    $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insSubtitulo = new SubTitulo();
                        $insSubtitulo->setNombre($row['subt_nombre']);
                        $insSubtitulo->setCodigo($row['subtitulo']);

                        $insajustecita = new Ajustecita();
                        $insajustecita->setIdajusteCita($row['idajuste_cita']);
                        $insajustecita->setTipo($row['tipo']);
                        $insajustecita->setSubtitulo($insSubtitulo->__toString());
                        $insBeanPagination->setList($insajustecita->__toString());
                    }
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
    public function bean_paginador_ajustecita_controlador($pagina, $registros, $filter)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $filter = mainModel::limpiar_cadena($filter);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;

            $insBeanCrud->setBeanPagination(self::paginador_ajustecita_controlador($this->conexion_db, $inicio, $registros, $filter));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_ajustecita_controlador($Ajustecita)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Ajustecita->setIdajusteCita(mainModel::limpiar_cadena($Ajustecita->getIdajusteCita()));
            $mensaje = ajustecitaModelo::datos_ajustecita_modelo($this->conexion_db, 'unico', $Ajustecita);

            if ($mensaje['countFilter'] == 0) {
                $insBeanCrud->setMessageServer('No se encuentra la cita');
            } else {

                $stmt = ajustecitaModelo::eliminar_ajustecita_modelo($this->conexion_db, $Ajustecita->getIdajusteCita());

                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer('ok');
                    $insBeanCrud->setBeanPagination(self::paginador_ajustecita_controlador($this->conexion_db, 0, 20, ''));

                } else {
                    $insBeanCrud->setMessageServer('No se eliminó la cita');
                }

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error!: ' . $th->getMessage() . '<br/>';

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';

        } finally {
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_ajustecita_controlador($Ajustecita)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Ajustecita->setIdajusteCita(mainModel::limpiar_cadena($Ajustecita->getIdajusteCita()));
            $Ajustecita->setTipo(mainModel::limpiar_cadena($Ajustecita->getTipo()));
            $Ajustecita->setSubtitulo(mainModel::limpiar_cadena($Ajustecita->getSubtitulo()));

            $mensaje = ajustecitaModelo::datos_ajustecita_modelo($this->conexion_db, 'unico', $Ajustecita);

            if ($mensaje['countFilter'] == 0) {
                $insBeanCrud->setMessageServer('No se encuentra la cita');
            } else {
                $stmt = ajustecitaModelo::actualizar_ajustecita_modelo($this->conexion_db, $Ajustecita);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer('ok');
                    $insBeanCrud->setBeanPagination(self::paginador_ajustecita_controlador($this->conexion_db, 0, 20, ''));

                } else {
                    $insBeanCrud->setMessageServer('No se actualizó la cita');
                }
            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error!: ' . $th->getMessage() . '<br/>';

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print '¡Error Processing Request!: ' . $e->getMessage() . '<br/>';

        } finally {
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }

}
