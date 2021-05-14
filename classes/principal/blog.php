
<?php
class Blog
{
    private $idblog;
    private $titulo;
    private $resumen;
    private $descripcion;
    private $descripcionAutor;
    private $archivo;
    private $autor;
    private $foto;
    private $tipoArchivo;
    private $comentario;

    public function setIdblog($idblog)
    {
        $this->idblog = $idblog;
    }
    public function getIdblog()
    {
        return $this->idblog;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function setResumen($resumen)
    {
        $this->resumen = $resumen;
    }
    public function getResumen()
    {
        return $this->resumen;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setDescripcionAutor($descripcionAutor)
    {
        $this->descripcionAutor = $descripcionAutor;
    }
    public function getDescripcionAutor()
    {
        return $this->descripcionAutor;
    }
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }
    public function getArchivo()
    {
        return $this->archivo;
    }

    public function setTipoArchivo($tipoArchivo)
    {
        $this->tipoArchivo = $tipoArchivo;
    }
    public function getTipoArchivo()
    {
        return $this->tipoArchivo;
    }

    public function setComentario($comentario)
    {
        $this->comentario = $comentario;
    }
    public function getComentario()
    {
        return $this->comentario;
    }
    public function setAutor($autor)
    {
        $this->autor = $autor;
    }
    public function getAutor()
    {
        return $this->autor;
    }public function setFoto($foto)
    {
        $this->foto = $foto;
    }
    public function getFoto()
    {
        return $this->foto;
    }
    public function __toString()
    {
        return
        array("archivo" => $this->archivo,
            "tipoArchivo" => $this->tipoArchivo,
            "autor" => $this->autor,
            "foto" => $this->foto,
            "comentario" => $this->comentario,
            "resumen" => $this->resumen,
            "descripcion" => $this->descripcion,
            "descripcionAutor" => $this->descripcionAutor,
            "idblog" => $this->idblog,
            "titulo" => $this->titulo,
        );
    }
}