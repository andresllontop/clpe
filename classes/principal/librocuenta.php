
<?php
class LibroCuenta
{
    private $idlibroCuenta;
    private $cuenta;
    private $cliente;
    private $libro;
    private $finalizacion;
    private $estado_certificado;
    private $certificado;
    private $imagen;
    private $monto;
    private $fecha;
    private $estado;

    public function setIdlibroCuenta($idlibroCuenta)
    {
        $this->idlibroCuenta = $idlibroCuenta;
    }
    public function getIdlibroCuenta()
    {
        return $this->idlibroCuenta;
    }
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function setLibro($libro)
    {
        $this->libro = $libro;
    }
    public function getLibro()
    {
        return $this->libro;
    }
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }
    public function getCliente()
    {
        return $this->cliente;
    }
    public function setFinalizacion($finalizacion)
    {
        $this->finalizacion = $finalizacion;
    }
    public function getFinalizacion()
    {
        return $this->finalizacion;
    }
    public function setEstadoCertificado($estado_certificado)
    {
        $this->estado_certificado = $estado_certificado;
    }
    public function getEstadoCertificado()
    {
        return $this->estado_certificado;
    }
    public function setCertificado($certificado)
    {
        $this->certificado = $certificado;
    }
    public function getCertificado()
    {
        return $this->certificado;
    }
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
    public function getImagen()
    {
        return $this->imagen;
    }
    public function setMonto($monto)
    {
        $this->monto = $monto;
    }
    public function getMonto()
    {
        return $this->monto;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function __toString()
    {
        return
        array(
            "idlibroCuenta" => $this->idlibroCuenta,
            "cuenta" => $this->cuenta,
            "libro" => $this->libro,
            "cliente" => $this->cliente,
            "imagen" => $this->imagen,
            "estado" => $this->estado,
            "fecha" => $this->fecha,
            "monto" => $this->monto,
            "certificado" => $this->certificado,
            "estado_certificado" => $this->estado_certificado,
            "finalizacion" => $this->finalizacion,
        );
    }
}