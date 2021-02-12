
<?php
class Tarea
{
    private $idtarea;
    private $cuenta;
    private $subTitulo;
    // 0:leccion; 1:examen capitulo ; 2:examen subtitulo ;
    private $tipo;
    private $fecha;
    private $pagina;
    private $registro;

    public function setIdtarea($idtarea)
    {
        $this->idtarea = $idtarea;
    }
    public function getIdtarea()
    {
        return $this->idtarea;
    }
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function setSubTitulo($subTitulo)
    {
        $this->subTitulo = $subTitulo;
    }
    public function getSubTitulo()
    {
        return $this->subTitulo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    public function setPagina($pagina)
    {
        $this->pagina = $pagina;
    }
    public function getPagina()
    {
        return $this->pagina;
    }
    public function setRegistro($registro)
    {
        $this->registro = $registro;
    }
    public function getRegistro()
    {
        return $this->registro;
    }

    public function __toString()
    {
        return
        array(
            "idtarea" => $this->idtarea,
            "cuenta" => $this->cuenta,
            "subTitulo" => $this->subTitulo,
            "tipo" => $this->tipo,
            "fecha" => $this->fecha,
        );
    }
}