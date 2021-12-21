<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-02-14
 * Time: 00:24
 */

namespace Eadmin\component\basic;


trait Common
{

    public function sizeMedium()
    {
        $this->attr('size', 'medium');
        return $this;
    }

    public function sizeMini()
    {
        $this->attr('size', 'mini');
        return $this;
    }

    public function sizeSmall()
    {
        $this->attr('size', 'small');
        return $this;
    }

    public function typePrimary()
    {
        $this->attr('type', 'primary');
        return $this;
    }

    public function typeInfo()
    {
        $this->attr('type', 'info');
        return $this;
    }

    public function typeSuccess()
    {
        $this->attr('type', 'success');
        return $this;
    }

    public function typeWarning()
    {
        $this->attr('type', 'warning');
        return $this;
    }

    public function typeDanger()
    {
        $this->attr('type', 'danger');
        return $this;
    }
}