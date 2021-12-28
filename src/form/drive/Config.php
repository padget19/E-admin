<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-02-13
 * Time: 19:47
 */

namespace Eadmin\form\drive;


use Eadmin\Admin;
use Eadmin\contract\FormInterface;

class Config implements FormInterface
{
    public function __construct($data = null)
    {

    }

    public function getPk()
    {
        return 'id';
    }

    public function setPkField(string $field)
    {

    }

    public function getData(string $field = null, $data = null)
    {
        return Admin::sysconf($field);
    }

    public function save(array $data)
    {
        foreach ($data as $field => $value) {
            Admin::sysconf($field, $value);
        }
        return true;
    }
    public function saveAll(array $data){
        return true;
    }
    public function edit($id)
    {

    }
    public function model(){
        return null;
    }
}
