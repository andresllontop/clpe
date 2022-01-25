
<?php
class AjusteCita
{
    private $idajusteCita;
    private $tipo;
    private $subtitulo;
    public function setIdajusteCita($idajusteCita)
    {
        $this->idajusteCita = $idajusteCita;
    }
    public function getIdajusteCita()
    {
        return $this->idajusteCita;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }

    public function setSubtitulo($subtitulo)
    {
        $this->subtitulo = $subtitulo;
    }
    public function getSubtitulo()
    {
        return $this->subtitulo;
    }

    public function __toString()
    {
        return
        array(
            "subtitulo" => $this->subtitulo,
            "idajusteCita" => $this->idajusteCita,
            "tipo" => $this->tipo,
        );
    }
}