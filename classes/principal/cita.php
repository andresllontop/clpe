
<?php
class Cita
{
    private $idcita;
    private $tipo;
    private $color;
    private $cliente;
    private $clienteExterno;
    private $asunto;
    private $subtitulo;
    private $estadoSolicitud;
    /*
    1:SOLICITUD
    2:ACEPTADO
    3:PROGRAMADA
     */

    private $fechaSolicitud;
    private $fechaAtendida;
    public function setIdcita($idcita)
    {
        $this->idcita = $idcita;
    }
    public function getIdcita()
    {
        return $this->idcita;
    }
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }
    public function getCliente()
    {
        return $this->cliente;
    }
    public function setClienteExterno($clienteExterno)
    {
        $this->clienteExterno = $clienteExterno;
    }
    public function getClienteExterno()
    {
        return $this->clienteExterno;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }
    public function setEstadoSolicitud($estadoSolicitud)
    {
        $this->estadoSolicitud = $estadoSolicitud;
    }
    public function getEstadoSolicitud()
    {
        return $this->estadoSolicitud;
    }
    public function setSubtitulo($subtitulo)
    {
        $this->subtitulo = $subtitulo;
    }
    public function getSubtitulo()
    {
        return $this->subtitulo;
    }

    public function setFechaSolicitud($fechaSolicitud)
    {
        $this->fechaSolicitud = $fechaSolicitud;
    }
    public function getFechaSolicitud()
    {
        return $this->fechaSolicitud;
    }
    public function setFechaAtendida($fechaAtendida)
    {
        $this->fechaAtendida = $fechaAtendida;
    }
    public function getFechaAtendida()
    {
        return $this->fechaAtendida;
    }
    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;
    }
    public function getAsunto()
    {
        return $this->asunto;
    }
    public function setColor($color)
    {
        $this->color = $color;
    }
    public function getColor()
    {
        return $this->color;
    }
    public function __toString()
    {
        return
        array(
            "subtitulo" => $this->subtitulo,
            "color" => $this->color,
            "asunto" => $this->asunto,
            "estadoSolicitud" => $this->estadoSolicitud,
            "fechaSolicitud" => $this->fechaSolicitud,
            "fechaAtendida" => $this->fechaAtendida,
            "idcita" => $this->idcita,
            "cliente" => $this->cliente,
            "clienteExterno" => $this->clienteExterno,
            "tipo" => $this->tipo,
        );
    }
}