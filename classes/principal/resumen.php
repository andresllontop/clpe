
<?php
class Resumen
{
    private $idresumen;
    private $descripcion;
    private $cuenta;
    private $subTitulo;

    public function setIdresumen($idresumen)
    {
        $this->idresumen = $idresumen;
    }
    public function getIdresumen()
    {
        return $this->idresumen;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
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
            "idresumen" => $this->idresumen,
            "descripcion" => $this->descripcion,
            "cuenta" => $this->cuenta,
            "subTitulo" => $this->subTitulo,
        );
    }
}