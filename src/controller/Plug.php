<?php


namespace Eadmin\controller;

use Eadmin\component\basic\Button;
use Eadmin\component\basic\Card;
use Eadmin\component\basic\Tabs;
use Eadmin\component\layout\Content;
use Eadmin\Controller;
use Eadmin\form\drive\Config;
use Eadmin\grid\Actions;
use Eadmin\grid\Grid;
use think\facade\Console;
use think\facade\Db;
use Eadmin\controller\BaseAdmin;
use Eadmin\form\Form;
use Eadmin\grid\Column;
use Eadmin\grid\Table;
use Eadmin\service\PlugService;

/**
 * 插件管理
 * Class Plug
 * @package app\admin\controller
 */
class Plug extends Controller
{
    /**
     * 插件列表
     * @auth false
     * @login true
     */
    public function index(Content $content)
    {
        return
            $content->title('插件管理')->content(
                Card::create(
                    Tabs::create()
                        ->pane('全部', $this->grid(0))
                        ->pane('已安装', $this->grid(1))
                )
            );

    }

    public function grid($type)
    {
        $search = $this->request->get('quickSearch');
        if ($type == 1) {
            $datas = PlugService::instance()->installed($search);
        } else {
            $datas = PlugService::instance()->all($search);
        }
        $grid = new Grid($datas);
        $grid->title('插件管理');
        $grid->hideSelection();
        $grid->column('name', '插件信息')->display(function ($val, $rows) {
            $html = <<<EOF
<div style='display: flex;justify-content: space-between;align-items: center'>
    <div style="flex: 1">
         <el-link target="_blank" href="{$rows['web_url']}">{$rows['name']}</el-link>&nbsp;<el-tag size="small" effect="dark">{$rows['title']}</el-tag>&nbsp;<el-tag size="small" type="info">{$rows['version']}</el-tag><br>{$rows['description']}
    </div>
</div>
EOF;
            return $html;
        });
        $grid->column('author', '作者')->display(function ($val, $rows) {
            $html = <<<EOF
{$rows['author']} <el-tag size="mini">{$rows['email']}</el-tag>
EOF;
            return $html;
        });
        $grid->actions(function (Actions $actions, $rows) {
            $actions->hideDel();
            $actions->column()->fixed(false);
            if ($rows['install']) {
                if ($rows['status']) {
                    $actions->append(
                        Button::create('禁用')->sizeSmall()->typeInfo()->save(['id' => $rows['name'], 'status' => 0], 'plug/enable', '确认禁用？')
                    );
                } else {
                    $actions->append(
                        Button::create('启用')->sizeSmall()->typeSuccess()->save(['id' => $rows['name'], 'status' => 1], 'plug/enable', '确认启用？')
                    );
                }
                $actions->append(
                    Button::create('卸载')->sizeSmall()->typeDanger()->save(['id' => $rows['name'], 'path' => $rows['path'], 'type' => 2], 'plug/install', '确认卸载？')
                );
            } else {
                $actions->append(
                    Button::create('安装')->sizeSmall()->typePrimary()->save(['id' => $rows['name'], 'path' => $rows['download'], 'type' => 1], 'plug/install', '确认安装？')
                );
            }
        });
        $grid->hideDeleteButton();
        $grid->tools([
            Button::create('创建扩展')
                ->typeWarning()
                ->sizeSmall()
                ->dialog()
                ->form($this->add()),
        ]);
        $grid->quickSearch();
        return $grid;
    }

    /**
     * 创建扩展
     * @auth false
     * @login true
     */
    public function add()
    {
        $form = new Form(new Config());
        $form->text('name', '名称')->placeholder('请输入扩展名称，例如：eadmin/plug')->required();
        $form->text('description', '描述');
        $form->text('namespace', '命名空间');
        $form->saving(function ($post) {
            $name = $post['name'];
            if (!strpos($name, '/')) {
                admin_warn_message('扩展名称格式错误，例如：eadmin/plug');

            }
            $cmd['name'] = $name;
            $description = $post['description'];
            $namespace   = $post['namespace'];
            if (!empty($description)) {
                array_push($cmd, "--description={$description}");
            }
            if (!empty($namespace)) {
                array_push($cmd, "--namespace={$namespace}");
            }
            Console::call('eadmin:plug', $cmd);
            admin_success_message('添加成功');
        });
        return $form;

    }

    /**
     * 启用/禁用
     * @auth false
     * @login true
     */
    public function enable($id, $status)
    {
        PlugService::instance()->enable($id, $status);
        admin_success_message('操作完成');
    }

    /**
     * 安装/卸载
     * @auth false
     * @login true
     */
    public function install()
    {
        $type = $this->request->put('type');
        $path = $this->request->put('path');
        $name = $this->request->put('id');
        if ($type == 1) {
            PlugService::instance()->install($name, $path);
            admin_success_message('安装完成');
        } else {
            PlugService::instance()->uninstall($name, $path);
            admin_success_message('卸载完成');
        }
    }
}
