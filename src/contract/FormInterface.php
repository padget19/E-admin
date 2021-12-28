<?php


namespace Eadmin\contract;


interface FormInterface
{
    public function __construct($data);

    public function getPk();

    public function setPkField(string $field);

    public function getData(string $field = null, $data = null);
    
    
    public function save(array $data);

    public function saveAll(array $data);

    public function edit($id);
    public function model();
}
