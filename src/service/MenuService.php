<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-04-11
 * Time: 14:21
 */

namespace Eadmin\service;

use Eadmin\Admin;
use Eadmin\model\SystemMenu;
use think\facade\Db;
use Eadmin\Service;

/**
 * 系统菜单服务
 * Class MenuService
 * @package app\admin\service
 */
class MenuService
{
   
    /**
     * 获取所有菜单
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function all($admin_visible = false)
    {
        $data = Db::name('system_menu')
            ->where('status', 1)
            ->when($admin_visible, function ($q) {
                $q->where('admin_visible', 1);
            })
            ->order('sort asc,id desc')
            ->cache(10)
            ->select()->toArray();
        
        return $data;
    }

    /**
     * 添加菜单
     * @param array $data
     */
    public function add(array $data){
        SystemMenu::create($data);
    }

    /**
     * 获取菜单
     * @param array $menuIds 包含的菜单id
     * @return array|mixed
     */
    public function menus(array $menuIds){
        $menus = $this->all();
        foreach ($menus as $key=>$menu){
            if(!in_array($menu['id'],$menuIds)){
                unset($menus[$key]);
            }
        }
        return $menus;
    }
    /**
     * 生成树形菜单
     * @return array
     */
    public function tree($admin_visible = false)
    {
        if (Admin::id() == config('admin.admin_auth_id')) {
            $menus = $this->all($admin_visible);
        } else {
            $menus = Admin::user()->menus();
        }
        return Admin::tree($menus);
    }

    /**
     * 生成菜单下拉框option
     * @param array $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function listOptions($data = null)
    {
        if (is_null($data)) {
            $data = Db::name('system_menu')->where('status', 1)->order('sort asc,id asc')->select();
        }
        $menusList = $this->getTreeLevel($data);
        foreach ($menusList as &$value) {
            $value['label'] = str_repeat("　├　", $value['level'] + 1) . $value['name'];
        }
        return $menusList;
    }

    protected function getTreeLevel($array, $pid = 0, $level = 0)
    {
        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        static $listMenus = [];
        foreach ($array as $key => $value) {
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['pid'] == $pid) {
                //父节点为根节点的节点,级别为0，也就是第一级
                $value['level'] = $level;
                //把数组放到list中
                $listMenus[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getTreeLevel($array, $value['id'], $level + 1);
            }
        }
        return $listMenus;
    }
}
