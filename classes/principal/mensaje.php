
<?php
class Mensaje
{
    private $idmensaje;
    private $titulo;
    private $descripcion;
    private $imagen;
    private $cuenta;
    private $estado;
    private $email;

    public function setIdMensaje($idmensaje)
    {
        $this->idmensaje = $idmensaje;
    }
    public function getIdMensaje()
    {
        return $this->idmensaje;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
    public function getImagen()
    {
        return $this->imagen;
    }

    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function __toString()
    {
        return
        array("titulo" => $this->titulo,
            "imagen" => $this->imagen,
            "cuenta" => $this->cuenta,
            "estado" => $this->estado,
            "idmensaje" => $this->idmensaje,
            "descripcion" => $this->descripcion,
        );
    }
}