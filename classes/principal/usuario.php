
<?php
class Usuario
{
    private $id;
    private $usuario;
    private $tipo;
    private $email;
    private $codigo;
    private $idbitacora;
    private $foto;
    private $empresa;
    private $perfil;
    private $libroCode;

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }
    public function getUsuario()
    {
        return $this->usuario;
    }
    public function setLibroCode($libroCode)
    {
        $this->libroCode = $libroCode;
    }
    public function getLibroCode()
    {
        return $this->libroCode;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }
    public function getCodigo()
    {
        return $this->codigo;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setFoto($foto)
    {
        $this->foto = $foto;
    }
    public function getFoto()
    {
        return $this->foto;
    }
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }
    public function getEmpresa()
    {
        return $this->empresa;
    }
    public function setIdBitacora($idbitacora)
    {
        $this->idbitacora = $idbitacora;
    }
    public function getIdBitacora()
    {
        return $this->idbitacora;
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
        array(
            "id" => $this->id,
            "usuario" => $this->usuario,
            "tipo_usuario" => $this->tipo,
            "codigo" => $this->codigo,
            "email" => $this->email,
            "empresa" => $this->empresa,
            "foto" => $this->foto,
            "perfil" => $this->perfil,
            "idbitacora" => $this->idbitacora,
            "libroCode" => $this->libroCode,
        );
    }
}