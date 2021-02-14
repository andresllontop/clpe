
<?php
class BeanCrud
{
    private $messageServer;
    private $beanPagination;
    private $beanClass;

    public function setMessageServer($messageServer)
    {
        $this->messageServer = $messageServer;
    }
    public function getMessageServer()
    {
        return $this->messageServer;
    }

    public function setBeanPagination($beanPagination)
    {
        $this->beanPagination = $beanPagination;
    }
    public function getBeanPagination()
    {
        return $this->beanPagination;
    }
    public function setBeanClass($beanClass)
    {
        $this->beanClass = $beanClass;
    }
    public function getBeanClass()
    {
        return $this->beanClass;
    }
    public function __toString()
    {
        // return "BeanCrud{" . "messageServer:" . $this->messageServer . ", beanPagination:" . $this->beanPagination . '}';
        return array("messageServer" => $this->messageServer,
            "beanPagination" => $this->beanPagination,
            "beanClass" => $this->beanClass);
    }
}