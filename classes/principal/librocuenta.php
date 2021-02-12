
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

    public function __toString()
    {
        return
        array(
            "idlibroCuenta" => $this->idlibroCuenta,
            "cuenta" => $this->cuenta,
            "libro" => $this->libro,
            "cliente" => $this->cliente,
            "certificado" => $this->certificado,
            "estado_certificado" => $this->estado_certificado,
            "finalizacion" => $this->finalizacion,
        );
    }
}