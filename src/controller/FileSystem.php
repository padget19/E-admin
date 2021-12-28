<?php


namespace Eadmin\controller;


use Eadmin\Admin;
use Eadmin\component\grid\Sidebar;
use Eadmin\Controller;
use Eadmin\form\Form;
use Eadmin\grid\SidebarGrid;
use Eadmin\model\AdminModel;
use Eadmin\model\SystemFile;
use Eadmin\model\SystemFileCate;
use Eadmin\service\FileService;
use Eadmin\service\FileSystemService;
use think\db\Query;

class FileSystem extends Controller
{
    public function index($type=0)
    {
        $search = $this->request->get('search');
        $cate_id = $this->request->get('cate_id');
        $ext = $this->request->get('ext');
        $data = SystemFile::where('admin_id', Admin::id())
            ->where('is_delete',0)
            ->when($cate_id, ['cate_id'=>$cate_id])
            ->when($search, [['real_name', 'like', "%{$search}%"]])
            ->when($ext,function (Query  $query) use($ext){
                if($ext == 'image/*'){
                    $query->whereLike('file_type',"image/%");
                }else{
                    $ext = str_replace('.','',$ext);
                    $exts = explode(',',$ext);
                    $query->whereIn('ext',$exts);
                }

            })
            ->pages()
            ->select()->map(function ($item) {
                $item['dir'] = false;
                $item['size'] = FileService::instance()->getSize($item['file_size']);
                $item['author'] = AdminModel::where('id',$item['admin_id'])->value('nickname');
                $item['update_time'] = $item['create_time'];
                return $item;
            })->toArray();
        $fileSystem = new \Eadmin\component\basic\FileSystem($data);
        $fileSystem->initPath(FileSystemService::instance()->getPath())
            ->attr('height', '350px')
            ->attr('display','menu')
            ->uploadFinder();
        $grid = $fileSystem;
        if($type == 1){
            $grid = null;
        }
        $sidebarGrid = SidebarGrid::create(new SystemFileCate(), $grid,'id','label')
            ->treePid()
            ->form($this->cateForm())
            ->field('cate_id')
            ->height(362);
        if(empty($type)){
            $sidebarGrid->sidebar()->bindAttValue('dataSource',[],true);
            $grid->bindAttr('cate',$sidebarGrid->sidebar()->bindAttr('dataSource'));
        }
        $sidebarGrid->sidebar()->attr('fileSystem',$fileSystem);
        $sidebarGrid->model()
            ->field('id,name as label,pid')
            ->where('status', 1)
            ->where(function (Query $query){
                $query->whereOr('admin_id',Admin::id())->whereOr('per_type',0);
            });
        return $sidebarGrid;
    }
    public function cateForm()
    {
        $form = new Form(new SystemFileCate);
        $options = Admin::menu()->listOptions(SystemFileCate::where('admin_id',Admin::id())->select()->toArray());
        $form->select('pid', '分类')
            ->options([0 => '顶级分类'] + array_column($options, 'label', 'id'))
            ->required();
        $form->text('name', '分类名称')->required();
        $form->radio('per_type', '权限')
            ->options([1=>'仅自己',0=>'所有人'])
            ->default(1);
        $form->switch('status', '显示')->default(1);
        $form->number('sort', '排序')->default(0);
        $form->hidden('admin_id')->default(Admin::id());
        return $form;
    }
    //移动分类
    public function moveCate($ids,$cate_id){
        SystemFile::whereIn('id',$ids)->update(['cate_id'=>$cate_id]);
        admin_success('成功', '文件移动成功');
    }
    //新建文件夹
    public function mkdir($path)
    {
        if (is_dir($path)) {
            admin_error_message('文件夹已存在');
        }
        mkdir($path, 0755);
        admin_success('成功', '新建文件夹成功');
    }

    //重命名文件夹
    public function rename($name, $path)
    {
        if (is_dir($path)) {
            $newPath = dirname($path) . DIRECTORY_SEPARATOR . $name;
            if (is_dir($newPath)) {
                admin_error_message('重命名文件夹名称已存在');
            }
            rename($path, $newPath);
            admin_success('成功', '文件夹重命名成功');
        }
        admin_error_message('文件夹不存在');
    }

    public function del($ids)
    {
        SystemFile::whereIn('id',$ids)->update(['is_delete'=>1]);
        admin_success('成功', '删除完成');
    }
}
