
<?php
class Titulo
{
    private $idtitulo;
    private $nombre;
    private $codigo;
    private $descripcion;
    private $estado;
    private $libro;
    private $pdf;
    private $imagen;
    private $registro;
    private $pagina;
    public function setIdTitulo($idtitulo)
    {
        $this->idtitulo = $idtitulo;
    }
    public function getIdTitulo()
    {
        return $this->idtitulo;
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }
    public function getCodigo()
    {
        return $this->codigo;
    }
    public function setLibro($libro)
    {
        $this->libro = $libro;
    }
    public function getLibro()
    {
        return $this->libro;
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
            "libro" => $this->libro,
            "pdf" => $this->pdf,
            "idtitulo" => $this->idtitulo,
            "codigo" => $this->codigo,
            "descripcion" => $this->descripcion,
            "imagen" => $this->imagen,
        );
    }
}