
<?php
class CuestionarioCliente
{
    private $idcuestionarioCliente;
    private $estado;
    private $cuenta;
    private $test;
    private $respuesta_P1;
    private $respuesta_P2;
    private $respuesta_P3;
    private $respuesta_P4;
    private $respuesta_P5;
    private $respuesta_P6;
    private $respuesta_P7;
    private $respuesta_P8;
    private $respuesta_P9;
    private $respuesta_P10;

    public function setIdCuestionarioCliente($idcuestionarioCliente)
    {
        $this->idcuestionarioCliente = $idcuestionarioCliente;
    }
    public function getIdCuestionarioCliente()
    {
        return $this->idcuestionarioCliente;
    }
    public function setRespuesta_P2($respuesta_P2)
    {
        $this->respuesta_P2 = $respuesta_P2;
    }
    public function getRespuesta_P2()
    {
        return $this->respuesta_P2;
    }
    public function setTest($test)
    {
        $this->test = $test;
    }
    public function getTest()
    {
        return $this->test;
    }
    public function setRespuesta_P1($respuesta_P1)
    {
        $this->respuesta_P1 = $respuesta_P1;
    }
    public function getRespuesta_P1()
    {
        return $this->respuesta_P1;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function getEstado()
    {
        return $this->estado;
    }

    public function setRespuesta_P3($respuesta_P3)
    {
        $this->respuesta_P3 = $respuesta_P3;
    }
    public function getRespuesta_P3()
    {
        return $this->respuesta_P3;
    }

    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function setRespuesta_P4($respuesta_P4)
    {
        $this->respuesta_P4 = $respuesta_P4;
    }
    public function getRespuesta_P4()
    {
        return $this->respuesta_P4;
    }
    public function setRespuesta_P5($respuesta_P5)
    {
        $this->respuesta_P5 = $respuesta_P5;
    }
    public function getRespuesta_P5()
    {
        return $this->respuesta_P5;
    }
    public function setRespuesta_P6($respuesta_P6)
    {
        $this->respuesta_P6 = $respuesta_P6;
    }
    public function getRespuesta_P6()
    {
        return $this->respuesta_P6;
    }
    public function setRespuesta_P7($respuesta_P7)
    {
        $this->respuesta_P7 = $respuesta_P7;
    }
    public function getRespuesta_P7()
    {
        return $this->respuesta_P7;
    }
    public function setRespuesta_P8($respuesta_P8)
    {
        $this->respuesta_P8 = $respuesta_P8;
    }
    public function getRespuesta_P8()
    {
        return $this->respuesta_P8;
    }
    public function setRespuesta_P9($respuesta_P9)
    {
        $this->respuesta_P9 = $respuesta_P9;
    }
    public function getRespuesta_P9()
    {
        return $this->respuesta_P9;
    }
    public function setRespuesta_P10($respuesta_P10)
    {
        $this->respuesta_P10 = $respuesta_P10;
    }
    public function getRespuesta_P10()
    {
        return $this->respuesta_P10;
    }

    public function __toString()
    {
        return
        array("estado" => $this->estado,
            "respuesta_P3" => $this->respuesta_P3,
            "cuenta" => $this->cuenta,
            "test" => $this->test,
            "respuesta_P1" => $this->respuesta_P1,
            "idcuestionarioCliente" => $this->idcuestionarioCliente,
            "respuesta_P2" => $this->respuesta_P2,
            "respuesta_P4" => $this->respuesta_P4,
            "respuesta_P5" => $this->respuesta_P5,
            "respuesta_P6" => $this->respuesta_P6,
            "respuesta_P7" => $this->respuesta_P7,
            "respuesta_P8" => $this->respuesta_P8,
            "respuesta_P9" => $this->respuesta_P9,
            "respuesta_P10" => $this->respuesta_P10,
        );
    }
}