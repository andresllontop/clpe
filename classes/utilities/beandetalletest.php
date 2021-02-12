
<?php
class BeanDetalleTest
{
    private $test;
    private $listdetalle;

    public function setTest($test)
    {
        $this->test = $test;
    }
    public function getTest()
    {
        return $this->test;
    }

    public function setListDetalle($listdetalle)
    {
        $this->listdetalle = $listdetalle;
    }
    public function getListDetalle()
    {
        return $this->listdetalle;
    }

    public function __toString()
    {
        return
        array("test" => $this->test,
            "listdetalle" => $this->listdetalle,
        );
    }
}