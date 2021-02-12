<?php

require_once './modelos/publicoModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class publicoControlador extends publicoModelo
{
    public function agregar_publico_controlador($Publico)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Publico->setNombre(mainModel::limpiar_cadena($Publico->getNombre()));

            $Publico->setEmail(mainModel::limpiar_cadena($Publico->getEmail()));
            $stmt = $this->conexion_db->prepare("SELECT count(idpublico) AS CONTADOR FROM `publico` WHERE email=?");
            $stmt->bindValue(1, $Publico->getEmail(), PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                if ($row['CONTADOR'] == 0) {
                    $stmt = publicoModelo::agregar_publico_modelo($this->conexion_db, $Publico);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        // $insBeanCrud->setBeanPagination(self::paginador_publico_controlador($this->conexion_db, 0, 5));

                    } else {

                        $insBeanCrud->setMessageServer("error, Hubo un problema al momento de registrarte");
                    }
                } else { $insBeanCrud->setMessageServer("ok");}

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
    public function datos_publico_controlador($tipo, $codigo)
    {
        return publicoModelo::datos_publico_modelo(mainModel::limpiar_cadena($tipo), $codigo);

    }
    public function reporte_publico_controlador($tipo, $codigo)
    {
        $row = "";

        try {
            $variable = publicoModelo::datos_publico_modelo($this->conexion_db, mainModel::limpiar_cadena($tipo), $codigo);
            if ($variable['countFilter'] > 0) {
                $titulo = "PUBLICO REGISTRADO";
                $row = "<table border='1'>
        <thead>
        <tr>
        <th colspan='3'> LISTA DE $titulo </th>
        </tr>
        <tr>
          <th ></th>
          <th >NOMBRE</th>
          <th >CORREO ELECTRONICO</th>
        </tr>
      </thead>
      <tbody> ";
                $contador = 1;
                foreach ($variable['list'] as $value) {
                    $row = $row . "
            <tr>
            <td>" . ($contador++) . "</td>
              <td>" . $value['nombre'] . "</td>
              <td>" . $value['email'] . "</td>
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
    public function paginador_publico_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idpublico) AS CONTADOR FROM `publico`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `publico` ORDER BY nombre ASC LIMIT ?,?");
                    $stmt->bindParam(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindParam(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();

                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insPublico = new Publico();
                        $insPublico->setIdpublico($row['idpublico']);
                        $insPublico->setNombre($row['nombre']);
                        $insPublico->setEmail($row['email']);
                        $insPublico->setFecha($row['fecha']);
                        $insBeanPagination->setList($insPublico->__toString());
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
    public function bean_paginador_publico_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_publico_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_publico_controlador($Publico)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $publico = publicoModelo::datos_publico_modelo($this->conexion_db, "unico", $Publico);
            if ($publico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el Usuario");
            } else {
                $stmt = publicoModelo::eliminar_publico_modelo($this->conexion_db, mainModel::limpiar_cadena($Publico->getIdpublico()));
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_publico_controlador($this->conexion_db, 0, 5));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el Usuario");
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

}
