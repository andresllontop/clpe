
<?php
class Video
{
    private $idvideo;
    private $nombre;
    private $imagen;
    private $enlace;
    private $archivo;
    private $ubicacion;

    public function setIdvideo($idvideo)
    {
        $this->idvideo = $idvideo;
    }
    public function getIdvideo()
    {
        return $this->idvideo;
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
    public function setEnlace($enlace)
    {
        $this->enlace = $enlace;
    }
    public function getEnlace()
    {
        return $this->enlace;
    }
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }
    public function getArchivo()
    {
        return $this->archivo;
    }
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;
    }
    public function getUbicacion()
    {
        return $this->ubicacion;
    }
    public function __toString()
    {
        return
        array(
            "idvideo" => $this->idvideo,
            "nombre" => $this->nombre,
            "enlace" => $this->enlace,
            "imagen" => $this->imagen,
            "archivo" => $this->archivo,
            "ubicacion" => $this->ubicacion,
        );
    }
}