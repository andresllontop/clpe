
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
    private $video;
    private $presentacion;
    private $libro;

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

    public function setVideo($video)
    {
        $this->video = $video;
    }
    public function getVideo()
    {
        return $this->video;
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
    public function setLibro($libro)
    {
        $this->libro = $libro;
    }
    public function getLibro()
    {
        return $this->libro;
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
            "video" => $this->video,
            "descripcion" => $this->descripcion,
            "idcurso" => $this->idcurso,
            "titulo" => $this->titulo,
            "libro" => $this->libro,
        );
    }
}