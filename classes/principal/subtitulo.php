
<?php
class SubTitulo
{
    private $idsubTitulo;
    private $nombre;
    private $codigo;
    private $descripcion;
    private $estado;
    private $titulo;
    private $pdf;
    private $imagen;
    private $registro;
    private $pagina;

    public function setIdSubTitulo($idsubTitulo)
    {
        $this->idsubTitulo = $idsubTitulo;
    }
    public function getIdSubTitulo()
    {
        return $this->idsubTitulo;
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }
    public function getCodigo()
    {
        return $this->codigo;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function setPdf($pdf)
    {
        $this->pdf = $pdf;
    }
    public function getPdf()
    {
        return $this->pdf;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
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

    public function setRegistro($registro)
    {
        $this->registro = $registro;
    }
    public function getRegistro()
    {
        return $this->registro;
    }
    public function setPagina($pagina)
    {
        $this->pagina = $pagina;
    }
    public function getPagina()
    {
        return $this->pagina;
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
        array("nombre" => $this->nombre,
            "estado" => $this->estado,
            "titulo" => $this->titulo,
            "pdf" => $this->pdf,
            "idsubTitulo" => $this->idsubTitulo,
            "codigo" => $this->codigo,
            "descripcion" => $this->descripcion,
            "imagen" => $this->imagen,

        );
    }
}