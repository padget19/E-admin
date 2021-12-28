<?php
/**
 * @Author: rocky
 * @Copyright: 广州拓冠科技 <http://my8m.com>
 * Date: 2019/8/19
 * Time: 9:47
 */


namespace Eadmin;


use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use think\facade\Cache;
use think\facade\Event;

/**
 * 定时任务调度
 * Class Crontab
 */
class Schedule
{
    protected $datetime;
    protected $closure;
    protected $minuteRule = 0;
    protected $secondRule = 0;
    protected $hourRule = 0;
    protected $hourAtMinute = 0;
    protected $dayRule = 0;
    protected $dayAtTime = '';
    protected $weekly = false;
    protected $monthly = false;
    protected $monthAtDay = 1;
    protected $quarterly = false;
    protected $yearly = false;
    protected $rule = [];
    protected $nowTime;
    protected $taskList = [];
    protected $timeDesc = '';
    protected $key = '';
    protected $name = '';
    public function __construct()
    {
        $this->datetime = new \DateTime;
        $this->nowTime = date('Y-m-d H:i');
    }

    public function setClosure(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * 任务调度
     * @param $name 定时任务名称
     * @param \Closure $closure 执行逻辑
     * @return Schedule
     */
    public function call($name, $closure)
    {
        $backtrace = debug_backtrace(1, 3);
        $backtrace = array_pop($backtrace);
        $new = new self();
        $new->setClosure($closure);
        $new->key = md5($backtrace['file'] . $name);
        $cacheKey = 'crontab_' . $new->key . '_log';
        $log = Cache::get($cacheKey) ?: [];
        $new->name = $name;
        $this->taskList[$new->key] = ['id' => $new->key, 'name' => $name, 'schedule' => $new, 'file' => $backtrace['file'], 'log' => $log];
        Event::listen(self::class, function () use ($new, $cacheKey) {
            $new->run();
        });
        return $new;
    }

    public function list()
    {
        return $this->taskList;
    }

    public function getDesc()
    {
        return $this->timeDesc;
    }

    public function run($force = false)
    {
        if ($this->minuteRule > 0) {
            $this->minuteRule();
        } elseif ($this->hourRule > 0) {
            $this->hourRule();
        } elseif ($this->dayRule > 0) {
            $this->dayRule();
        } elseif ($this->weekly) {
            $this->weekRule();
        } elseif ($this->monthly) {
            $this->monthRule();
        } elseif ($this->quarterly) {
            $this->quarterlyRule();
        } elseif ($this->yearly) {
            $this->yearRule();
        }
        if($this->secondRule > 0){
            $secondTime = Cache::get($this->key);
            if($secondTime == date('Y-m-d H:i:s') && $this->secondRule == 1){
                return false;
            }elseif($this->secondRule >1 && $secondTime){
                $this->secondRule--;
                return false;
            }elseif ($this->secondRule >1){
                $this->secondRule--;
            }
            Cache::set($this->key,date('Y-m-d H:i:s'),$this->secondRule);
        }
        if (in_array($this->nowTime, $this->rule) || $force || $this->secondRule > 0) {

            $message = '';
            $cacheKey = 'crontab_' . $this->key . '_log';
            $log = Cache::get($cacheKey) ?: [];
            $time = microtime(true);
            $datetime = date('Y-m-d H:i:s');
            try {
                if ($this->closure instanceof \Closure) {
                    call_user_func($this->closure);
                } elseif (class_exists($this->closure)) {
                    app()->make($this->closure);
                }
            }catch (\Throwable $exception){
                $message.='<div><b style="color: red">任务失败错误信息</b>：' . $exception->getMessage().'</div>';
                $message.='<div><b style="color: red">任务失败追踪错误</b>：<pre>' . $exception->getTraceAsString().'</pre></div>';
            }
            $time = microtime(true) - $time;
            if(!empty($message)){
                if(app()->runningInConsole()) {
                    dump("[{$this->name}] 执行失败");
                }
                array_unshift($log, ['message' => $message .'耗时：'.$time, 'time' => $datetime]);
            }else{
                if(app()->runningInConsole()){
                    dump("[{$this->name}] 执行完成,耗时:".$time);
                }
                array_unshift($log, ['message' => $message .PHP_EOL. '执行完成,耗时：'.$time, 'time' => $datetime]);
            }
            Cache::set($cacheKey, $log, 86400);
        }
    }

    /**
     * 定时每几分钟执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $minte 每几分钟
     */
    public function everyMinute($minte = 1)
    {
        $this->timeDesc = "每{$minte}分钟";
        $this->minuteRule = $minte;
    }

    /**
     * 定时每几时执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $minte 每几小时
     */
    public function hourly($hour = 1)
    {
        $this->timeDesc = "每{$hour}小时";
        $this->hourRule = $hour;
    }

    /**
     * 定时每小时的第几分钟执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $minte 第几分钟 1-59之间
     */
    public function hourlyAt($minute)
    {
        if ($minute > 0 && $minute <= 59) {
            $this->hourly(1);
            $this->hourAtMinute = $minute;
        }
        $this->timeDesc = "每小时{$minute}分钟";
    }
    /**
     * 定时每秒执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $minte 每几秒
     */
    public function everySecond($second=1){
        $this->timeDesc = "每{$second}秒";
        $this->secondRule = $second;
    }
    /**
     * 定时每几天执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $minte 每几天
     */
    public function everyDay($day = 1)
    {
        $this->dayRule = $day;
        $this->timeDesc = "每{$day}天";
    }

    /**
     * 定时每天的几点执行
     * @Author: rocky
     * 2019/8/19 10:01
     * @param int $time 时间  格式-小时:分钟 13:00
     */
    public function everyDayAt($time)
    {
        $this->everyDay(1);
        $this->dayAtTime = $time;
        $this->timeDesc = "每天" . $time;
    }

    /**
     * 定时每周执行
     * @Author: rocky
     * 2019/8/19 10:01
     */
    public function weekly()
    {
        $this->timeDesc = '每周';
        $this->weekly = true;
    }

    /**
     * 定时每月执行
     * @Author: rocky
     * 2019/8/19 10:01
     */
    public function monthly()
    {
        $this->timeDesc = '每月';
        $this->monthly = true;
    }

    /**
     * 定时每月第几天的某个时间执行
     * @Author: rocky
     * 2019/8/19 11:50
     * @param $day 第几天
     * @param $time 时间 格式-小时:分钟 13:00
     */
    public function monthlyOn($day, $time)
    {
        $this->monthly = true;
        $this->monthAtDay = $day;
        $this->dayAtTime = $time;
        $this->timeDesc = "每月第{$day}天{$time}";
    }

    /**
     * 定时每季度执行
     * @Author: rocky
     * 2019/8/19 10:01
     */
    public function quarterly()
    {
        $this->quarterly = true;
        $this->timeDesc = "每季度";
    }

    /**
     * 定时每年执行
     * @Author: rocky
     * 2019/8/19 10:01
     */
    public function yearly()
    {
        $this->yearly = true;
        $this->timeDesc = "每年";
    }

    /**
     * 分钟规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function minuteRule()
    {
        $minute = 0;
        $nowHour = date('H');
        while ($minute <= 59) {
            $this->datetime->setTime($nowHour, $minute);
            $minute += $this->minuteRule;
            if ($minute <= 59) {
                array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
            }
        }
    }

    /**
     * 小时规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function HourRule()
    {
        $hour = 0;
        while ($hour <= 23) {
            $this->datetime->setTime($hour, $this->hourAtMinute);
            $hour += $this->hourRule;
            if ($hour <= 23) {
                array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
            }
        }
    }

    /**
     * 每天规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function dayRule()
    {
        $day = 0;
        $monthDayNum = date("t");
        $year = date('Y');
        $month = date('m');
        if (!empty($this->dayAtTime)) {
            list($hour, $minute) = explode(':', $this->dayAtTime);
        } else {
            $hour = 0;
            $minute = 0;
        }
        while ($day < $monthDayNum) {
            $day += $this->dayRule;
            if ($day <= $monthDayNum) {
                $this->datetime->setDate($year, $month, $day);
                $this->datetime->setTime($hour, $minute, 0);
                array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
            }
        }
    }

    /**
     * 每周规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function weekRule()
    {
        $monthDayNum = date("t");
        $day = 0;
        $year_month = date('Y-m');
        $year = date('Y');
        $month = date('m');
        while ($day < $monthDayNum) {
            $day += 1;
            $week = date("w", strtotime($year_month . '-' . $day));
            if ($week == 0) {
                $this->datetime->setDate($year, $month, $day);
                $this->datetime->setTime(0, 0, 0);
                array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
            }
        }
    }

    /**
     * 每月规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function monthRule()
    {
        $month = 0;
        $year = date('Y');
        if (!empty($this->dayAtTime)) {
            list($hour, $minute) = explode(':', $this->dayAtTime);
        } else {
            $hour = 0;
            $minute = 0;
        }
        while ($month < 12) {
            $month += 1;
            $this->datetime->setDate($year, $month, $this->monthAtDay);
            $this->datetime->setTime($hour, $minute, 0);
            array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
        }
    }

    /**
     * 季度规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function quarterlyRule()
    {
        $year = date('Y');
        $this->datetime->setDate($year, 1, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
        $this->datetime->setDate($year, 4, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
        $this->datetime->setDate($year, 8, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
        $this->datetime->setDate($year, 12, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
    }

    /**
     * 每年规则生成
     * @Author: rocky
     * 2019/8/19 12:00
     */
    private function yearRule()
    {
        $year = date('Y');
        $this->datetime->setDate($year, 1, 1);
        $this->datetime->setTime(0, 0, 0);
        array_push($this->rule, $this->datetime->format('Y-m-d H:i'));
    }
}
