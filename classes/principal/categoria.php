
<?php
class Categoria
{
    private $idcategoria;
    private $nombre;

    public function setIdcategoria($idcategoria)
    {
        $this->idcategoria = $idcategoria;
    }
    public function getIdcategoria()
    {
        return $this->idcategoria;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }

    public function __toString()
    {
        return
        array(
            "idcategoria" => $this->idcategoria,
            "nombre" => $this->nombre,
        );
    }
}