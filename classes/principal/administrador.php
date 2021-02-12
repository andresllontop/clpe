
<?php
class Administrador
{
    private $idadministrador;
    private $nombre;
    private $telefono;
    private $apellido;
    private $cuenta;
    private $ocupacion;
    private $pais;

    public function setIdAdministrador($idadministrador)
    {
        $this->idadministrador = $idadministrador;
    }
    public function getIdAdministrador()
    {
        return $this->idadministrador;
    }
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
    public function getTelefono()
    {
        return $this->telefono;
    }
    public function setOcupacion($ocupacion)
    {
        $this->ocupacion = $ocupacion;
    }
    public function getOcupacion()
    {
        return $this->ocupacion;
    }
    public function setPais($pais)
    {
        $this->pais = $pais;
    }
    public function getPais()
    {
        return $this->pais;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }
    public function getApellido()
    {
        return $this->apellido;
    }

    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function __toString()
    {
        return
        array("nombre" => $this->nombre,
            "apellido" => $this->apellido,
            "cuenta" => $this->cuenta,
            "ocupacion" => $this->ocupacion,
            "pais" => $this->pais,
            "idadministrador" => $this->idadministrador,
            "telefono" => $this->telefono,
        );
    }
}