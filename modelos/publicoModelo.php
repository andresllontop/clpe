<?php

require_once './core/mainModel.php';

class publicoModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_publico_modelo($conexion, $publico)
    {

        $datatime = date("Y/m/d h:m:s", strtotime($publico->getFecha()));
        $sql = $conexion->prepare("INSERT INTO `publico`(nombre,email,fecha) VALUES(?,?,?)");
        $sql->bindValue(1, $publico->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(2, $publico->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(3, $datatime);

        return $sql;

    }
    protected function eliminar_publico_modelo($conexion, $publico)
    {
        $sql = $conexion->prepare("DELETE FROM `publico` WHERE  idpublico=?");
        $sql->bindValue(1, $publico, PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_publico_modelo($conexion, $tipo, $publico)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idpublico) AS CONTADOR FROM `publico` WHERE idpublico=:IDpublico");
                    $stmt->bindValue(":IDpublico", $publico->getIdpublico(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `publico`
                            WHERE idpublico=:IDpublico");
                            $stmt->bindValue(":IDpublico", $publico->getIdpublico(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insPublico = new Publico();
                                $insPublico->setIdpublico($row['idpublico']);
                                $insPublico->setNombre($row['nombre']);
                                $insPublico->setEmail($row['email']);
                                $insBeanPagination->setList($insPublico->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idpublico) AS CONTADOR FROM `publico`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `publico`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insPublico = new Publico();
                                $insPublico->setIdpublico($row['idpublico']);
                                $insPublico->setNombre($row['nombre']);
                                $insPublico->setEmail($row['email']);
                                $insBeanPagination->setList($insPublico->__toString());
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
    protected function actualizar_publico_modelo($conexion, $datos)
    {
        $datatime = date("Y/m/d h:m:s", strtotime($publico->getFecha()));
        $sql = $conexion->prepare("UPDATE publico
        SET AdminNombre=:Nombre,AdminApellido=:Apellido,AdminTelefono=:Telefono,
        AdminOcupacion=:Ocupacion
        WHERE id=:ID");
        $sql->bindValue(1, $publico->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(2, $publico->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(3, $publico->getIdpublico(), PDO::PARAM_INT);
        $sql->bindValue(3, $datatime);
        return $sql;
    }
}
