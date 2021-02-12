<?php

require_once './modelos/economicoModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

class economicoControlador extends economicoModelo
{

    public function agregar_economico_controlador($Economico)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Economico->setDescripcion(mainModel::limpiar_cadena($Economico->getDescripcion()));
            $Economico->setResumen(mainModel::limpiar_cadena($Economico->getResumen()));
            $Economico->setComentario(mainModel::limpiar_cadena($Economico->getComentario()));

            if ($original['error'] > 0) {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
            } else {
                $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/economico/" . $destino . "/");
                if ($resultado != "") {
                    $Economico->setArchivo($resultado);
                    $stmt = economicoModelo::agregar_economico_modelo($this->conexion_db, $Economico);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_economico_controlador($this->conexion_db, 0, 20));
                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido registrar el economico");
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                } else {
                    $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra imagen");
                }

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
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function datos_economico_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(economicoModelo::datos_economico_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_economico_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idhistorial_economico) AS CONTADOR FROM `historial_economico`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `historial_economico` ORDER BY fecha DESC LIMIT ?,?");
                    $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insEconomico = new Economico();
                        $insEconomico->setIdEconomico($row['idhistorial_economico']);
                        $insEconomico->setNombre($row['nombres']);
                        $insEconomico->setApellido($row['apellidos']);
                        $insEconomico->setTelefono($row['telefono']);
                        $insEconomico->setPais($row['pais']);
                        $insEconomico->setBanco($row['nombre_banco']);
                        $insEconomico->setMoneda($row['moneda']);
                        $insEconomico->setComision($row['comision']);
                        $insEconomico->setPrecio($row['precio']);
                        $insEconomico->setTipo($row['tipo']);
                        $insEconomico->setFecha($row['fecha']);
                        $insEconomico->setVoucher($row['voucher']);
                        $insBeanPagination->setList($insEconomico->__toString());
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
    public function bean_paginador_economico_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_economico_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_economico_controlador($Economico)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $economico = economicoModelo::datos_economico_modelo($this->conexion_db, "unico", $Economico);
            if ($economico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el economico");
            } else {
                $stmt = economicoModelo::eliminar_economico_modelo($this->conexion_db, mainModel::limpiar_cadena($Economico->getIdeconomico()));

                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_economico_controlador($this->conexion_db, 0, 20));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el economico");
                }
                $stmt->closeCursor();
                $stmt = null;
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
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_economico_controlador($Economico)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Economico->setDescripcion(mainModel::limpiar_cadena($Economico->getDescripcion()));
            $Economico->setResumen(mainModel::limpiar_cadena($Economico->getResumen()));
            $Economico->setComentario(mainModel::limpiar_cadena($Economico->getComentario()));

            switch ((int) $Economico->getTipoArchivo()) {
                case 1:
                    if (isset($_FILES['txtImagenEconomico'])) {
                        $original = $_FILES['txtImagenEconomico'];
                        $nombre = $original['name'];
                        $permitido = array("image/png", "image/jpg", "image/jpeg");
                        $destino = "IMAGENES";
                        $limit_kb = 1700;
                    } else {
                        $nombre = "";
                    }

                    break;
                case 2:
                    if (isset($_FILES['txtVideoEconomico'])) {
                        $original = $_FILES['txtVideoEconomico'];
                        $nombre = $original['name'];
                        $permitido = array("video/mp4");
                        $destino = "VIDEOS";
                        $limit_kb = (17 * 1024);
                    } else {
                        $nombre = "";
                    }

                    break;
            }
            $leconomico = economicoModelo::datos_economico_modelo($this->conexion_db, "unico", $Economico);
            if ($leconomico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el economico");
            } else {
                if ($nombre != "") {
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/economico/" . $destino . "/");
                        if ($resultado != "") {
                            $Economico->setArchivo($resultado);
                            $stmt = economicoModelo::actualizar_economico_modelo($this->conexion_db, $Economico);

                            if ($stmt->execute()) {
                                switch ($Economico->getTipoArchivo()) {
                                    case '1':
                                        unlink('./adjuntos/economico/IMAGENES/' . $leconomico["list"][0]['archivo']);
                                        break;
                                    case '2':
                                        unlink('./adjuntos/economico/VIDEOS/' . $leconomico["list"][0]['archivo']);
                                        break;
                                }
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_economico_controlador($this->conexion_db, 0, 20));

                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el economico");
                            }
                            $stmt->closeCursor();
                            $stmt = null;
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }
                } else {
                    $Economico->setArchivo($leconomico["list"][0]['archivo']);
                    $stmt = economicoModelo::actualizar_economico_modelo($this->conexion_db, $Economico);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_economico_controlador($this->conexion_db, 0, 20));
                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido actualizar el economico");
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                }
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
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function reporte_economico_controlador($tipo, $codigo)
    {
        $row = "";

        try {
            $variable = economicoModelo::datos_economico_modelo($this->conexion_db, mainModel::limpiar_cadena($tipo), $codigo);
            if ($variable['countFilter'] > 0) {
                $titulo = "";
                switch ($codigo->getMoneda()) {
                    case "PEN":
                        $titulo = "HISTORIAL ECONOMICO EN SOLES";
                        break;
                    case "USD":
                        $titulo = "HISTORIAL ECONOMICO EN DOLARES";
                        break;
                    default:
                        $titulo = "HISTORIAL ECONOMICO EN OTRAS MONEDAS";
                        break;
                }
                $row = "<table border='1'>
        <thead>
        <tr>
        <th colspan='12'> LISTA DE $titulo </th>
        </tr>
        <tr>
          <th ></th>
          <th >FECHA</th>
          <th >NOMBRES</th>
          <th >APELLIDOS</th>
          <th>TELEFONO</th>
          <th >PAIS</th>
          <th >MEDIO DE PAGO</th>
          <th >BANCO</th>
          <th >MONEDA</th>
          <th >PRECIO</th>
          <th >COMISIÓN</th>
          <th >MONTO DEPOSITADO</th>
        </tr>
      </thead>
      <tbody> ";
                $contador = 1;
                foreach ($variable['list'] as $value) {
                    $row = $row . "
            <tr>
            <td>" . ($contador++) . "</td>
              <td>" . $value['fecha'] . "</td>
              <td>" . $value['nombre'] . "</td>
              <td>" . $value['apellido'] . "</td>
              <td>" . $value['telefono'] . "</td>
              <td>" . $value['pais'] . "</td>
              <td>" . ($value['tipo'] == 1 ? "CULQI" : "EFECTIVO") . "</td>
              <td>" . $value['banco'] . "</td>
              <td>" . $value['moneda'] . "</td>
              <td>" . $value['precio'] . "</td>
              <td>" . $value['comision'] . "</td>
              <td>" . number_format(((float) $value['precio'] - (float) $value['comision']), 2) . "</td>
            </tr>";
                }
                $row = $row . "</tbody> </table>";

                header("Content-Disposition:attachment;filename=$titulo.xls");

            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $row;
    }
}
