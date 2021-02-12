
<?php
class Album
{
    private $idalbum;
    private $nombre;
    private $desde;
    private $hasta;
    private $video;
    private $tipo;

    public function setIdalbum($idalbum)
    {
        $this->idalbum = $idalbum;
    }
    public function getIdalbum()
    {
        return $this->idalbum;
    }
    public function setDesde($desde)
    {
        $this->desde = $desde;
    }
    public function getDesde()
    {
        return $this->desde;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setVideo($video)
    {
        $this->video = $video;
    }
    public function getVideo()
    {
        return $this->video;
    }
    public function setHasta($hasta)
    {
        $this->hasta = $hasta;
    }
    public function getHasta()
    {
        return $this->hasta;
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
        array(
            "tipo" => $this->tipo,
            "hasta" => $this->hasta,
            "video" => $this->video,
            "idalbum" => $this->idalbum,
            "desde" => $this->desde,
            "nombre" => $this->nombre,
        );
    }
}