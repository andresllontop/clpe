
<?php
class Blog
{
    private $idblog;
    private $titulo;
    private $resumen;
    private $descripcion;
    private $archivo;
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

    public function __toString()
    {
        return
        array("archivo" => $this->archivo,
            "tipoArchivo" => $this->tipoArchivo,
            "comentario" => $this->comentario,
            "resumen" => $this->resumen,
            "descripcion" => $this->descripcion,
            "idblog" => $this->idblog,
            "titulo" => $this->titulo,
        );
    }
}