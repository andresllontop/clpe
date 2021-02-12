
<?php
class Visita
{
    private $idvisita;
    private $ip;
    private $pagina;
    private $contador;
    private $info;
    private $fecha;
    private $fecha_fin;

    public function setIdvisita($idvisita)
    {
        $this->idvisita = $idvisita;
    }
    public function getIdvisita()
    {
        return $this->idvisita;
    }
    public function setIp($ip)
    {
        $this->ip = $ip;
    }
    public function getIp()
    {
        return $this->ip;
    }
    public function setPagina($pagina)
    {
        $this->pagina = $pagina;
    }
    public function getPagina()
    {
        return $this->pagina;
    }
    public function setContador($contador)
    {
        $this->contador = $contador;
    }
    public function getContador()
    {
        return $this->contador;
    }
    public function setInfo($info)
    {
        $this->info = $info;
    }
    public function getInfo()
    {
        return $this->info;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    public function setFecha_Fin($fecha_fin)
    {
        $this->fecha_fin = $fecha_fin;
    }
    public function getFecha_Fin()
    {
        return $this->fecha_fin;
    }
    public function __toString()
    {
        return
        array(
            "idvisita" => $this->idvisita,
            "ip" => $this->ip,
            "info" => $this->info,
            "contador" => $this->contador,
            "pagina" => $this->pagina,
            "fecha" => $this->fecha,
            "fecha_fin" => $this->fecha_fin,
        );
    }
}