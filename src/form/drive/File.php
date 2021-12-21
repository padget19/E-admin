<?php

namespace Eadmin\form\drive;

use Eadmin\contract\FormInterface;

class File implements FormInterface
{
    //主键字段
    protected $pkField = 'id';
    protected $file;
    protected $data = [];

    public function __construct($data)
    {
        $this->file = $data;
        $this->data = include $this->file;
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

    public function edit($id)
    {
        
    }

    public function getPk()
    {
        return $this->pkField;
    }

    public function save(array $data)
    {
        unset($data['eadmin_app'],$data['eadmin_class'],$data['eadmin_function'],$data['eadmin_step_num']);
        $content = var_export($data, true);
        $content = <<<PHP
<?php
return $content;
PHP;

        return file_put_contents($this->file,$content);
    }

    public function saveAll(array $data)
    {
        return true;
    }

    public function setPkField(string $field)
    {
        $this->pkField = $field;
    }

    public function model()
    {
        return null;
    }

}
