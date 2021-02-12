
<?php
class Convocatoria
{
    private $idconvocatoria;
    private $estado;
    private $descripcion;
    private $cantidad;
    private $codigo;
    private $fecha;
    private $imagen;

    public function setIdConvocatoria($idconvocatoria)
    {
        $this->idconvocatoria = $idconvocatoria;
    }
    public function getIdConvocatoria()
    {
        return $this->idconvocatoria;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }
    public function getCodigo()
    {
        return $this->codigo;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }
    public function getCantidad()
    {
        return $this->cantidad;
    }
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
    public function getImagen()
    {
        return $this->imagen;
    }

    public function __toString()
    {
        return
        array("estado" => $this->estado,
            "fecha" => $this->fecha,
            "codigo" => $this->codigo,
            "imagen" => $this->imagen,
            "cantidad" => $this->cantidad,
            "descripcion" => $this->descripcion,
            "idconvocatoria" => $this->idconvocatoria,
        );
    }
}