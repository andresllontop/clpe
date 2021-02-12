
<?php
class Audio
{
    private $idaudio;
    private $nombre;
    private $cuenta;
    private $subTitulo;

    public function setIdaudio($idaudio)
    {
        $this->idaudio = $idaudio;
    }
    public function getIdaudio()
    {
        return $this->idaudio;
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

    public function setSubTitulo($subTitulo)
    {
        $this->subTitulo = $subTitulo;
    }
    public function getSubTitulo()
    {
        return $this->subTitulo;
    }

    public function __toString()
    {
        return
        array(
            "idaudio" => $this->idaudio,
            "nombre" => $this->nombre,
            "cuenta" => $this->cuenta,
            "subTitulo" => $this->subTitulo,
        );
    }
}