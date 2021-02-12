
<?php
class BeanDetalleConvocatoria
{
    private $convocatoria;
    private $listdetalle;

    public function setConvocatoria($convocatoria)
    {
        $this->convocatoria = $convocatoria;
    }
    public function getConvocatoria()
    {
        return $this->convocatoria;
    }

    public function setListDetalle($listdetalle)
    {
        $this->listdetalle = $listdetalle;
    }
    public function getListDetalle()
    {
        return $this->listdetalle;
    }

    public function __toString()
    {
        return
        array("convocatoria" => $this->convocatoria,
            "listdetalle" => $this->listdetalle,
        );
    }
}