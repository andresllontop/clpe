
<?php
class VideoUsuario
{
    private $idvideoUsuario;
    private $comentario;
    private $video;
    private $cuenta;
    private $subTitulo;

    public function setIdvideoUsuario($idvideoUsuario)
    {
        $this->idvideoUsuario = $idvideoUsuario;
    }
    public function getIdvideoUsuario()
    {
        return $this->idvideoUsuario;
    }
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }
    public function getComentario()
    {
        return $this->comentario;
    }
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function setSubTitulo($subTitulo)
    {
        $this->subTitulo = $subTitulo;
    }
    public function getSubTitulo()
    {
        return $this->subTitulo;
    }
    public function setVideo($video)
    {
        $this->video = $video;
    }
    public function getVideo()
    {
        return $this->video;
    }
    public function __toString()
    {
        return
        array(
            "idvideoUsuario" => $this->idvideoUsuario,
            "comentario" => $this->comentario,
            "video" => $this->video,
            "cuenta" => $this->cuenta,
            "subTitulo" => $this->subTitulo,
        );
    }
}