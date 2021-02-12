
<?php
class RecursoCliente
{
    private $idrecursoCliente;
    private $estado;
    private $cuenta;
    private $restriccion;

    public function setIdrecursoCliente($idrecursoCliente)
    {
        $this->idrecursoCliente = $idrecursoCliente;
    }
    public function getIdrecursoCliente()
    {
        return $this->idrecursoCliente;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function setRestriccion($restriccion)
    {
        $this->restriccion = $restriccion;
    }
    public function getRestriccion()
    {
        return $this->restriccion;
    }

    public function __toString()
    {
        return
        array(
            "idrecursoCliente" => $this->idrecursoCliente,
            "estado" => $this->estado,
            "cuenta" => $this->cuenta,
            "restriccion" => $this->restriccion,
        );
    }
}