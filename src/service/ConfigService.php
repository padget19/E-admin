<?php


namespace Eadmin\service;


use Eadmin\component\basic\Card;
use Eadmin\component\basic\TabPane;
use Eadmin\component\basic\Tabs;
use Eadmin\component\layout\Row;
use Eadmin\form\drive\Config;
use Eadmin\form\Form;
use Eadmin\model\SystemConfig;
use Eadmin\model\SystemConfigCate;
use Eadmin\Service;

class ConfigService extends Service
{
    protected $data = [];
    protected $level = 0;
    public function form(){
        return Card::create($this->build())->title('系统参数配置');
    }
    public function build($pid = 0){

        $cates = SystemConfigCate::where('status',1)->where('pid',$pid)->select();
        if($this->level % 2 == 0){
            $tabPosition = 'left';
        }else{
            $tabPosition = 'top';
        }
        $tabs = Tabs::create(null,'')->tabPosition($tabPosition);
        if($cates->isEmpty()){
            return [];
        }else{
            $this->level++;
        }
        foreach ($cates as $cate){
            $this->data = SystemConfig::where('status',1)->where('cate_id',$cate['id'])->select();
            $pane = '';
            if(!$this->data->isEmpty()){
                $pane = $this->configForm($this->data);
                $row = new Row();
                $row->column($pane,13);
                $pane = $row;
            }
            $tabPane = new TabPane();
            $tabPane->content($cate['name'], 'label');
            $tabPane->content($pane);
            $tabPane->content($this->build($cate['id']));
            $tabs->content($tabPane);
        }
        return $tabs;
    }
    public function configForm(){
        $form = new Form(new Config());
        $form->labelPosition('top');
        foreach ($this->data as $row){
            $type =  $row['type'];
            $component = $form->$type($row['name'],$row['label']);
            if($row['attribute']['multiple'] == 1){
                $component->multiple();
            }
            if($row['attribute']['required'] == 1){
                $component->required();
            }
            if(!empty($row['help'])){
                $component->help($row['help']);
            }
            if(in_array($type,['select','radio','checkbox'])){
                $options = $row['attribute']['options'];
                $options = array_column($options,'label','value');
                $component->options($options);
            }
        }
        return $form;
    }
}
