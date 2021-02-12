
<?php
class Bitacora
{
    private $idbitacora;
    private $estado;
    private $fecha_inicio;
    private $fecha_fin;
    private $tipo;
    private $cuenta;

    public function setIdbitacora($idbitacora)
    {
        $this->idbitacora = $idbitacora;
    }
    public function getIdbitacora()
    {
        return $this->idbitacora;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function setFecha_Inicio($fecha_inicio)
    {
        $this->fecha_inicio = $fecha_inicio;
    }
    public function getFecha_Inicio()
    {
        return $this->fecha_inicio;
    }
    public function setFecha_Fin($fecha_fin)
    {
        $this->fecha_fin = $fecha_fin;
    }
    public function getFecha_Fin()
    {
        return $this->fecha_fin;
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

    public function __toString()
    {
        return
        array("tipo" => $this->tipo,
            "cuenta" => $this->cuenta,
            "fecha_inicio" => $this->fecha_inicio,
            "fecha_fin" => $this->fecha_fin,
            "idbitacora" => $this->idbitacora,
            "estado" => $this->estado,
        );
    }
}