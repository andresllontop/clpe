
<?php
class Prospecto
{
    private $idprospecto;
    private $nombre;
    private $cuenta;
    private $documento;
    private $pais;
    private $telefono;
    private $email;
    private $especialidad;
    private $idFatherProspecto;

    public function setIdprospecto($idprospecto)
    {
        $this->idprospecto = $idprospecto;
    }
    public function getIdprospecto()
    {
        return $this->idprospecto;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }
    public function getDocumento()
    {
        return $this->documento;
    }
    public function setPais($pais)
    {
        $this->pais = $pais;
    }
    public function getPais()
    {
        return $this->pais;
    }
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
    public function getTelefono()
    {
        return $this->telefono;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEspecialidad($especialidad)
    {
        $this->especialidad = $especialidad;
    }
    public function getEspecialidad()
    {
        return $this->especialidad;
    }
    public function setIdFatherProspecto($idFatherProspecto)
    {
        $this->idFatherProspecto = $idFatherProspecto;
    }
    public function getIdFatherProspecto()
    {
        return $this->idFatherProspecto;
    }
    public function __toString()
    {
        return
        array(
            "idprospecto" => $this->idprospecto,
            "nombre" => $this->nombre,
            "cuenta" => $this->cuenta,
            "idFatherProspecto" => $this->idFatherProspecto,
            "telefono" => $this->telefono,
            "email" => $this->email,
            "especialidad" => $this->especialidad,
            "pais" => $this->pais,
            "documento" => $this->documento,
        );
    }
}