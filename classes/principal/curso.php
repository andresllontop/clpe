
<?php
class Curso
{
    private $idcurso;
    private $titulo;
    private $precio;
    private $descripcion;
    private $descuento;
    private $tipo;
    private $imagen;
    private $portada;
    private $presentacion;

    public function setIdcurso($idcurso)
    {
        $this->idcurso = $idcurso;
    }
    public function getIdcurso()
    {
        return $this->idcurso;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    public function getPrecio()
    {
        return $this->precio;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;
    }
    public function getDescuento()
    {
        return $this->descuento;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }

    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
    public function getImagen()
    {
        return $this->imagen;
    }
    public function setPortada($portada)
    {
        $this->portada = $portada;
    }
    public function getPortada()
    {
        return $this->portada;
    }
    public function setPresentacion($presentacion)
    {
        $this->presentacion = $presentacion;
    }
    public function getPresentacion()
    {
        return $this->presentacion;
    }

    public function __toString()
    {
        return
        array("descuento" => $this->descuento,
            "tipo" => $this->tipo,
            "imagen" => $this->imagen,
            "presentacion" => $this->presentacion,
            "portada" => $this->portada,
            "precio" => $this->precio,
            "descripcion" => $this->descripcion,
            "idcurso" => $this->idcurso,
            "titulo" => $this->titulo,
        );
    }
}