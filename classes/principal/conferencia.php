
<?php
class Conferencia
{
    private $idconferencia;
    private $link;
    private $titulo;
    private $fecha;
    private $descripcion;
    private $estado;
    private $imagen;

    public function setIdconferencia($idconferencia)
    {
        $this->idconferencia = $idconferencia;
    }
    public function getIdconferencia()
    {
        return $this->idconferencia;
    }
    public function setLink($link)
    {
        $this->link = $link;
    }
    public function getLink()
    {
        return $this->link;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
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

    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
    public function getImagen()
    {
        return $this->imagen;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function __toString()
    {
        return
        array("estado" => $this->estado,
            "titulo" => $this->titulo,
            "imagen" => $this->imagen,
            "fecha" => $this->fecha,
            "descripcion" => $this->descripcion,
            "idconferencia" => $this->idconferencia,
            "link" => $this->link,
        );
    }
}