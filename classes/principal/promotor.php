
<?php
class Promotor
{
    private $idpromotor;
    private $nombre;
    private $telefono;
    private $apellido;
    private $email;
    private $ocupacion;
    private $youtube;
    private $descripcion;
    private $historia;
    private $foto;
    private $fotoPortada;

    public function setIdPromotor($idpromotor)
    {
        $this->idpromotor = $idpromotor;
    }
    public function getIdPromotor()
    {
        return $this->idpromotor;
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

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }
    public function getApellido()
    {
        return $this->apellido;
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
    public function setHistoria($historia)
    {
        $this->historia = $historia;
    }
    public function getHistoria()
    {
        return $this->historia;
    }
    public function setFoto($foto)
    {
        $this->foto = $foto;
    }
    public function getFoto()
    {
        return $this->foto;
    }
    public function setFotoPortada($fotoPortada)
    {
        $this->fotoPortada = $fotoPortada;
    }
    public function getFotoPortada()
    {
        return $this->fotoPortada;
    }
    public function __toString()
    {
        return
        array("nombre" => $this->nombre,
            "apellido" => $this->apellido,
            "email" => $this->email,
            "ocupacion" => $this->ocupacion,
            "youtube" => $this->youtube,
            "idpromotor" => $this->idpromotor,
            "telefono" => $this->telefono,
            "descripcion" => $this->descripcion,
            "historia" => $this->historia,
            "foto" => $this->foto,
            "fotoPortada" => $this->fotoPortada,

        );
    }
}