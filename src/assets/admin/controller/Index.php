<?php


namespace app\admin\controller;

use app\admin\example\BarCard;
use app\admin\example\LineCard;
use app\admin\example\PieCard;
use app\admin\example\ProgressCard;
use Eadmin\chart\Echart;


use Eadmin\chart\echart\LineChart;
use Eadmin\component\basic\Badge;
use Eadmin\component\basic\Card;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Statistic;
use Eadmin\component\basic\Tabs;
use Eadmin\component\form\field\Select;
use Eadmin\component\layout\Content;
use Eadmin\component\layout\Row;
use Eadmin\constant\Style;
use Eadmin\Controller;
use Eadmin\grid\Filter;
use Eadmin\grid\Grid;


/**
 * 控制台
 * Class Index
 * @package app\admin\controller
 */
class Index extends Controller
{
    /**
     * 仪表盘
     * @auth true
     * @login true
     * @method get
     */
    public function dashboard(Content $content)
    {
        $content->title('概览');


        $content->row(function (Row $row) {
            $row->gutter(10);
            $row->column(Statistic::create('统计', '1,128', 'el-icon-collection', '#884add', '#c08afa')->redirect('http://www.baidu.com'), 4);
            $row->column(Statistic::create('新增用户', '500', 'el-icon-user-solid', '#fc4b67', '#ff8a92'), 4);
            $row->column(Statistic::create('商品', '20', 'el-icon-goods', '#fbb016', '#ffc301'), 4);
            $row->column(Statistic::create('订单', '99', 'el-icon-s-help', '#34c17c', '#8adfb6'), 4);
            $row->column(Statistic::create('通知', '998', 'el-icon-message-solid', '#4789e7', '#c28af9'), 4);
            $row->column(Statistic::create('待办', '2,128', 'el-icon-s-opportunity', '#ff431e', '#fba457'), 4);
        });
        $content->row(function (Row $row)  {
            $row->gutter(10);
            $row->column(new LineCard(), 6);
            $row->column(new BarCard(), 6);
            $row->column(new PieCard(), 6);
            $row->column(new ProgressCard(), 6);
        });

        $content->row(function (Row $row) {
            $row->gutter(10);


            $row->column(Card::create(Tabs::create()
                ->pane('折线图',$this->lineEchart('line'))
                ->pane('柱状图',$this->lineEchart('bar'))
            ), 18);
            $row->column($this->rank(), 6);
        });


        $content->row(function (Row $row) {
            $row->gutter(10);
            $row->column($this->pieEchart(), 12);
            $row->column($this->radarEchart(), 12);
        });


        return $content;
    }

    public function line()
    {
        $echart = new LineChart('50px');
        $echart->hideLegend();
        $echart->xAxis(['', '', '', '', '', '', ''], [
            'show' => false,
        ]);
        $echart->series('sdf', [28, 40, 36, 52, 38, 60, 55], [
            'areaStyle' => [],
            'showSymbol' => false,
        ]);
        $echart->setOptions([
            'yAxis' => [
                'splitLine' => ['show' => false],
            ],
            'grid' => [
                'left' => '0',
                'right' => '0',
                'top' => '10%',
                'bottom' => '0',
            ],
        ]);
        return $echart;
    }

    public function rank()
    {
        $data = [
            [
                'id' => 1,
                'name' => '阿里',
            ],
            [
                'id' => 2,
                'name' => '京东',
            ],
            [
                'id' => 3,
                'name' => '拼多多',
            ],
            [
                'id' => 4,
                'name' => '唯品会',
            ],
            [
                'id' => 5,
                'name' => '京东超时',
            ],
            [
                'id' => 6,
                'name' => '唯品会',
            ],
            [
                'id' => 7,
                'name' => '苹果',
            ],
            [
                'id' => 8,
                'name' => '未知',
            ],
            [
                'id' => 8,
                'name' => '未知',
            ],
            [
                'id' => 8,
                'name' => '未知',
            ],
            [
                'id' => 8,
                'name' => '未知',
            ],
            [
                'id' => 8,
                'name' => '未知',
            ],
        ];
        $grid = new Grid($data);
        $grid->column('id', '排名')->display(function ($val) {
            $badge = Badge::create()->value($val);
            if ($val == 1) {
                $badge->type('danger');
            } elseif ($val == 2) {
                $badge->type('warning');
            } elseif ($val == 3) {
                $badge->type('success');
            } elseif ($val == 4) {
                $badge->type('primary');
            } else {
                $badge->type('info');
            }
            return $badge;
        });
        $grid->column('name', '名称');
        $grid->hidePage();
        $grid->hideTools();
        $grid->hideSelection();
        $grid->hideAction();
        return $grid;
    }

    /**
     * 雷达图
     */
    public function radarEchart()
    {
        $echart = new Echart('雷达图', 'radar');
        //不分组直接调用
//        $echart->table('system_user','create_time');
//        $echart->count('不分组系统用户数量',2);
//        $echart->sum('系统用户id总和','id',50);
//        $echart->sum('系统用户id1','id',50);
//        $echart->sum('系统用户id2','id',50);
//        $echart->sum('系统用户id3','id',100);

        //多模块分组
        $echart->group('系统用户', function ($echart) {
            $echart->table('system_user', 'create_time');
            $echart->count('系统用户数量', 50);
            $echart->sum('系统用户id总和', 'id', 50);
        });
        $echart->group('日志', function ($echart) {
            $echart->table('system_user', 'create_time');
            $echart->count('日志总数量', 50);
            $echart->sum('日志id总和', 'id', 1000);
        });
        return $echart;
    }

    /**
     * 漏斗图
     */
    public function funnelchart()
    {
        $echart = new Echart('漏斗图', 'funnel');
        $echart->table('system_user', 'create_time');
        $echart->count('日志总数量');
        $echart->sum('日志id总和', 'id');
        $echart->avg('日志id平均', 'id');
        return $echart;
    }

    /**
     * 饼图
     */
    public function pieEchart()
    {
        $echart = new Echart('饼图', 'pie');
        //不分组直接调用
        $echart->table('system_user', 'create_time');
        $echart->count('不分组系统用户数量');

        //多模块分组
        $echart->group('系统用户', function ($echart) {
            $echart->table('system_user', 'create_time');
            $echart->count('系统用户数量');
            $echart->sum('系统用户id总和', 'id');
        });
        $echart->group('日志', function ($echart) {
            $echart->table('system_user', 'create_time');
            $echart->count('日志总数量');
            $echart->sum('日志id总和', 'id');
            $echart->avg('日志id平均', 'id');
        });
        return $echart;
    }

    /**
     * 折线图
     */
    public function lineEchart($type)
    {
        $echart = new Echart('', $type,'335px');
        $contentChart = new Content();
        $contentChart->row(function (Row $row){
            $row->gutter(10)->attr('style',['textAlign'=>'center']);
            $row->column('<p>访问次数</p><p style="margin-top: 15px;font-size: 24px;color: #333;font-weight: 700">111</p>',6);
            $row->column('<p>新增用户</p><p style="margin-top: 15px;font-size: 24px;color: #333;font-weight: 700">351</p>',6);
            $row->column('<p>系统用户</p><p style="margin-top: 15px;font-size: 24px;color: #333;font-weight: 700">881</p>',6);
        });
        $echart->header($contentChart);
        $echart->table('system_user');
        //筛选
        $echart->filter(function (Filter $filter) {
            $filter->eq('id', '系统用户');
        });
        $echart->count('系统用户', function ($query) {
            $query->where('id', 1);
        });
        $echart->sum('新增用户', 'id');
        $echart->sum('访问次数', 'id');
        return $echart;

    }

    /**
     * 柱状图
     */
    public function barEchart()
    {
        $echart = new Echart('折线图', 'bar');
        $echart->table('system_user');
        //筛选
//        $echart->filter(function (Filter $filter) {
//            $filter->eq('id', 'id');
//        });
        $echart->count('系统用户', function ($query) {
            $query->where('id', 1);
        });
        $echart->sum('123', 'id');
        $echart->sum('ff', 'id');
        return $echart;
    }
}
