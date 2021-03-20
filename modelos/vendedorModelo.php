<?php

require_once './core/mainModel.php';

class vendedorModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_vendedor_modelo($conexion, $vendedor)
    {
        $sql = $conexion->prepare("INSERT INTO vendedor
        (nombre,apellido,tipo,telefono,empresa,pais,codigo)
         VALUES(:Nombre,:Apellido,:Tipo,:Telefono,:Empresa,:Pais,:Codigo)");
        $sql->bindValue(":Nombre", $vendedor->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Apellido", $vendedor->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $vendedor->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Empresa", $vendedor->getEmpresa(), PDO::PARAM_STR);
        $sql->bindValue(":Pais", $vendedor->getPais(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $vendedor->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":Telefono", $vendedor->getTelefono(), PDO::PARAM_INT);

        return $sql;

    }
    protected function eliminar_vendedor_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `vendedor` WHERE
         idvendedor=:IDvendedor ");
        $sql->bindValue(":IDvendedor", $codigo, PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_vendedor_modelo($conexion, $tipo, $vendedor)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idvendedor) AS CONTADOR FROM `vendedor`
                    WHERE idvendedor=:IDvendedor");
                    $stmt->bindValue(":IDvendedor", $vendedor->getIdVendedor(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `vendedor` WHERE idvendedor=:IDvendedor");
                            $stmt->bindValue(":IDvendedor", $vendedor->getIdVendedor(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insVendedor = new Vendedor();
                                $insVendedor->setIdVendedor($row['idvendedor']);
                                $insVendedor->setNombre($row['nombre']);
                                $insVendedor->setApellido($row['apellido']);
                                $insVendedor->setTipo($row['tipo']);
                                $insVendedor->setTelefono($row['telefono']);
                                $insVendedor->setEmpresa($row['empresa']);
                                $insVendedor->setPais($row['pais']);
                                $insVendedor->setCodigo($row['codigo']);
                                $insBeanPagination->setList($insVendedor->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idvendedor) AS CONTADOR FROM `vendedor`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `vendedor` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insVendedor = new Vendedor();
                                $insVendedor->setIdVendedor($row['idvendedor']);
                                $insVendedor->setNombre($row['nombre']);
                                $insVendedor->setApellido($row['apellido']);
                                $insVendedor->setTipo($row['tipo']);
                                $insVendedor->setTelefono($row['telefono']);
                                $insVendedor->setEmpresa($row['empresa']);
                                $insVendedor->setPais($row['pais']);
                                $insVendedor->setCodigo($row['codigo']);
                                $insBeanPagination->setList($insVendedor->__toString());
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

    }protected function actualizar_vendedor_modelo($conexion, $vendedor)
    {
        $sql = $conexion->prepare("UPDATE `vendedor`
        SET nombre=:Nombre,apellido=:Apellido,tipo=:Tipo,
        empresa=:Empresa,pais=:Pais,
        codigo=:Codigo,telefono=:Telefono  WHERE idvendedor=:ID");
        $sql->bindValue(":Nombre", $vendedor->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Apellido", $vendedor->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $vendedor->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Empresa", $vendedor->getEmpresa(), PDO::PARAM_STR);
        $sql->bindValue(":Pais", $vendedor->getPais(), PDO::PARAM_STR);
        $sql->bindValue(":Codigo", $vendedor->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":Telefono", $vendedor->getTelefono(), PDO::PARAM_INT);

        $sql->bindValue(":ID", $vendedor->getIdVendedor());
        return $sql;

    }

}
