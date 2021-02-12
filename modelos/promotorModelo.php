<?php

require_once './core/mainModel.php';

class promotorModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_promotor_modelo($conexion, $promotor)
    {
        $sql = $conexion->prepare("INSERT INTO docente
        (nombres,apellidos,email,descripcion,historia,foto,fotoPortada,youtube)
         VALUES(:Nombre,:Apellido,:Email,:Descripcion,:Historia,:Foto,:FotoPortada,:Youtube)");
        $sql->bindValue(":Nombre", $promotor->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Apellido", $promotor->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(":Email", $promotor->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $promotor->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Historia", $promotor->getHistoria(), PDO::PARAM_STR);
        $sql->bindValue(":Youtube", $promotor->getYoutube(), PDO::PARAM_STR);
        $sql->bindValue(":Foto", $promotor->getFoto(), PDO::PARAM_STR);
        $sql->bindValue(":FotoPortada", $promotor->getFotoPortada(), PDO::PARAM_STR);

        return $sql;

    }
    protected function eliminar_promotor_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `docente` WHERE
         iddocente=:IDpromotor ");
        $sql->bindValue(":IDpromotor", $codigo, PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_promotor_modelo($conexion, $tipo, $promotor)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(iddocente) AS CONTADOR FROM `docente`
                    WHERE iddocente=:IDpromotor");
                    $stmt->bindValue(":IDpromotor", $promotor->getIdPromotor(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `docente` WHERE iddocente=:IDpromotor");
                            $stmt->bindValue(":IDpromotor", $promotor->getIdPromotor(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insPromotor = new Promotor();
                                $insPromotor->setIdPromotor($row['iddocente']);
                                $insPromotor->setNombre($row['nombres']);
                                $insPromotor->setApellido($row['apellidos']);
                                $insPromotor->setEmail($row['email']);
                                $insPromotor->setYoutube($row['youtube']);
                                $insPromotor->setTelefono($row['celular']);
                                $insPromotor->setOcupacion($row['especialidad']);
                                $insPromotor->setDescripcion($row['descripcion']);
                                $insPromotor->setHistoria($row['historia']);
                                $insPromotor->setFoto($row['foto']);
                                $insPromotor->setFotoPortada($row['fotoPortada']);
                                $insBeanPagination->setList($insPromotor->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(iddocente) AS CONTADOR FROM `docente`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `docente` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insPromotor = new Promotor();
                                $insPromotor->setIdPromotor($row['iddocente']);
                                $insPromotor->setNombre($row['nombres']);
                                $insPromotor->setApellido($row['apellidos']);
                                $insPromotor->setEmail($row['email']);
                                $insPromotor->setYoutube($row['youtube']);
                                $insPromotor->setTelefono($row['celular']);
                                $insPromotor->setOcupacion($row['especialidad']);
                                $insPromotor->setDescripcion($row['descripcion']);
                                $insPromotor->setHistoria($row['historia']);
                                $insPromotor->setFoto($row['foto']);
                                $insPromotor->setFotoPortada($row['fotoPortada']);
                                $insBeanPagination->setList($insPromotor->__toString());
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

    }protected function actualizar_promotor_modelo($conexion, $promotor)
    {
        $sql = $conexion->prepare("UPDATE `docente`
        SET nombres=:Nombre,apellidos=:Apellido,email=:Email,
        descripcion=:Descripcion,historia=:Historia,
        foto=:Foto,fotoPortada=:FotoPortada,youtube=:Youtube
        WHERE iddocente=:ID");
        $sql->bindValue(":Nombre", $promotor->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Apellido", $promotor->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(":Email", $promotor->getEmail(), PDO::PARAM_STR);
        // $sql->bindValue(":Celular", $datos['Celular']);
        // $sql->bindValue(":Especialidad", $datos['Especialidad']);
        $sql->bindValue(":Descripcion", $promotor->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Historia", $promotor->getHistoria(), PDO::PARAM_STR);
        $sql->bindValue(":Youtube", $promotor->getYoutube(), PDO::PARAM_STR);
        $sql->bindValue(":Foto", $promotor->getFoto(), PDO::PARAM_STR);
        $sql->bindValue(":FotoPortada", $promotor->getFotoPortada(), PDO::PARAM_STR);

        $sql->bindValue(":ID", $promotor->getIdPromotor());
        return $sql;

    }

}
