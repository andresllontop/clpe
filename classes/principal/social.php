
<?php
class Social
{
    private $idsocial;
    private $titulo;
    private $fraseTestimonio;
    private $fraseCurso;
    private $parametroCurso;
    private $descripcion;
    private $archivo;
    private $imagenFondo;
    private $tipoArchivo;

    public function setIdsocial($idsocial)
    {
        $this->idsocial = $idsocial;
    }
    public function getIdsocial()
    {
        return $this->idsocial;
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
    public function setFraseTestimonio($fraseTestimonio)
    {
        $this->fraseTestimonio = $fraseTestimonio;
    }
    public function getFraseTestimonio()
    {
        return $this->fraseTestimonio;
    }public function setFraseCurso($fraseCurso)
    {
        $this->fraseCurso = $fraseCurso;
    }
    public function getFraseCurso()
    {
        return $this->fraseCurso;
    }public function setParametroCurso($parametroCurso)
    {
        $this->parametroCurso = $parametroCurso;
    }
    public function getParametroCurso()
    {
        return $this->parametroCurso;
    }public function setImagenFondo($imagenFondo)
    {
        $this->imagenFondo = $imagenFondo;
    }
    public function getImagenFondo()
    {
        return $this->imagenFondo;
    }
    public function __toString()
    {
        return
        array("archivo" => $this->archivo,
            "tipoArchivo" => $this->tipoArchivo,
            "descripcion" => $this->descripcion,
            "idsocial" => $this->idsocial,
            "titulo" => $this->titulo,
            "parametroCurso" => $this->parametroCurso,
            "fraseCurso" => $this->fraseCurso,
            "fraseTestimonio" => $this->fraseTestimonio,
            "imagenFondo" => $this->imagenFondo,
        );
    }
}