
<?php
class BeanPersonaConvocatoria
{
    private $idbeanpersonaconvocatoria;
    private $persona;
    private $listrespuesta;

    public function setIdBeanPersonaConvocatoria($idbeanpersonaconvocatoria)
    {
        $this->idbeanpersonaconvocatoria = $idbeanpersonaconvocatoria;
    }
    public function getIdBeanPersonaConvocatoria()
    {
        return $this->idbeanpersonaconvocatoria;
    }
    public function setPersona($persona)
    {
        $this->persona = $persona;
    }
    public function getPersona()
    {
        return $this->persona;
    }

    public function setListRespuesta($listrespuesta)
    {
        $this->listrespuesta = $listrespuesta;
    }
    public function getListRespuesta()
    {
        return $this->listrespuesta;
    }

    public function __toString()
    {
        return
        array("persona" => $this->persona,
            "listrespuesta" => $this->listrespuesta,
            "idbeanpersonaconvocatoria" => $this->idbeanpersonaconvocatoria,
        );
    }
}