
<?php
class Leccion
{
    private $idleccion;
    private $cuenta;
    private $subTitulo;
    private $video;
    private $comentario;
    private $estado;
    private $fecha;
    private $pagina;
    private $registro;
    private $libroCode;
    public function setIdleccion($idleccion)
    {
        $this->idleccion = $idleccion;
    }
    public function getIdleccion()
    {
        return $this->idleccion;
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

    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }
    public function getComentario()
    {
        return $this->comentario;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    public function setPagina($pagina)
    {
        $this->pagina = $pagina;
    }
    public function getPagina()
    {
        return $this->pagina;
    }
    public function setRegistro($registro)
    {
        $this->registro = $registro;
    }
    public function getRegistro()
    {
        return $this->registro;
    }
    public function setLibroCode($libroCode)
    {
        $this->libroCode = $libroCode;
    }
    public function getLibroCode()
    {
        return $this->libroCode;
    }
    public function __toString()
    {
        return
        array(
            "idleccion" => $this->idleccion,
            "cuenta" => $this->cuenta,
            "estado" => $this->estado,
            "fecha" => $this->fecha,
            "subTitulo" => $this->subTitulo,
            "video" => $this->video,
            "comentario" => $this->comentario,
            "libroCode" => $this->libroCode,
        );
    }
}