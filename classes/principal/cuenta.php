<?php
class Cuenta
{
    private $idcuenta;
    private $cuentaCodigo;
    private $usuario;
    private $clave;
    private $email;
    private $estado = 0;
    private $tipo;
    private $foto;
    private $voucher;
    private $verificacion;
    private $perfil;
    private $precio = 0;

    public function setIdCuenta($idcuenta)
    {
        $this->idcuenta = $idcuenta;
    }
    public function getIdCuenta()
    {
        return $this->idcuenta;
    }
    public function setVoucher($voucher)
    {
        $this->voucher = $voucher;
    }
    public function getVoucher()
    {
        return $this->voucher;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setFoto($foto)
    {
        $this->foto = $foto;
    }
    public function getFoto()
    {
        return $this->foto;
    }
    public function setCuentaCodigo($cuentaCodigo)
    {
        $this->cuentaCodigo = $cuentaCodigo;
    }
    public function getCuentaCodigo()
    {
        return $this->cuentaCodigo;
    }
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }
    public function getUsuario()
    {
        return $this->usuario;
    }
    public function setClave($clave)
    {
        $this->clave = $clave;
    }
    public function getClave()
    {
        return $this->clave;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    public function getPrecio()
    {
        return $this->precio;
    }
    public function setVerificacion($verificacion)
    {
        $this->verificacion = $verificacion;
    }
    public function getVerificacion()
    {
        return $this->verificacion;
    }
    public function setPerfil($perfil)
    {
        $this->perfil = $perfil;
    }
    public function getPerfil()
    {
        return $this->perfil;
    }
    public function __toString()
    {
        return
        array("cuentaCodigo" => $this->cuentaCodigo,
            "usuario" => $this->usuario,
            "clave" => $this->clave,
            "email" => $this->email,
            "perfil" => $this->perfil,
            "verificacion" => $this->verificacion,
            "precio" => $this->precio,
            "estado" => $this->estado,
            "voucher" => $this->voucher,
            "tipo" => $this->tipo,
            "foto" => $this->foto,
            "idcuenta" => $this->idcuenta,

        );
    }
}
