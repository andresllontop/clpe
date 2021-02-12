
<?php
class PersonaConvocatoria
{
    private $idpersonaconvocatoria;
    private $cantidad;
    private $codigo;
    private $ip;
    private $fecha;

    public function setIdPersonaConvocatoria($idpersonaconvocatoria)
    {
        $this->idpersonaconvocatoria = $idpersonaconvocatoria;
    }
    public function getIdPersonaConvocatoria()
    {
        return $this->idpersonaconvocatoria;
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }
    public function getCodigo()
    {
        return $this->codigo;
    }
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }
    public function getCantidad()
    {
        return $this->cantidad;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }
    public function getIp()
    {
        return $this->ip;
    }

    public function __toString()
    {
        return
        array("cantidad" => $this->cantidad,
            "fecha" => $this->fecha,
            "ip" => $this->ip,
            "codigo" => $this->codigo,
            "idpersonaconvocatoria" => $this->idpersonaconvocatoria,
        );
    }
}