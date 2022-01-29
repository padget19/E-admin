<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-11-08
 * Time: 09:55
 */

namespace Eadmin\grid\excel;

use Eadmin\Admin;
use Eadmin\service\NoticeService;
use Eadmin\service\QueueService;
use think\facade\Filesystem;

/**
 * csv导出
 * Class Csv
 * @package Eadmin\grid\excel
 */
class Csv extends AbstractExporter
{
    protected $rowIndex = 0;
    protected function writeRowData($fp){
        $this->filterColumns();
        //设置标题
        $title  = array_values($this->columns);
        $fields = array_keys($this->columns);
        foreach ($title as $key => $item) {
            $title[$key] = mb_convert_encoding($item, 'GBK', 'UTF-8');
        }
        //将标题写到标准输出中
        if ($this->rowIndex == 0) {
            fputcsv($fp, $title);
        }
        foreach ($this->data as $item) {
            $row = [];
            foreach ($fields as $field) {
                $value = is_null($item[$field]) ? '' : $item[$field];
                $row[] = mb_convert_encoding($value, 'GBK', 'UTF-8');
            }
            fputcsv($fp, $row);
            $this->rowIndex++;
        }
        fclose($fp);
    }
    public function queueExport($count){
        $path = Filesystem::path('excel');
        $filesystem = new \Symfony\Component\Filesystem\Filesystem;
        $filesystem->mkdir($path);
        $fileName = $path.DIRECTORY_SEPARATOR.$this->fileName . '.csv';
        $fp = fopen($fileName, 'a');
        $this->writeRowData($fp);
        $queue = new QueueService(request()->get('system_queue_id'));
        $queue->percentage($count,$this->rowIndex-1,'正在导出');
        if($this->rowIndex >= $count){
            $filename = '/upload/excel/' . $this->fileName . '.csv';
            
            NoticeService::instance()->pushIcon(Admin::id(),admin_trans('admin.export_download'), '【'.admin_trans('admin.下载文件').'】'.$this->fileName, 'el-icon-message','',request()->get('eadmin_domain').'/'.$filename);
            $queue->progress($filename);
        }
    }
    public function export()
    {
        $fp = fopen('php://output', 'a');
        set_time_limit(0);
        ini_set('memory_limit', '128M');
        header('Content-Type: application/vnd.ms-execl');
        header('Content-Disposition: attachment;filename="' . $this->fileName . '.csv"');
        $this->writeRowData($fp);
    }
}
