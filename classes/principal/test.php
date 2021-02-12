
<?php
class Test
{
    private $idtest;
    private $tipo;
    private $descripcion;
    private $nombre;
    private $titulo;
    private $sub;
    private $subcodigo;
    private $cantidad;
    private $imagen;

    public function setIdTest($idtest)
    {
        $this->idtest = $idtest;
    }
    public function getIdTest()
    {
        return $this->idtest;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setSub($sub)
    {
        $this->sub = $sub;
    }
    public function getSub()
    {
        return $this->sub;
    }
    public function setSubCodigo($subcodigo)
    {
        $this->subcodigo = $subcodigo;
    }
    public function getSubCodigo()
    {
        return $this->subcodigo;
    }
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }
    public function getCantidad()
    {
        return $this->cantidad;
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
        array("tipo" => $this->tipo,
            "imagen" => $this->imagen,
            "cantidad" => $this->cantidad,
            "subcodigo" => $this->subcodigo,
            "sub" => $this->sub,
            "nombre" => $this->nombre,
            "descripcion" => $this->descripcion,
            "titulo" => $this->titulo,
            "idtest" => $this->idtest,
        );
    }
}