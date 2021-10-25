
<?php
class Economico
{
    private $ideconomico;
    private $nombre;
    private $apellido;
    private $telefono;
    private $pais;
    private $banco;
    private $moneda;
    private $comision;
    private $precio;
    private $tipo;
    private $fecha;
    private $voucher;
    private $libro;

    public function setIdeconomico($ideconomico)
    {
        $this->ideconomico = $ideconomico;
    }
    public function getIdeconomico()
    {
        return $this->ideconomico;
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
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
    public function getTelefono()
    {
        return $this->telefono;
    }
    public function setPais($pais)
    {
        $this->pais = $pais;
    }
    public function getPais()
    {
        return $this->pais;
    }

    public function setBanco($banco)
    {
        $this->banco = $banco;
    }
    public function getBanco()
    {
        return $this->banco;
    }

    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    }
    public function getMoneda()
    {
        return $this->moneda;
    }
    public function setComision($comision)
    {
        $this->comision = $comision;
    }
    public function getComision()
    {
        return $this->comision;
    }
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    public function getPrecio()
    {
        return $this->precio;
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
    public function setVoucher($voucher)
    {
        $this->voucher = $voucher;
    }
    public function getVoucher()
    {
        return $this->voucher;
    }
    public function setLibro($libro)
    {
        $this->libro = $libro;
    }
    public function getLibro()
    {
        return $this->libro;
    }
    public function __toString()
    {
        return
        array("pais" => $this->pais,
            "banco" => $this->banco,
            "moneda" => $this->moneda,
            "precio" => $this->precio,
            "tipo" => $this->tipo,
            "libro" => $this->libro,
            "voucher" => $this->voucher,
            "fecha" => $this->fecha,
            "comision" => $this->comision,
            "apellido" => $this->apellido,
            "telefono" => $this->telefono,
            "ideconomico" => $this->ideconomico,
            "nombre" => $this->nombre,
        );
    }
}