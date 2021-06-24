
<?php
class Cliente
{
    private $idcliente;
    private $nombre;
    private $telefono;
    private $apellido;
    private $cuenta;
    private $ocupacion;
    private $pais;
    private $tarea;
    private $vendedor;
    private $tipomedio;
    private $estado = 0;
    private $fecha;

    public function setIdCliente($idcliente)
    {
        $this->idcliente = $idcliente;
    }
    public function getIdCliente()
    {
        return $this->idcliente;
    }
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
    public function getTelefono()
    {
        return $this->telefono;
    }
    public function setOcupacion($ocupacion)
    {
        $this->ocupacion = $ocupacion;
    }
    public function getOcupacion()
    {
        return $this->ocupacion;
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

    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }
    public function setPais($pais)
    {
        $this->pais = $pais;
    }
    public function getPais()
    {
        return $this->pais;
    }
    public function setTarea($tarea)
    {
        $this->tarea = $tarea;
    }
    public function getTarea()
    {
        return $this->tarea;
    }
    public function setVendedor($vendedor)
    {
        $this->vendedor = $vendedor;
    }
    public function getVendedor()
    {
        return $this->vendedor;
    }

    public function setTipoMedio($tipomedio)
    {
        $this->tipomedio = $tipomedio;
    }
    public function getTipoMedio()
    {
        return $this->tipomedio;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    public function __toString()
    {
        return
        array("nombre" => $this->nombre,
            "apellido" => $this->apellido,
            "estado" => $this->estado,
            "fecha" => $this->fecha,
            "cuenta" => $this->cuenta,
            "tipomedio" => $this->tipomedio,
            "vendedor" => $this->vendedor,
            "tarea" => $this->tarea,
            "ocupacion" => $this->ocupacion,
            "idcliente" => $this->idcliente,
            "telefono" => $this->telefono,
            "pais" => $this->pais,
        );
    }
}