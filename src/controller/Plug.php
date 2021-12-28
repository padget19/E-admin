<?php


namespace Eadmin\controller;

use Eadmin\Admin;
use Eadmin\component\basic\Button;
use Eadmin\component\basic\Card;
use Eadmin\component\basic\Html;
use Eadmin\component\basic\Space;
use Eadmin\component\basic\Tabs;
use Eadmin\component\basic\Tag;
use Eadmin\component\basic\TimeLine;
use Eadmin\component\form\FormAction;
use Eadmin\component\layout\Content;
use Eadmin\Controller;
use Eadmin\form\drive\Config;
use Eadmin\grid\Actions;
use Eadmin\grid\Grid;
use Symfony\Component\Process\Process;
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
        $tabs = Tabs::create();
        $cates = Admin::plug()->getCate();
        $tabs->pane('全部', $this->grid());
        $tabs->pane('已安装', $this->grid(0, 1));
        foreach ($cates as $cate) {
            $tabs->pane($cate['name'], $this->grid($cate['id']));
        }
        return
            $content->title('插件管理')->content(
                Card::create($tabs)
            );

    }

    public function grid($cate_id = 0, $type = 0)
    {
        $search = $this->request->get('quickSearch');
        $page = $this->request->get('page', 1);
        $size = $this->request->get('size', 20);
        if ($type == 1) {
            $datas = Admin::plug()->installed($search, $page, $size);

        } else {
            $datas = Admin::plug()->all($search, $cate_id, $page, $size);
        }

        $grid = new Grid($datas['list']);
        $grid->drive()->setTotal($datas['total']);
        $grid->title('插件管理');
        $grid->hideSelection();
        $grid->column('cate.name', '分类')->tag('info', 'plain');
        $grid->column('composer', '名称')->display(function ($val, $data) {
            return Html::div()->content([
                Html::div()->content(Tag::create($data['name'])->size('mini')->effect('dark')),
                Html::div()->content($data['composer']),
                Html::div()->content($data['desc']),
            ]);
        });

        $grid->column('install_version', '版本');
        $grid->actions(function (Actions $actions, $rows) {
            $actions->hideDel();

            $timeLine = TimeLine::create();
            foreach ($rows['version'] as $version) {
                $descs = explode("\n", $version['desc']);
                $html = Html::div();
                foreach ($descs as $desc) {
                    $html->content(Html::div()->content($desc));
                }
                if (count($version['require']) > 0) {
                    $html->content(Html::div()->content('依赖插件：')->style(['marginTop' => '5px']));
                }
                $space = Space::create();
                foreach ($version['require'] as $require) {
                    $space->content(Tag::create($require['title'])->size('mini'));
                }
                $html->content($space);
                $timeLine->item($html)->timestamp($version['version']);
            }
            if (!empty($rows['git_remote'])) {
                $actions->append(
                    Button::create('更新到git')
                        ->save(['path' => $rows['path'], 'git_remote' => $rows['git_remote']], 'plug/uploadGit', 'git commit')->input()
                );
            }
            $actions->append(
                Button::create('历史版本')
                    ->dialog()
                    ->content($timeLine)
            );
            if (isset($rows['setting'])) {
                $actions->append(
                    Button::create('设置')
                        ->plain()
                        ->typePrimary()
                        ->dialog()->content($rows['setting'])->whenShow(!empty($rows['setting']))
                );
            }
            if ($rows['install']) {
                if ($rows['status']) {
                    $actions->append(
                        Button::create('禁用')->sizeSmall()->typeInfo()->save(['id' => $rows['composer'], 'status' => 0], 'plug/enable', '确认禁用？')
                    );
                } else {
                    $actions->append(
                        Button::create('启用')->sizeSmall()->typeSuccess()->save(['id' => $rows['composer'], 'status' => 1], 'plug/enable', '确认启用？')
                    );
                }
                $plug = array_column($rows['version'], 'require', 'version');

                $require = $plug[$rows['install_version']];
                $actions->append(
                    Button::create('卸载')
                        ->sizeSmall()
                        ->typeDanger()
                        ->when(count($require) > 0, function (Button $button) use ($rows, $require) {
                            return $button->dialog()->content($this->uninstallAll($rows['composer'], $rows['path'], $require));
                        }, function (Button $button) use ($rows) {
                            return $button->save(['id' => $rows['composer'], 'path' => $rows['path']], 'plug/uninstall', '确认卸载？');
                        })
                );
            } else {
                $actions->append(
                    Button::create('安装')
                        ->sizeSmall()
                        ->typePrimary()
                        ->dialog()
                        ->content($this->install($rows['composer'], $rows['version']))
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
            Button::create('登录')
                ->typeDanger()
                ->whenShow(!Admin::plug()->isLogin())
                ->sizeSmall()
                ->dialog()
                ->form($this->login()),
        ]);
        $grid->quickSearch();
        return $grid;
    }

    public function login()
    {
        return Form::create([], function (Form $form) {
            $form->text('username', '账号')->required();
            $form->password('password', '密码')->required();
            $form->actions()->submitButton()->content('登录');
            $form->saving(function ($post) {
                $res = Admin::plug()->login($post['username'], $post['password']);
                if ($res) {
                    admin_success_message('登录成功');
                } else {
                    admin_error_message('登录失败，账号密码错误');
                }
            });
        });
    }

    public function uploadGit($path, $git_remote, $inputValue)
    {
        if (empty($inputValue)) {
            admin_error_message('commit 不能为空');
        }
        $girDir = $path . DIRECTORY_SEPARATOR . '.git';
        if (!is_dir($girDir)) {
            $cmd = "cd $path && git init && git remote rm origin";
            exec($cmd, $out, $result);
            $cmd = "cd $path && git init && git checkout -b eadmin";
            exec($cmd, $out, $result);
            $cmd = "cd $path && git remote add origin $git_remote";
            exec($cmd, $out, $result);
        }

        $cmd = "git config user.name 'eadmin' && git config user.email 'eadmin@eadmin.com' && git add . && git commit -m $inputValue";
        $process = new Process($cmd, $path);
        $process->run();
        if (!$process->isSuccessful()) {
            admin_error($process->getOutput(), $process->getErrorOutput());
        }
        $process = new Process('git push origin eadmin', $path);
        $process->run();
        if ($process->isSuccessful()) {
            admin_success_message('git上传成功');
        } else {
            admin_error($process->getOutput(), $process->getErrorOutput());
        }
    }

    /**
     * 安装
     * @param $composer
     * @param $data
     * @auth false
     * @login true
     * @return Form
     */
    public function install($composer, $data)
    {
        $options = array_column($data, 'version', 'id');
        $form = new Form([]);
        $form->select('id', '版本')->options($options)->required();
        $form->actions(function (FormAction $formAction) {
            $formAction->submitButton()->content('安装');
            $formAction->hideResetButton();
        });
        $form->saving(function ($post) use ($data, $composer) {
            if (!Admin::plug()->isLogin()) {
                admin_error_message('请先登录插件');
            }

            $this->requireInstall($data, $post['id']);
            $urls = array_column($data, 'url', 'id');
            $versions = array_column($data, 'version', 'id');
            Admin::plug()->install($composer, $urls[$post['id']], $versions[$post['id']]);
            admin_success_message('安装完成');
        });
        return $form;
    }

    /**
     * 卸载
     * @auth false
     * @login true
     */
    public function uninstall()
    {
        $path = $this->request->put('path');
        $name = $this->request->put('id');
        Admin::plug()->uninstall($name, $path);
        admin_success_message('卸载完成');
    }

    protected function getRequires($require)
    {
        $options = array_column($require, 'title', 'composer');
        foreach ($require as $req) {
            foreach ($req['version'] as $row) {
                $options = array_merge($options, $this->getRequires($row['require']));
            }
        }
        return $options;
    }

    /**
     * 卸载
     * @auth false
     * @login true
     */
    public function uninstallAll($composer, $path, $require)
    {

        $form = new Form([]);
        $form->checkbox('requires', '卸载依赖')->options($this->getRequires($require));
        $form->actions(function (FormAction $formAction) {
            $formAction->submitButton()->content('卸载');
            $formAction->hideResetButton();
            $formAction->confirm('确认卸载?');
        });
        $form->saving(function ($post) use ($composer, $path) {
            foreach ($post['requires'] as $require) {
                Admin::plug()->uninstall($require, Admin::plug()->getPath() . '/' . $require);
            }
            Admin::plug()->uninstall($composer, $path);
            admin_success_message('卸载完成');
        });
        return $form;
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
            $namespace = $post['namespace'];
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
        Admin::plug()->enable($id, $status);
        admin_success_message('操作完成');
    }

    /**
     * @param $data
     * @param $version
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function requireInstall($data, $version)
    {
        $urls = array_column($data, 'url', 'id');
        $versions = array_column($data, 'version', 'id');
        $requires = array_column($data, 'require', 'id');
        $requires = $requires[$version];
        foreach ($requires as $require) {
            if (!Db::name('system_plugs')->where('name', $require['composer'])->find()) {
                Admin::plug()->install($require['composer'], $require['url'], $require['version_number']);
                $this->requireInstall($require['version'], $require['id']);
            }
        }
    }
}
