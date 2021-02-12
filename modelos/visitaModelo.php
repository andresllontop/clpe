<?php

require_once './core/mainModel.php';

class visitaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_visita_modelo($conexion, $Visita)
    {
        $sql = $conexion->prepare("INSERT INTO `visita` (ip,pagina,contador,fecha_inicio,descripcion) VALUES(:IP,:Pagina,:Contador,:Fecha,:InformacionPais)");
        $sql->bindValue(":IP", $Visita->getIp());
        $sql->bindValue(":Pagina", $Visita->getPagina());
        $sql->bindValue(":Contador", $Visita->getContador());
        $sql->bindValue(":Fecha", $Visita->getFecha());
        $sql->bindValue(":InformacionPais", $Visita->getInfo());

        return $sql;

    }
    protected function datos_visita_modelo($conexion, $tipo, $visita)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `visita` WHERE id=?");
                    $stmt->bindValue(1, $visita->getIdvisita(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `visita` WHERE id=?");
                            $stmt->bindValue(1, $visita->getIdvisita(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insVisita = new Visita();
                                $insVisita->setIdvisita($row['id']);
                                $insVisita->setIp($row['ip']);
                                $insVisita->setPagina($row['pagina']);
                                $insVisita->setContador($row['contador']);
                                $insVisita->setFecha($row['fecha_inicio']);
                                $insVisita->setFecha_Fin($row['fecha_fin']);
                                $insBeanPagination->setList($insVisita->__toString());
                            }

                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "ip":
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `visita` WHERE ip=? and pagina=?");
                    $stmt->bindValue(1, $visita->getIp(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $visita->getPagina(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `visita` WHERE ip=? and pagina=?");
                            $stmt->bindValue(1, $visita->getIp(), PDO::PARAM_STR);
                            $stmt->bindValue(2, $visita->getPagina(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insVisita = new Visita();
                                $insVisita->setIdvisita($row['id']);
                                $insVisita->setIp($row['ip']);
                                $insVisita->setPagina($row['pagina']);
                                $insVisita->setContador($row['contador']);
                                $insVisita->setFecha($row['fecha_inicio']);
                                $insVisita->setFecha_Fin($row['fecha_fin']);
                                $insBeanPagination->setList($insVisita->__toString());
                            }

                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->query("SELECT COUNT(id) AS CONTADOR FROM `visita`");
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->query("SELECT * FROM `visita`");
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insVisita = new Visita();
                                $insVisita->setIdvisita($row['id']);
                                $insVisita->setIp($row['ip']);
                                $insVisita->setPagina($row['pagina']);
                                $insVisita->setInfo($row['descripcion']);
                                $insVisita->setContador($row['contador']);
                                $insVisita->setFecha($row['fecha_inicio']);
                                $insVisita->setFecha_Fin($row['fecha_fin']);
                                $insBeanPagination->setList($insVisita->__toString());
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
    protected function eliminar_visita_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `visita` WHERE  id=:Codigo");
        $sql->bindValue(":Codigo", $codigo);
        return $sql;
    }
    protected function actualizar_visita_modelo($conexion, $Visita)
    {
        $sql = $conexion->prepare("UPDATE `visita` SET contador=:Contador, fecha_fin=:FechaFin WHERE id=:ID");
        $sql->bindValue(":Contador", $Visita->getContador());
        $sql->bindValue(":FechaFin", $Visita->getFecha_Fin());
        $sql->bindValue(":ID", $Visita->getIdvisita());

        return $sql;

    }
}
