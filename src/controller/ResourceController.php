<?php
declare (strict_types=1);

namespace Eadmin\controller;

use Eadmin\Controller;
use think\Request;

class ResourceController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $call = $this->call();
        if (request()->has('eadmin_export')) {
            return $call->exportData();
        }
        return $call;
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $form = $this->call();
        return $form->save($request->post());
    }

    public function create()
    {
        return $this->call();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        return $this->call()->edit($id);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->call();
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        if ($id == 'batch') {
            return $this->call()->update($this->request->put('eadmin_ids'), $request->put());
        } else {
            $form = $this->call();
            return $form->save($request->put());
        }
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if ($id == 'delete') {
            $ids = request()->delete('eadmin_ids');
        } else {
            $ids = explode(',', $id);
        }
        $res = $this->call()->destroy($ids);
        if ($res !== false) {
            admin_success('操作完成', '删除成功');
        } else {
            admin_error_message('数据保存失败');
        }
    }

    protected function call()
    {
        $class    = request()->param('eadmin_class');
        $action   = request()->param('eadmin_function');
        $instance = app($class);
        $reflect  = new \ReflectionMethod($instance, $action);
        $class = explode('\\',$class);
        $controller = end($class);
        $app = request()->param('eadmin_app','');
        app('http')->name($app);
        app()->setNamespace("app\\".$app);
        $this->request->setController($controller);
        $actionName = $reflect->getName();
        $this->request->setAction($actionName);
		$paramArr = [];

		foreach ($this->request->param() as $field => $param){
			if(is_string($param) && !is_null(json_decode($param))){
				$paramArr[$field] = json_decode($param, true);
			}elseif ($param == 'true'){
				$paramArr[$field] = true;
			}elseif($param == 'false'){
				$paramArr[$field] = false;
			} else {
				$paramArr[$field] = $param;
			}
		}

		return app()->invokeReflectMethod($instance, $reflect, $paramArr);
    }
}
