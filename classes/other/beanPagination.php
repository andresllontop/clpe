
<?php
class BeanPagination
{
    private $countFilter;
    private $list = array();

    public function setCountFilter($countFilter)
    {
        $this->countFilter = $countFilter;
    }
    public function getCountFilter()
    {
        return $this->countFilter;
    }

    public function setList($lista)
    {
        array_push($this->list, $lista);
    }
    public function getList()
    {
        return $this->list;
    }

    public function __toString()
    {
        return array("countFilter" => ($this->countFilter == null ? 0 : $this->countFilter),
            "list" => $this->list);

    }
}