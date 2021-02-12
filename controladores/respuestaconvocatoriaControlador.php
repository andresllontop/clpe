<?php

require_once './modelos/respuestaconvocatoriaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/personaconvocatoria.php';

class respuestaconvocatoriaControlador extends respuestaconvocatoriaModelo
{
    public function agregar_respuestaconvocatoria_controlador($BeanRespuestaConvocatoria)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $personaClass = new PersonaConvocatoria();
            $personaClass->setIp(str_pad(self::get_client_ip_respuesta(), 25));
            $personaClass->setFecha(date("Y/m/d H:i:s"));
            $personaClass->setCantidad(count($BeanRespuestaConvocatoria));
            $personaClass->setCodigo($BeanRespuestaConvocatoria[0]->codigo);

            $stmt = respuestaconvocatoriaModelo::agregar_respuestaconvocatoria_modelo($this->conexion_db, $personaClass);
            if ($stmt->execute()) {
                $stmt = $this->conexion_db->prepare("SELECT max(idpersona_convocatoria) AS CONTADOR FROM `persona_convocatoria` ");
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    if ($row['CONTADOR'] > 0) {
                        $stmt = $this->conexion_db->prepare("INSERT INTO `respuesta_convocatoria`(pregunta,respuesta,idpersona_convocatoria) VALUES(?,?,?)");
                        $valor = false;
                        foreach ($BeanRespuestaConvocatoria as $lista) {
                            $stmt->bindValue(1, $lista->pregunta, PDO::PARAM_STR);
                            $stmt->bindValue(2, $lista->respuesta, PDO::PARAM_STR);
                            $stmt->bindValue(3, $row['CONTADOR'], PDO::PARAM_INT);
                            $valor = $stmt->execute();
                        }
                        if ($valor) {
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                        }
                    } else {
                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar la respuesta");
                    }
                }

            } else {
                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar la respuesta");
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
    public function datos_respuestaconvocatoria_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);

            $insBeanCrud->setBeanPagination(respuestaconvocatoriaModelo::datos_respuestaconvocatoria_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_respuestaconvocatoria_controlador($conexion, $inicio, $registros, $filtro)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(idpersona_convocatoria) AS CONTADOR FROM `persona_convocatoria` WHERE codigo_convocatoria like concat('%',?,'%')");
            $stmt->bindValue(1, $filtro, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `persona_convocatoria` WHERE codigo_convocatoria like concat('%',?,'%') ORDER BY fecha ASC LIMIT ?,?");
                    $stmt->bindValue(1, $filtro, PDO::PARAM_STR);
                    $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insRespuestaConvocatoria = new PersonaConvocatoria();
                        $insRespuestaConvocatoria->setIdPersonaConvocatoria($row['idpersona_convocatoria']);
                        $insRespuestaConvocatoria->setCantidad($row['cantidad']);
                        $insRespuestaConvocatoria->setIp($row['ip']);
                        $insRespuestaConvocatoria->setFecha($row['fecha']);
                        $insRespuestaConvocatoria->setCodigo($row['codigo_convocatoria']);
                        $insBeanPagination->setList($insRespuestaConvocatoria->__toString());
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
    public function bean_paginador_respuestaconvocatoria_controlador($pagina, $registros, $filtro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $filtro = mainModel::limpiar_cadena($filtro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_respuestaconvocatoria_controlador($this->conexion_db, $inicio, $registros, $filtro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_respuestaconvocatoria_controlador($RespuestaConvocatoria)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $RespuestaConvocatoria->setIdPersonaConvocatoria(mainModel::limpiar_cadena($RespuestaConvocatoria->getIdPersonaConvocatoria()));

            $lista = respuestaconvocatoriaModelo::datos_respuestaconvocatoria_modelo($this->conexion_db, "unico", $RespuestaConvocatoria);

            $stmt = respuestaconvocatoriaModelo::eliminar_respuestaconvocatoria_modelo($this->conexion_db, $RespuestaConvocatoria->getIdPersonaConvocatoria());
            if ($stmt->execute()) {
                $stmt = respuestaconvocatoriaModelo::eliminar_personaconvocatoria_modelo($this->conexion_db, $RespuestaConvocatoria->getIdPersonaConvocatoria());
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_respuestaconvocatoria_controlador($this->conexion_db, 0, 20));
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido eliminar el cuestionario");
                }
            } else {
                $insBeanCrud->setMessageServer("No hemos podido eliminar el cuestionario");
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
    //Obtiene la IP del cliente
    public function get_client_ip_respuesta()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

}
