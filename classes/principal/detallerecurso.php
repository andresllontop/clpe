
<?php
class DetalleRecurso
{
    private $iddetallerecurso;
    private $descripcion;
    private $archivo;
    private $tipo;
    private $recurso;

    public function setIdDetalleRecurso($iddetallerecurso)
    {
        $this->iddetallerecurso = $iddetallerecurso;
    }
    public function getIdDetalleRecurso()
    {
        return $this->iddetallerecurso;
    }
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }
    public function getArchivo()
    {
        return $this->archivo;
    }
    public function setRecurso($recurso)
    {
        $this->recurso = $recurso;
    }
    public function getRecurso()
    {
        return $this->recurso;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function __toString()
    {
        return
        array("tipo" => $this->tipo,
            "descripcion" => $this->descripcion,
            "recurso" => $this->recurso,
            "iddetallerecurso" => $this->iddetallerecurso,
            "archivo" => $this->archivo,

        );
    }
}