<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 标签类
 */
namespace Admin\Controller;

class LabelController extends BaseController
{

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
    }

    //标签列表
    public function labelList()
    {


        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '标签列表',
                'url' => U('Admin/Label/labelList')
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('label');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $map = array();
        $name = I('name');
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $psize = C('PAGE') ? C('PAGE') : 10;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '标签列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //删除标签
    public function labelDel()
    {
        $id = $_GET['id'];//必须使用get方法
        $m = M('label');
        if (!$id) {
            $info['status'] = 0;
            $info['msg'] = 'ID不能为空!';
            $this->ajaxReturn($info);
        }
        $re = $m->delete($id);
        if ($re) {
            $info['status'] = 1;
            $info['msg'] = '删除成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '删除失败!';
        }
        $this->ajaxReturn($info);
    }

    public function labelSet()
    {
        $id = I('id');
        $m = M('label');
        //dump($m);
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '标签列表',
                'url' => U('Admin/Label/labelList')
            ),
            '1' => array(
                'name' => '设置分类',
                'url' => U('Admin/Label/labelSet', array('id' => $id))
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            $data = I('post.');

            if ($id) {
                $re = $m->save($data);
                if (FALSE !== $re) {
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！'.$data['iskj'];
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            } else {
                $data['ctime'] = time();
                $re = $m->add($data);
                if ($re) {
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！';
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            }
            $this->ajaxReturn($info);
        }

        //处理编辑界面
        if ($id) {
            $cache = $m->where('id=' . $id)->find();
            $this->assign('cache', $cache);
        }
        $this->display();
    }


}