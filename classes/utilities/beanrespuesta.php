
<?php
class BeanRespuesta
{
    private $idbeanrespuesta;
    private $respuesta;
    private $listdetalle;

    public function setIdBeanRespuesta($idbeanrespuesta)
    {
        $this->idbeanrespuesta = $idbeanrespuesta;
    }
    public function getIdBeanRespuesta()
    {
        return $this->idbeanrespuesta;
    }
    public function setRespuesta($respuesta)
    {
        $this->respuesta = $respuesta;
    }
    public function getRespuesta()
    {
        return $this->respuesta;
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
        array("respuesta" => $this->respuesta,
            "listdetalle" => $this->listdetalle,
            "idbeanrespuesta" => $this->idbeanrespuesta,
        );
    }
}