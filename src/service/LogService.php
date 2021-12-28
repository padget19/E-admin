<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-02-21
 * Time: 13:19
 */

namespace Eadmin\service;


use think\facade\Request;
use Eadmin\Service;

class LogService extends Service
{
    protected $logPathDir;
    protected $filePath;
    protected $dataCount = 0;
    protected $limit = 10;
    public $page = 1;
    protected $pageOffset = false;

    public function __construct($filePath = null)
    {
        $this->logPathDir = app()->getRootPath() . 'runtime/log/';

        if (empty($filePath)) {
            $this->filePath = $this->getLastModifiedLog();
        } else {
            $this->filePath = $filePath;
        }


    }

    public function getLogFiles($count = 20)
    {
        $files = glob($this->logPathDir . '*');
        $files = array_combine($files, array_map('filemtime', $files));
        arsort($files);
        $filesArr = [];
        foreach ($files as $file => $time) {
            $filesArr[] = [
                'path'        => $file,
                'file_name'   => basename($file),
                'size'        => $this->getSize(filesize($file)),
                'update_time' => date('Y-m-d H:i:s', $time)
            ];
        }
        return array_slice($filesArr, 0, $count);
    }

    /**
     * 获取文件大小
     * @param int $fileSize
     * @return string
     */
    function getSize($fileSize)
    {

        if ($fileSize >= 1073741824) {

            $fileSize = round($fileSize / 1073741824 * 100) / 100 . ' GB';

        } elseif ($fileSize >= 1048576) {

            $fileSize = round($fileSize / 1048576 * 100) / 100 . ' MB';

        } elseif ($fileSize >= 1024) {

            $fileSize = round($fileSize / 1024 * 100) / 100 . ' KB';

        } else {

            $fileSize = $fileSize . ' 字节';

        }

        return $fileSize;

    }

    public function getLastModifiedLog()
    {
        $logs = $this->getLogFiles();
        $logs = current($logs);
        return $logs['path'] ?? '';
    }

    /**
     * Fetch logs by giving offset.
     *
     * @param int $seek
     * @param int $lines
     * @param int $buffer
     *
     * @return array
     *
     * @see http://www.geekality.net/2011/05/28/php-tail-tackling-large-files/
     */
    public function fetch($seek = 0, $lines = 20, $buffer = 4096, $filterContent = '', $filterTime = [])
    {
        if (empty($this->filePath) || !file_exists($this->filePath)) {
            return [];
        }
        $f = fopen($this->filePath, 'rb');
        if ($seek) {
            fseek($f, abs($seek));
        } else {
            fseek($f, 0, SEEK_END);
        }

        if (fread($f, 1) != PHP_EOL) {
            $lines -= 1;
        }
        fseek($f, -1, SEEK_CUR);
        // 从前往后读,上一页
        // Start reading
        if ($seek > 0) {
            $output = '';

            $this->pageOffset['start'] = ftell($f);

            while (!feof($f) && $lines >= 0) {
                $output = $output . ($chunk = fread($f, $buffer));
                $lines  -= substr_count($chunk, PHP_EOL);
            }

            $this->pageOffset['end'] = ftell($f);

            while ($lines++ < 0) {
                $strpos                  = strrpos($output, PHP_EOL) + 1;
                $_                       = mb_strlen($output, '8bit') - $strpos;
                $output                  = substr($output, 0, $strpos);
                $this->pageOffset['end'] -= $_;
            }

            // 从后往前读,下一页
        } else {
            $output = '';

            $this->pageOffset['end'] = ftell($f);

            while (ftell($f) > 0 && $lines >= 0) {

                $offset = min(ftell($f), $buffer);
                fseek($f, -$offset, SEEK_CUR);
                $output = ($chunk = fread($f, $offset)) . $output;

                fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
                $lines -= substr_count($chunk, PHP_EOL);

            }

            $this->pageOffset['start'] = ftell($f);

            while ($lines++ < 0) {
                $strpos                    = strpos($output, PHP_EOL) + 1;
                $output                    = substr($output, $strpos);
                $this->pageOffset['start'] += $strpos;
            }
        }


        return $this->parseLog($output, $filterContent, $filterTime);
    }

    /**
     * Get previous page url.
     *
     * @return bool|string
     */
    public function getPrevPageUrl($filterContent = '', $filterTime = '')
    {

        if (!empty($filterContent) || !empty(array_filter($filterTime))) {
            $page = request()->param('page', 1);
            if ($page > 1) {
                $this->page = $page - 1;
                return 0;
            } else {
                return false;
            }
        } else {
            $filesize = $this->getFilesize();
            if ($this->pageOffset['end'] >= $filesize - 1) {
                return false;
            }
            return $this->pageOffset['end'];
        }

    }

    /**
     * Get Next page url.
     *
     * @return bool|string
     */
    public function getNextPageUrl($filterContent = '', $filterTime = '')
    {
        if (!empty($filterContent) || !empty(array_filter($filterTime))) {
            $page = request()->param('page', 1);
            if ($this->dataCount < $this->limit) {
                return false;
            } else {
                $this->page = $page + 1;
                return 0;
            }
        } else {
            if(!$this->pageOffset){
                return false;
            }
            if ($this->pageOffset['start'] == 0) {
                return false;
            }

            return -$this->pageOffset['start'];
        }

    }

    public function getFilesize()
    {
        return filesize($this->filePath);
    }

    /**
     * Parse raw log text to array.
     *
     * @param $raw
     *
     * @return array
     */
    protected function parseLog($raw, $filterContent = '', $filterTime = '')
    {
        $logs = explode(PHP_EOL, $raw);
        if (empty($logs)) {
            return [];
        }
        $parsed = [];
        foreach ($logs as $key => $log) {
            if (!empty(trim($log))) {
                $log         = json_decode($log, true);
                $strtotime   = strtotime($log['time']);
                $log['time'] = date('Y年m月d日 H:i:s', $strtotime);
                $info        = $log['msg'];
                if (!empty($filterContent) && !empty($filterTime)) {
                    $dates = $filterTime;
                    $date  = date('Y-m-d H:i:s', $strtotime);
                    if (stristr($info, $filterContent) !== false && ($dates[0] <= $date && $dates[1] >= $date)) {
                        $parsed[] = $log;
                    }
                } elseif (!empty($filterContent)) {
                    if (stristr($info, $filterContent) !== false) {
                        $parsed[] = $log;
                    }
                } elseif (!empty(array_filter($filterTime))) {
                    $dates = $filterTime;
                    $date  = date('Y-m-d H:i:s', $strtotime);
                    if ($dates[0] <= $date && $dates[1] >= $date) {
                        $parsed[] = $log;
                    }
                } else {
                    $parsed[] = $log;
                }
            }
        }
        unset($logs);
        $this->limit = Request::param('limit', 10);
        rsort($parsed);
        if (!empty($filterContent) || !empty($filterTime)) {
            $page   = request()->param('page', 1);
            $page   = $this->limit * ($page - 1);
            $parsed = array_splice($parsed, $page, $this->limit);
        }
        $this->dataCount = count($parsed);
        return $parsed;
    }
}
