
<?php
class Testimonio
{
    private $idtestimonio;
    private $titulo;
    private $descripcion;
    private $archivo;
    private $imagen;
    private $enlaceYoutube;
    private $estado;

    public function setIdtestimonio($idtestimonio)
    {
        $this->idtestimonio = $idtestimonio;
    }
    public function getIdtestimonio()
    {
        return $this->idtestimonio;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
    public function getImagen()
    {
        return $this->imagen;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }
    public function getArchivo()
    {
        return $this->archivo;
    }

    public function setEnlaceYoutube($enlaceYoutube)
    {
        $this->enlaceYoutube = $enlaceYoutube;
    }
    public function getEnlaceYoutube()
    {
        return $this->enlaceYoutube;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }

    public function __toString()
    {
        return
        array("archivo" => $this->archivo,
            "enlaceYoutube" => $this->enlaceYoutube,
            "estado" => $this->estado,
            "imagen" => $this->imagen,
            "descripcion" => $this->descripcion,
            "idtestimonio" => $this->idtestimonio,
            "titulo" => $this->titulo,
        );
    }
}