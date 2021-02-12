
<?php
class Item
{
    private $iditem;
    private $nombre;

    public function setIditem($iditem)
    {
        $this->iditem = $iditem;
    }
    public function getIditem()
    {
        return $this->iditem;
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
            "iditem" => $this->iditem,
            "nombre" => $this->nombre,
        );
    }
}