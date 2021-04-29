
<?php
class Empresa
{
    private $idempresa;
    private $nombre;
    private $terminoCondicion;
    private $telefono;
    private $telefonoSegundo;
    private $mision;
    private $email;
    private $vision;
    private $youtube;
    private $descripcion;
    private $direccion;
    private $logo;
    private $instagram;
    private $enlace;
    private $facebook;
    private $precio;
    private $frase;

    public function setIdEmpresa($idempresa)
    {
        $this->idempresa = $idempresa;
    }
    public function getIdEmpresa()
    {
        return $this->idempresa;
    }
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
    public function getTelefono()
    {
        return $this->telefono;
    }
    public function setVision($vision)
    {
        $this->vision = $vision;
    }
    public function getVision()
    {
        return $this->vision;
    }
    public function setYoutube($youtube)
    {
        $this->youtube = $youtube;
    }
    public function getYoutube()
    {
        return $this->youtube;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getNombre()
    {
        return $this->nombre;
    }

    public function setMision($mision)
    {
        $this->mision = $mision;
    }
    public function getMision()
    {
        return $this->mision;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }
    public function getDireccion()
    {
        return $this->direccion;
    }
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }
    public function getLogo()
    {
        return $this->logo;
    }
    public function setEnlace($enlace)
    {
        $this->enlace = $enlace;
    }
    public function getEnlace()
    {
        return $this->enlace;
    }
    public function setTelefonoSegundo($telefonoSegundo)
    {
        $this->telefonoSegundo = $telefonoSegundo;
    }
    public function getTelefonoSegundo()
    {
        return $this->telefonoSegundo;
    }
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }
    public function getFacebook()
    {
        return $this->facebook;
    }
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    public function getPrecio()
    {
        return $this->precio;
    }

    public function setFrase($frase)
    {
        $this->frase = $frase;
    }
    public function getFrase()
    {
        return $this->frase;
    }
    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;
    }
    public function getInstagram()
    {
        return $this->instagram;
    }
    public function setTerminoCondicion($terminoCondicion)
    {
        $this->terminoCondicion = $terminoCondicion;
    }
    public function getTerminoCondicion()
    {
        return $this->terminoCondicion;
    }
    public function __toString()
    {
        return
        array("nombre" => $this->nombre,
            "mision" => $this->mision,
            "frase" => $this->frase,
            "terminoCondicion" => $this->terminoCondicion,
            "instagram" => $this->instagram,
            "email" => $this->email,
            "vision" => $this->vision,
            "youtube" => $this->youtube,
            "idempresa" => $this->idempresa,
            "telefono" => $this->telefono,
            "descripcion" => $this->descripcion,
            "direccion" => $this->direccion,
            "logo" => $this->logo,
            "enlace" => $this->enlace,
            "precio" => $this->precio,
            "facebook" => $this->facebook,
            "telefonoSegundo" => $this->telefonoSegundo,

        );
    }
}