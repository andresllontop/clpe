
<?php
class Noticia
{
    private $idnoticia;
    private $titulo;
    private $descripcion;
    private $imagen;

    public function setIdnoticia($idnoticia)
    {
        $this->idnoticia = $idnoticia;
    }
    public function getIdnoticia()
    {
        return $this->idnoticia;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
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
        array(
            "idnoticia" => $this->idnoticia,
            "titulo" => $this->titulo,
            "imagen" => $this->imagen,
            "descripcion" => $this->descripcion,
        );
    }
}