<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-02-01
 * Time: 21:28
 */

namespace Eadmin\component\grid;


use Eadmin\component\basic\Confirm;
use Eadmin\component\basic\Dialog;
use Eadmin\component\Component;

/**
 * Class BatchAction
 * @package Eadmin\component\grid
 * @method $this url(string $value) ajax请求url
 * @method $this params(array $value) 提交ajax参数

 */
class BatchAction extends Component
{
    protected $name = 'BatchAction';

    public function __construct($content)
    {
        parent::__construct();
        $this->attr('gridBatch',true);
        $this->event('gridRefresh',[]);
        $this->content($content);
    }

    /**
     * 确认框
     * @param string $message  正文内容
     * @param string $title  标题
     * @param string $type success / info / warning / error
     * @return $this
     */
    public function confirm($message,$title='提示',$type=''){
        $this->attr('confirm',[
            'message'=>$message,
            'title'=>$title,
            'type'=>$type
        ]);
        return $this;
    }
    /**
     * 创建批量操作
     * @param $content
     * @return BatchAction
     */
    public static function create($content)
    {
        return new self($content);
    }
    /**
     * 模态对话框
     * @return Dialog
     */
    public function dialog()
    {
        $dialog = Dialog::create($this);
        return $dialog;
    }
}
