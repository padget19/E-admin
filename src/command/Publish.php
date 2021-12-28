<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-07
 * Time: 20:50
 */

namespace Eadmin\command;

use Symfony\Component\Finder\Finder;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\console\Command;

class Publish extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('eadmin:publish')->setDescription('Publish any publishable assets from vendor packages');
        $this->addOption('force', 'f', Option::VALUE_NONE, 'Force overwrite file');
        $this->addOption('public', 'p', Option::VALUE_NONE, '前端静态文件 overwrite file');
    }

    protected function execute(Input $input, Output $output)
    {
        $force = $input->getOption('force');
        $public = $input->hasOption('public');
        if(!$public){
            $assetsDir = __DIR__ . '/../assets/admin';
            $this->copyDir($assetsDir, app()->getAppPath() . 'admin',$force);
            $assetsDir = __DIR__ . '/../database';
            $this->copyDir($assetsDir, app()->getRootPath() . 'database',$force);
        }
        $assetsDir = __DIR__ . '/../assets/public';
        $this->copyDir($assetsDir, app()->getRootPath() . 'public/eadmin', $force);
        $assetsDir = __DIR__ . '/../assets/admin/view/index.vue';
        $file = __DIR__.'/../../.env';
        copy($file,app()->getRootPath().'public/eadmin/.env');
        copy($assetsDir, app()->getAppPath() . 'admin/view/index.vue');
    }
    
    /**
     * 递归复制目录文件
     * @param string $dir 源目录
     * @param string $src 目标目录
     * @param bool $cover 是否覆盖文件
     */
    protected function copyDir($dir, $src,$cover =false)
    {
        if (is_dir($src) && !$cover) {
            if ($this->output->confirm($this->input, "确认覆盖资源文件目录[$src]? [y]/n")) {
                $cover = true;
            }
        } else {
            if (!is_dir($src)) {
                mkdir($src, 0755);
            }
            $cover = true;
        }
        if ($cover) {
            $finder = new Finder();
            foreach ($finder->in($dir) as $file) {
                $path = $file->getRealPath();
                $makePath = $src . DIRECTORY_SEPARATOR . $file->getRelativePath() . DIRECTORY_SEPARATOR . $file->getFilename();
                if (is_dir($path)) {
                    if (!is_dir($makePath)) {
                        mkdir($makePath, 0755);
                    }
                } else {
                    copy($path, $makePath);
                }
            }
        }
        if ($cover) {
            $this->output->writeln("<info>[{$dir}] to [{$src}] 资源文件写入成功 successfully!</info>");
        }
    }

}
