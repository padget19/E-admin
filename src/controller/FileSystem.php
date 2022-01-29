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
        $fileSystem->initPath(\think\facade\Filesystem::disk('local')->path('/'))
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
        $form->select('pid', admin_trans('filesystem.labels.cate'))
            ->options([0 => admin_trans('filesystem.topCate')] + array_column($options, 'label', 'id'))
            ->required();
        $form->text('name',  admin_trans('filesystem.labels.name'))->required();
        $form->radio('per_type', admin_trans('filesystem.labels.permission'))
            ->options(admin_trans('filesystem.options.per_type'))
            ->default(1);
        $form->switch('status',  admin_trans('filesystem.labels.show'))->default(1);
        $form->number('sort',  admin_trans('filesystem.labels.sort'))->default(0);
        $form->hidden('admin_id')->default(Admin::id());
        return $form;
    }
    //移动分类
    public function moveCate($ids,$cate_id){
        SystemFile::whereIn('id',$ids)->update(['cate_id'=>$cate_id]);
        admin_success(admin_trans('filesystem.success'), admin_trans('filesystem.moveFolderComplete'));
    }
    //新建文件夹
    public function mkdir($path)
    {
        if (is_dir($path)) {
            admin_error_message(admin_trans('filesystem.folderExist'));
        }
        mkdir($path, 0755);
        admin_success(admin_trans('filesystem.success'), admin_trans('filesystem.newFolderComplete'));
    }

    //重命名文件夹
    public function rename($name, $path)
    {
        if (is_dir($path)) {
            $newPath = dirname($path) . DIRECTORY_SEPARATOR . $name;
            if (is_dir($newPath)) {
                admin_error_message(admin_trans('filesystem.folderRenameExist'));
            }
            rename($path, $newPath);
            admin_success(admin_trans('filesystem.success'), admin_trans('filesystem.folderRenameComplete'));
        }
        admin_error_message(admin_trans('filesystem.folderNotExist'));
    }

    public function del($ids)
    {
        SystemFile::whereIn('id',$ids)->update(['is_delete'=>1]);
        admin_success(admin_trans('filesystem.success'), admin_trans('filesystem.deleleComplete'));
    }
}
