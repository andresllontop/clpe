
<?php
class SubItem
{
    private $idsubitem;
    private $titulo;
    private $detalle;
    private $imagen;
    private $tipo;
    private $curso;
    private $video;

    public function setIdsubitem($idsubitem)
    {
        $this->idsubitem = $idsubitem;
    }
    public function getIdsubitem()
    {
        return $this->idsubitem;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }
    public function getDetalle()
    {
        return $this->detalle;
    }
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
    public function getImagen()
    {
        return $this->imagen;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setVideo($video)
    {
        $this->video = $video;
    }
    public function getVideo()
    {
        return $this->video;
    }
    public function setCurso($curso)
    {
        $this->curso = $curso;
    }
    public function getCurso()
    {
        return $this->curso;
    }

    public function __toString()
    {
        return
        array(
            "idsubitem" => $this->idsubitem,
            "titulo" => $this->titulo,
            "detalle" => $this->detalle,
            "imagen" => $this->imagen,
            "video" => $this->video,
            "curso" => $this->curso,
            "tipo" => $this->tipo,

        );
    }
}