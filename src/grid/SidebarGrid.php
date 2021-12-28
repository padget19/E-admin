<?php


namespace Eadmin\grid;


use Eadmin\Admin;
use Eadmin\component\basic\Button;
use Eadmin\component\basic\Html;
use Eadmin\component\Component;
use Eadmin\component\form\field\Tree;
use Eadmin\component\layout\Row;
use Eadmin\traits\CallProvide;

use think\exception\HttpResponseException;
use think\Model;

class SidebarGrid extends Component
{
    use CallProvide;
    protected $name = 'html';
    protected $tree;
    protected $sidebar;
    protected $grid;
    protected $tools = [];
    protected $id = '';
    protected $treePid = '';
    protected $treeId = '';
    protected $db;
    /**
     * @param Model $model
     * @param Grid $grid
     * @param string $id id字段名称
     * @param string $name 显示label字段名称
     * @param string $children 子级字段名称
     * @return static
     */
    public static function create(Model $model, $grid, $id = 'id', $name = 'name', $children = 'children')
    {

        $self =  new static($model, $grid, $id, $name, $children);
        $self->parseCallMethod(true,2);

        return $self;
    }

    /**
     * 树形
     * @param string $pid 父级pid
     * @return $this
     */
    public function treePid($pid='pid',$id = 'id')
    {
        $this->treePid = $pid;
        $this->treeId = $id;
        return $this;
    }

    public function form($form, $width = '40%')
    {
        $this->tools['addButton'] = Button::create('添加')
            ->typePrimary()
            ->icon('el-icon-plus')
            ->sizeSmall()
            ->dialog()->width($width)->form($form);
        $button = Button::create('编辑')
            ->typeWarning()
            ->icon('el-icon-edit')
            ->sizeSmall();
        $this->tools['editButton'] = $button->dialog()->width($width)->form($form);
        $this->tools['edit'] = $button;
        $this->sidebar->attr('tools', $this->tools);
        return $this;
    }
    public function model(){
        return $this->db;
    }
    /**
     * 头部内容
     * @param $content
     * @return $this
     */
    public function header($content)
    {
        $this->sidebar->attr('header', true);
        $this->sidebar->content($content, 'header');
        return $this;
    }
    public function sidebar(){
        return $this->sidebar;
    }
    /**
     * 高度
     * @param int $height
     * @return $this
     */
    public function height(int $height)
    {
        $this->tree->style(['maxHeight' => $height . 'px', 'overflowY' => 'auto']);
        return $this;
    }

    /**
     * 默认选择
     * @param string|int $value
     * @return $this
     */
    public function default($value)
    {
        $this->sidebar->attr('default', $value);
        return $this;
    }

    /**
     * 请求字段名
     * @param $name
     * @return $this
     */
    public function field($name)
    {
        $this->sidebar->attr('field', $name);
        return $this;
    }
    /**
     * 隐藏添加按钮
     * @param bool $bool
     * @return $this
     */
    public function hideAdd($bool=true)
    {
        $this->sidebar->attr('hideAdd', $bool);
        return $this;
    }
    /**
     * 隐藏编辑按钮
     * @param bool $bool
     * @return $this
     */
    public function hideEdit($bool=true)
    {
        $this->sidebar->attr('hideEdit', $bool);
        return $this;
    }



    /**
     * 隐藏删除按钮
     * @param bool $bool
     * @return $this
     */
    public function hideDel($bool=true)
    {
        $this->sidebar->attr('hideDel', $bool);
        return $this;
    }
    /**
     * 隐藏工具栏
     * @param bool $bool
     * @return $this
     */
    public function hideFilter($bool=true){
        $this->sidebar->attr('hideFilter', $bool);
        return $this;
    }
    /**
     * 隐藏工具栏
     * @param bool $bool
     * @return $this
     */
    public function hideTools($bool=true){
        $this->sidebar->attr('hideTools', $bool);
        return $this;
    }
    /**
     * 附加参数
     * @param $data
     * @return $this
     */
    public function params(array $data)
    {
        $this->sidebar->attr('params', $data);
        return $this;
    }

    public function __construct(Model $model, $grid, $id, $name, $children)
    {
        $this->attr('data-tag','div');
        $this->id = $id;
        $this->model = $model;
        $this->db = $model->db();
        $this->grid = $grid;
        $this->tree = Tree::create();
        $this->tree->setName('ElTree');
        $this->tree
            ->nodeKey($id)
            ->props(['children' => $children, 'label' => $name])
            ->defaultExpandAll()
            ->expandOnClickNode(false)
            ->highlightCurrent();
        $this->sidebar = new \Eadmin\component\grid\Sidebar();
        $this->sidebar->attr('tree', $this->tree);
        if($this->grid){
            $this->sidebar->bindAttr('gridValue', $this->grid->bindAttr('modelValue'), true);
            $this->sidebar->bindAttr('gridParams', $this->grid->bindAttr('addParams'), true);
        }
    }
    public function jsonSerialize()
    {
        $this->sidebar->attr('remoteParams',$this->getCallMethod());
        //删除
        if(request()->has('eadmin_sidebar_delete')){
            $this->model->where($this->model->getPk(),request()->param('id'))->delete();
            admin_success_message('删除成功');
        }
        //加载数据
        if(request()->has('eadmin_sidebar_data')){
            $data = $this->db->select()->toArray();
            if ($this->treePid) {
                $data = Admin::tree($data, $this->treeId, $this->treePid);
            }
            throw new HttpResponseException(json([
                'code' => 200,
                'data' => $data,
            ]));
        }
        if(is_null($this->grid)){
            return $this->sidebar;
        }else{
            $row = new Row();
            $row->gutter(10);
            $row->column($this->sidebar, 5);
            $row->column($this->grid, 19);
            $this->content($row);
        }
        return parent::jsonSerialize(); // TODO: Change the autogenerated stub
    }
}
