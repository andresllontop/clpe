
<?php
class Vendedor
{
    private $idvendedor;
    private $nombre;
    private $telefono;
    private $apellido;
    private $pais;
    private $tipo;
    private $empresa;
    private $codigo;

    public function setIdVendedor($idvendedor)
    {
        $this->idvendedor = $idvendedor;
    }
    public function getIdVendedor()
    {
        return $this->idvendedor;
    }
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
    public function getTelefono()
    {
        return $this->telefono;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }
    public function getEmpresa()
    {
        return $this->empresa;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }
    public function getApellido()
    {
        return $this->apellido;
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

    public function __toString()
    {
        return
        array("nombre" => $this->nombre,
            "apellido" => $this->apellido,
            "pais" => $this->pais,
            "tipo" => $this->tipo,
            "empresa" => $this->empresa,
            "idvendedor" => $this->idvendedor,
            "telefono" => $this->telefono,
            "codigo" => $this->codigo,

        );
    }
}