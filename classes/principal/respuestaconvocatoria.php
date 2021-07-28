
<?php
class RespuestaConvocatoria
{
    private $idrespuestaconvocatoria;
    private $respuesta;
    private $pregunta;
    private $persona;
    private $tipo;

    public function setIdRespuestaConvocatoria($idrespuestaconvocatoria)
    {
        $this->idrespuestaconvocatoria = $idrespuestaconvocatoria;
    }
    public function getIdRespuestaConvocatoria()
    {
        return $this->idrespuestaconvocatoria;
    }

    public function setPregunta($pregunta)
    {
        $this->pregunta = $pregunta;
    }
    public function getPregunta()
    {
        return $this->pregunta;
    }
    public function setPersona($persona)
    {
        $this->persona = $persona;
    }
    public function getPersona()
    {
        return $this->persona;
    }

    public function setRespuesta($respuesta)
    {
        $this->respuesta = $respuesta;
    }
    public function getRespuesta()
    {
        return $this->respuesta;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function __toString()
    {
        return
        array("pregunta" => $this->pregunta,
            "persona" => $this->persona,
            "tipo" => $this->tipo,
            "respuesta" => $this->respuesta,
            "idrespuestaconvocatoria" => $this->idrespuestaconvocatoria,
        );
    }
}