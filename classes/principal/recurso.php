
<?php
class Recurso
{
    private $idrecurso;
    private $nombre;
    private $imagen;
    private $disponible;
    private $subtitulo;
    private $cuenta;
    private $registro;
    private $pagina;

    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }
    public function setIdRecurso($idrecurso)
    {
        $this->idrecurso = $idrecurso;
    }
    public function getIdRecurso()
    {
        return $this->idrecurso;
    }
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
    public function getImagen()
    {
        return $this->imagen;
    }
    public function setSubTitulo($subtitulo)
    {
        $this->subtitulo = $subtitulo;
    }
    public function getSubTitulo()
    {
        return $this->subtitulo;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setDisponible($disponible)
    {
        $this->disponible = $disponible;
    }
    public function getDisponible()
    {
        return $this->disponible;
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
    public function __toString()
    {
        return
        array("disponible" => $this->disponible,
            "nombre" => $this->nombre,
            "subtitulo" => $this->subtitulo,
            "idrecurso" => $this->idrecurso,
            "imagen" => $this->imagen,
            "cuenta" => $this->cuenta,

        );
    }
}