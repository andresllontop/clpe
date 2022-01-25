
<?php
class Cita
{
    private $idcita;
    private $tipo;
    private $cliente;
    private $asunto;
    private $subtitulo;
    private $estadoSolicitud;
    /*
    1:SOLICITUD
    2:ACEPTADO
    3:PROGRAMADA
     */
    private $fechaAceptacion;
    private $fechaSolicitud;
    private $fechaProgramada;
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
    public function setEstadoAceptacion($estadoAceptacion)
    {
        $this->estadoAceptacion = $estadoAceptacion;
    }
    public function getEstadoAceptacion()
    {
        return $this->estadoAceptacion;
    }
    public function setFechaAceptacion($fechaAceptacion)
    {
        $this->fechaAceptacion = $fechaAceptacion;
    }
    public function getFechaAceptacion()
    {
        return $this->fechaAceptacion;
    }
    public function setFechaSolicitud($fechaSolicitud)
    {
        $this->fechaSolicitud = $fechaSolicitud;
    }
    public function getFechaSolicitud()
    {
        return $this->fechaSolicitud;
    }
    public function setFechaProgramada($fechaProgramada)
    {
        $this->fechaProgramada = $fechaProgramada;
    }
    public function getFechaProgramada()
    {
        return $this->fechaProgramada;
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
    public function __toString()
    {
        return
        array(
            "subtitulo" => $this->subtitulo,
            "asunto" => $this->asunto,
            "estadoSolicitud" => $this->estadoSolicitud,
            "estadoAceptacion" => $this->estadoAceptacion,
            "fechaSolicitud" => $this->fechaSolicitud,
            "fechaAceptacion" => $this->fechaAceptacion,
            "fechaProgramada" => $this->fechaProgramada,
            "fechaAtendida" => $this->fechaAtendida,
            "idcita" => $this->idcita,
            "cliente" => $this->cliente,
            "tipo" => $this->tipo,
        );
    }
}