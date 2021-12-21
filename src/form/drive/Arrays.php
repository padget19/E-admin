<?php


namespace Eadmin\form\drive;


use Eadmin\contract\FormInterface;

class Arrays implements FormInterface
{
    //主键字段
    protected $pkField ='id';
    //数据源
    protected $data = [];
    protected $originData = [];

    public function __construct($data)
    {
        $this->originData = $data;
    }

    public function getData(string $field = null, $data = null)
    {
        if (is_null($data)) {
            $data = $this->data;
        }
        if (is_null($field)) {
            return $data;
        } else {
            foreach (explode('.', $field) as $f) {
                if (isset($data[$f])) {
                    $data = $data[$f];
                } else {
                    $data = null;
                }
            }
            return $data;
        }
    }
    public function getDataAll(){
        return [];
    }
    public function edit($id)
    {
        $this->data = $this->originData;
    }

    public function getPk()
    {
        return $this->pkField;
    }

    public function save(array $data)
    {
        return true;
    }
    public function saveAll(array $data){
        return true;
    }
    public function setPkField(string $field)
    {
        $this->pkField = $field;
    }
    public function model(){
        return null;
    }
}
