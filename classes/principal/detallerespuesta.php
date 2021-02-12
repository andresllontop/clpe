
<?php
class DetalleRespuesta
{
    private $iddetallerespuesta;
    private $respuesta;
    private $descripcion;
    private $subtitulo;
    private $pregunta;
    private $estado;
    private $tipo;
    private $test;

    public function setIdDetalleRespuesta($iddetallerespuesta)
    {
        $this->iddetallerespuesta = $iddetallerespuesta;
    }
    public function getIdDetalleRespuesta()
    {
        return $this->iddetallerespuesta;
    }
    public function setSubtitulo($subtitulo)
    {
        $this->subtitulo = $subtitulo;
    }
    public function getSubtitulo()
    {
        return $this->subtitulo;
    }
    public function setRespuesta($respuesta)
    {
        $this->respuesta = $respuesta;
    }
    public function getRespuesta()
    {
        return $this->respuesta;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setTest($test)
    {
        $this->test = $test;
    }
    public function getTest()
    {
        return $this->test;
    }
    public function setPregunta($pregunta)
    {
        $this->pregunta = $pregunta;
    }
    public function getPregunta()
    {
        return $this->pregunta;
    }
    public function __toString()
    {
        return
        array("respuesta" => $this->respuesta,
            "pregunta" => $this->pregunta,
            "test" => $this->test,
            "tipo" => $this->tipo,
            "estado" => $this->estado,
            "descripcion" => $this->descripcion,
            "subtitulo" => $this->subtitulo,
            "iddetallerespuesta" => $this->iddetallerespuesta,
        );
    }
}