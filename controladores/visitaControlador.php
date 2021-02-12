<?php

require_once './modelos/visitaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class visitaControlador extends visitaModelo
{
    public function agregar_visita_controlador($Visita)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Visita->setPagina(mainModel::limpiar_cadena($Visita->getPagina()));
            $Visita->setContador(mainModel::limpiar_cadena($Visita->getContador()));
            $Visita->setInfo(mainModel::limpiar_cadena($Visita->getInfo()));

            $visita = visitaModelo::datos_visita_modelo($this->conexion_db, "ip", $Visita);
            if ($visita["countFilter"] > 0) {
                $Visita->setFecha_Fin($Visita->getFecha());
                $Visita->setIdvisita($visita["list"][0]['idvisita']);
                $Visita->setContador($visita["list"][0]['contador'] + 1);
                $stmt = visitaModelo::actualizar_visita_modelo($this->conexion_db, $Visita);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido actualizar la IP");
                }
            } else {
                $stmt = visitaModelo::agregar_visita_modelo($this->conexion_db, $Visita);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");

                } else {
                    $insBeanCrud->setMessageServer("No hemos podido registrar la IP");
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
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function datos_visita_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);

            $insBeanCrud->setBeanPagination(visitaModelo::datos_visita_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function reporte_visita_controlador($tipo, $codigo)
    {
        $row = "";

        try {
            $variable = visitaModelo::datos_visita_modelo($this->conexion_db, mainModel::limpiar_cadena($tipo), $codigo);
            if ($variable['countFilter'] > 0) {
                $titulo = "INDICADORES";
                $row = "<table border='1'>
        <thead>
        <tr>
        <th colspan='7'> LISTA DE $titulo </th>
        </tr>
        <tr>
          <th ></th>
          <th >IP</th>
          <th >PAGINA</th>
          <th >PAÍS</th>
          <th >VECES INGRESADAS</th>
          <th>FECHA DE INGRESO INCIAL</th>
          <th>ULTIMA FECHA DE INGRESO</th>
        </tr>
      </thead>
      <tbody> ";
                $contador = 1;
                foreach ($variable['list'] as $value) {
                    $row = $row . "
            <tr>
            <td>" . ($contador++) . "</td>
              <td>" . $value['ip'] . "</td>
              <td>" . $value['pagina'] . "</td>
              <td>" . $value['info'] . "</td>
              <td>" . $value['contador'] . "</td>
              <td>" . $value['fecha'] . "</td>
              <td>" . $value['fecha_fin'] . "</td>
            </tr>";
                }
                $row = $row . "</tbody> </table>";
                header("Content-Disposition: attachment; filename=$titulo.xls");
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
    public function paginador_visita_controlador($conexion, $inicio, $registros, $filtros)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $f_inicial = mainModel::limpiar_cadena($filtros["f_inicial"]);
            $f_final = mainModel::limpiar_cadena($filtros["f_final"]);
            $tipo_pagina = mainModel::limpiar_cadena($filtros["pagina"]);
            $f_pais = mainModel::limpiar_cadena($filtros["pais"]);

            if ($tipo_pagina == "todo" && $f_inicial != "") {
                $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `visita` WHERE (DATE(fecha_fin) BETWEEN ? AND ? ) AND UPPER(descripcion) like concat('%',?,'%')");
                $stmt->bindParam(1, $f_inicial);
                $stmt->bindParam(2, $f_final);
                $stmt->bindParam(3, $f_pais);
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    $insBeanPagination->setCountFilter($row['CONTADOR']);
                    if ($row['CONTADOR'] > 0) {
                        $stmt = $conexion->prepare("SELECT * FROM `visita` WHERE ( DATE(fecha_fin) BETWEEN ? AND ? ) AND UPPER(descripcion) like concat('%',?,'%') ORDER BY fecha_fin DESC LIMIT ?,?");
                        $stmt->bindParam(1, $f_inicial);
                        $stmt->bindParam(2, $f_final);
                        $stmt->bindParam(3, $f_pais);
                        $stmt->bindParam(4, $inicio, PDO::PARAM_INT);
                        $stmt->bindParam(5, $registros, PDO::PARAM_INT);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            $insVisita = new Visita();
                            $insVisita->setIdvisita($row['id']);
                            $insVisita->setIp($row['ip']);
                            $insVisita->setPagina($row['pagina']);
                            $insVisita->setContador($row['contador']);
                            $insVisita->setFecha($row['fecha_inicio']);
                            $insVisita->setInfo($row['descripcion']);
                            $insVisita->setFecha_Fin($row['fecha_fin']);

                            $insBeanPagination->setList($insVisita->__toString());
                        }

                    }
                }

                $stmt->closeCursor(); // this is not even required
                $stmt = null; // doing this is mandatory for connection to get closed
            } else if ($tipo_pagina == "todo" && $f_inicial == "") {

                $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `visita` WHERE DATE(fecha_fin) <=?  AND UPPER(descripcion) like concat('%',?,'%')");
                $stmt->bindParam(1, $f_final);
                $stmt->bindParam(2, $f_pais);
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    $insBeanPagination->setCountFilter($row['CONTADOR']);
                    if ($row['CONTADOR'] > 0) {
                        $stmt = $conexion->prepare("SELECT * FROM `visita` WHERE DATE(fecha_fin) <= ?  AND UPPER(descripcion) like concat('%',?,'%') ORDER BY fecha_fin DESC LIMIT ?,?");
                        $stmt->bindParam(1, $f_final);
                        $stmt->bindParam(2, $f_pais);
                        $stmt->bindParam(3, $inicio, PDO::PARAM_INT);
                        $stmt->bindParam(4, $registros, PDO::PARAM_INT);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            $insVisita = new Visita();
                            $insVisita->setIdvisita($row['id']);
                            $insVisita->setIp($row['ip']);
                            $insVisita->setPagina($row['pagina']);
                            $insVisita->setContador($row['contador']);
                            $insVisita->setFecha($row['fecha_inicio']);
                            $insVisita->setInfo($row['descripcion']);
                            $insVisita->setFecha_Fin($row['fecha_fin']);

                            $insBeanPagination->setList($insVisita->__toString());
                        }

                    }
                }

                $stmt->closeCursor(); // this is not even required
                $stmt = null; // doing this is mandatory for connection to get closed
            } else if ($tipo_pagina != "todo" && $f_inicial != "") {
                $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `visita` WHERE pagina=? AND ( DATE(fecha_fin) BETWEEN ? AND ? )  AND UPPER(descripcion) like concat('%',?,'%')");
                $stmt->bindParam(1, $tipo_pagina, PDO::PARAM_STR);
                $stmt->bindParam(2, $f_inicial);
                $stmt->bindParam(3, $f_final);
                $stmt->bindParam(4, $f_pais);
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    $insBeanPagination->setCountFilter($row['CONTADOR']);
                    if ($row['CONTADOR'] > 0) {
                        $stmt = $conexion->prepare("SELECT * FROM `visita` WHERE pagina=? AND ( DATE(fecha_fin) BETWEEN ? AND ? ) AND UPPER(descripcion) like concat('%',?,'%') ORDER BY fecha_fin DESC LIMIT ?,?");
                        $stmt->bindParam(1, $tipo_pagina, PDO::PARAM_STR);
                        $stmt->bindParam(2, $f_inicial);
                        $stmt->bindParam(3, $f_final);
                        $stmt->bindParam(4, $f_pais);
                        $stmt->bindParam(5, $inicio, PDO::PARAM_INT);
                        $stmt->bindParam(6, $registros, PDO::PARAM_INT);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            $insVisita = new Visita();
                            $insVisita->setIdvisita($row['id']);
                            $insVisita->setIp($row['ip']);
                            $insVisita->setPagina($row['pagina']);
                            $insVisita->setContador($row['contador']);
                            $insVisita->setFecha($row['fecha_inicio']);
                            $insVisita->setInfo($row['descripcion']);
                            $insVisita->setFecha_Fin($row['fecha_fin']);

                            $insBeanPagination->setList($insVisita->__toString());
                        }

                    }
                }

                $stmt->closeCursor(); // this is not even required
                $stmt = null; // doing this is mandatory for connection to get closed
            } else if ($tipo_pagina != "todo" && $f_inicial == "") {

                $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `visita` WHERE pagina=? AND DATE(fecha_fin)<=?  AND UPPER(descripcion) like concat('%',?,'%')");
                $stmt->bindParam(1, $tipo_pagina, PDO::PARAM_STR);
                $stmt->bindParam(2, $f_final);
                $stmt->bindParam(3, $f_pais);
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    $insBeanPagination->setCountFilter($row['CONTADOR']);
                    if ($row['CONTADOR'] > 0) {
                        $stmt = $conexion->prepare("SELECT * FROM `visita` WHERE pagina=? AND DATE(fecha_fin)<=? AND UPPER(descripcion) like concat('%',?,'%') ORDER BY fecha_fin DESC LIMIT ?,?");
                        $stmt->bindParam(1, $tipo_pagina, PDO::PARAM_STR);
                        $stmt->bindParam(2, $f_final);
                        $stmt->bindParam(3, $f_pais);
                        $stmt->bindParam(4, $inicio, PDO::PARAM_INT);
                        $stmt->bindParam(5, $registros, PDO::PARAM_INT);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            $insVisita = new Visita();
                            $insVisita->setIdvisita($row['id']);
                            $insVisita->setIp($row['ip']);
                            $insVisita->setPagina($row['pagina']);
                            $insVisita->setContador($row['contador']);
                            $insVisita->setFecha($row['fecha_inicio']);
                            $insVisita->setInfo($row['descripcion']);
                            $insVisita->setFecha_Fin($row['fecha_fin']);

                            $insBeanPagination->setList($insVisita->__toString());
                        }

                    }
                }

                $stmt->closeCursor(); // this is not even required
                $stmt = null; // doing this is mandatory for connection to get closed
            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";
        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";
        }
        return $insBeanPagination->__toString();

    }
    public function bean_paginador_visita_controlador($pagina, $registros, $filtros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_visita_controlador($this->conexion_db, $inicio, $registros, $filtros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_visita_controlador($Visita)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Visita->setIdVisita(mainModel::limpiar_cadena($Visita->getIdVisita()));

            $lista = visitaModelo::datos_visita_modelo($this->conexion_db, "unico", $Visita);
            if ($lista["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No podemos eliminar no existe la visita");
            } else {
                $stmt = visitaModelo::eliminar_visita_modelo($this->conexion_db, $Visita->getIdVisita());
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_visita_controlador($this->conexion_db, 0, 5));
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido eliminar la visita");
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
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_visita_controlador($Visita)
    {
        $insBeanCrud = new BeanCrud();
        try {

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

}
