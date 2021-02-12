
<?php
class Publico
{
    private $idpublico;
    private $nombre;
    private $email;
    private $fecha;

    public function setIdpublico($idpublico)
    {
        $this->idpublico = $idpublico;
    }
    public function getIdpublico()
    {
        return $this->idpublico;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function __toString()
    {
        return
        array(
            "idpublico" => $this->idpublico,
            "nombre" => $this->nombre,
            "email" => $this->email,
            "fecha" => $this->fecha,
        );
    }
}