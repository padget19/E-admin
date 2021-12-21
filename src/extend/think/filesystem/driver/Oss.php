<?php

namespace think\filesystem\driver;


use Iidestiny\Flysystem\Oss\OssAdapter;
use League\Flysystem\AdapterInterface;
use think\filesystem\Driver;

class Oss extends Driver
{
    protected function createAdapter(): AdapterInterface
    {

        return new OssAdapter(
            $this->config['accessKey'],
            $this->config['secretKey'],
            $this->config['endpoint'],
            $this->config['bucket']
        );
    }
}