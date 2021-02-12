
<?php
class VideoSubTitulo
{
    private $idvideoSubTitulo;
    private $nombre;
    private $codigo;
    private $imagen;

    private $subTitulo;

    public function setIdVideoSubTitulo($idvideoSubTitulo)
    {
        $this->idvideoSubTitulo = $idvideoSubTitulo;
    }
    public function getIdVideoSubTitulo()
    {
        return $this->idvideoSubTitulo;
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }
    public function getCodigo()
    {
        return $this->codigo;
    }
    public function setSubTitulo($subTitulo)
    {
        $this->subTitulo = $subTitulo;
    }
    public function getSubTitulo()
    {
        return $this->subTitulo;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
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
        array("nombre" => $this->nombre,
            "subTitulo" => $this->subTitulo,
            "idvideoSubTitulo" => $this->idvideoSubTitulo,
            "codigo" => $this->codigo,
            "imagen" => $this->imagen,

        );
    }
}