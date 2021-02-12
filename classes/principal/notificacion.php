
<?php
class Notificacion
{
    private $idnotificacion;
    private $rangoInicial;
    private $rangoFinal;
    private $descripcion;
    private $tipo;
    private $fecha;
    private $cuenta;

    public function setIdNotificacion($idnotificacion)
    {
        $this->idnotificacion = $idnotificacion;
    }
    public function getIdNotificacion()
    {
        return $this->idnotificacion;
    }
    public function setRangoInicial($rangoInicial)
    {
        $this->rangoInicial = $rangoInicial;
    }
    public function getRangoInicial()
    {
        return $this->rangoInicial;
    }
    public function setRangoFinal($rangoFinal)
    {
        $this->rangoFinal = $rangoFinal;
    }
    public function getRangoFinal()
    {
        return $this->rangoFinal;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }

    public function __toString()
    {
        return
        array(
            "fecha" => $this->fecha,
            "cuenta" => $this->cuenta,
            "tipo" => $this->tipo,
            "rangoFinal" => $this->rangoFinal,
            "descripcion" => $this->descripcion,
            "idnotificacion" => $this->idnotificacion,
            "rangoInicial" => $this->rangoInicial,
        );
    }
}