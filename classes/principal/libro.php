
<?php
class Libro
{
    private $idlibro;
    private $nombre;
    private $codigo;
    private $descripcion;
    private $imagen;
    private $imagenOtro;
    private $estado;
    private $categoria;
    private $video;
    private $list = array();
    public function setIdLibro($idlibro)
    {
        $this->idlibro = $idlibro;
    }
    public function getIdLibro()
    {
        return $this->idlibro;
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }
    public function getCodigo()
    {
        return $this->codigo;
    }
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    }
    public function getCategoria()
    {
        return $this->categoria;
    }
    public function setVideo($video)
    {
        $this->video = $video;
    }
    public function getVideo()
    {
        return $this->video;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }

    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function setImagenOtro($imagenOtro)
    {
        $this->imagenOtro = $imagenOtro;
    }
    public function getImagenOtro()
    {
        return $this->imagenOtro;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
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
    public function setList($lista)
    {
        array_push($this->list, $lista);
    }
    public function getList()
    {
        return $this->list;
    }
    public function __toString()
    {
        return
        array("nombre" => $this->nombre,
            "imagenOtro" => $this->imagenOtro,
            "estado" => $this->estado,
            "categoria" => $this->categoria,
            "video" => $this->video,
            "idlibro" => $this->idlibro,
            "codigo" => $this->codigo,
            "descripcion" => $this->descripcion,
            "imagen" => $this->imagen,
            "list" => $this->list,

        );
    }
}