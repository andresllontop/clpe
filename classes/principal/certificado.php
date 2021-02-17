
<?php
class Certificado
{
    private $idcertificado;
    private $nombre;
    private $indicador;
    private $cuenta;
    private $fecha;
    private $fechainicial;

    public function setIdcertificado(int $idcertificado)
    {
        $this->idcertificado = $idcertificado;
    }
    public function getIdcertificado()
    {
        return $this->idcertificado;
    }
    public function setIndicador(int $indicador)
    {
        $this->indicador = $indicador;
    }
    public function getIndicador()
    {
        return $this->indicador;
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

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFechaInicial($fechainicial)
    {
        $this->fechainicial = $fechainicial;
    }
    public function getFechaInicial()
    {
        return $this->fechainicial;
    }

    public function __toString()
    {
        return
        array(
            "cuenta" => $this->cuenta,
            "fecha" => $this->fecha,
            "fechainicial" => $this->fechainicial,
            "idcertificado" => $this->idcertificado,
            "indicador" => $this->indicador,
            "nombre" => $this->nombre,
        );
    }
}