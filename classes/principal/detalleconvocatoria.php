
<?php
class DetalleConvocatoria
{
    private $iddetalleconvocatoria;
    private $convocatoria;
    private $descripcion;
    private $tipo;

    public function setIdDetalleConvocatoria($iddetalleconvocatoria)
    {
        $this->iddetalleconvocatoria = $iddetalleconvocatoria;
    }
    public function getIdDetalleConvocatoria()
    {
        return $this->iddetalleconvocatoria;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setConvocatoria($convocatoria)
    {
        $this->convocatoria = $convocatoria;
    }
    public function getConvocatoria()
    {
        return $this->convocatoria;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function __toString()
    {
        return
        array("convocatoria" => $this->convocatoria,
            "descripcion" => $this->descripcion,
            "tipo" => $this->tipo,
            "iddetalleconvocatoria" => $this->iddetalleconvocatoria,
        );
    }
}