
<?php
class Respuesta
{
    private $idrespuesta;
    private $test;
    private $cuenta;
    private $fecha;
    private $estado;
    private $tipo;
    private $titulo;
    private $pagina;
    private $registro;
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

    public function setIdRespuesta($idrespuesta)
    {
        $this->idrespuesta = $idrespuesta;
    }
    public function getIdRespuesta()
    {
        return $this->idrespuesta;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    public function setTest($test)
    {
        $this->test = $test;
    }
    public function getTest()
    {
        return $this->test;
    }

    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }

    public function __toString()
    {
        return
        array("test" => $this->test,
            "titulo" => $this->titulo,
            "tipo" => $this->tipo,
            "estado" => $this->estado,
            "cuenta" => $this->cuenta,
            "fecha" => $this->fecha,
            "idrespuesta" => $this->idrespuesta,
        );
    }
}