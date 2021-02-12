
<?php
class DetalleTest
{
    private $iddetalletest;
    private $test;
    private $descripcion;
    private $subtitulo;

    public function setIdDetalleTest($iddetalletest)
    {
        $this->iddetalletest = $iddetalletest;
    }
    public function getIdDetalleTest()
    {
        return $this->iddetalletest;
    }
    public function setSubtitulo($subtitulo)
    {
        $this->subtitulo = $subtitulo;
    }
    public function getSubtitulo()
    {
        return $this->subtitulo;
    }
    public function setTest($test)
    {
        $this->test = $test;
    }
    public function getTest()
    {
        return $this->test;
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
        array("test" => $this->test,
            "descripcion" => $this->descripcion,
            "subtitulo" => $this->subtitulo,
            "iddetalletest" => $this->iddetalletest,
        );
    }
}