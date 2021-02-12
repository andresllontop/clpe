
<?php
class Nacionalidad
{
    private $idnacionalidad;
    private $pais;
    private $codigo;
    private $icono;

    public function setIdnacionalidad($idnacionalidad)
    {
        $this->idnacionalidad = $idnacionalidad;
    }
    public function getIdnacionalidad()
    {
        return $this->idnacionalidad;
    }
    public function setPais($pais)
    {
        $this->pais = $pais;
    }
    public function getPais()
    {
        return $this->pais;
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }
    public function getCodigo()
    {
        return $this->codigo;
    }
    public function setIcono($icono)
    {
        $this->icono = $icono;
    }
    public function getIcono()
    {
        return $this->icono;
    }

    public function __toString()
    {
        return
        array(
            "idnacionalidad" => $this->idnacionalidad,
            "pais" => $this->pais,
            "icono" => $this->icono,
            "codigo" => $this->codigo,
        );
    }
}