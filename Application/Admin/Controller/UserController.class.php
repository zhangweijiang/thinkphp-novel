<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 用户类
 */
namespace Admin\Controller;

class UserController extends BaseController
{

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
    }


    public function userList()
    {
        

        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员列表',
                'url' => U('Admin/User/userList')
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('user');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $map = array();
        $name = I('name');
        $status = I('status');
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        if($status){
            $map['status'] = $status;
            $this->assign('status', $status);
        }
        $psize = C('PAGE') ? C('PAGE') : 10;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '会员列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //删除用户
    public function userDel()
    {
        $id = $_GET['id'];//必须使用get方法
        $m = M('User');
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

    public function updateStatus(){
        $id = I('post.id');
        $status = I('post.status');
        if($status==1){
            $status = 2;
        }else if($status==2){
            $status = 1;
        }
        //status的值1表示启用，2表示禁用
        $re = M('user')->where(array('id'=>$id))->save(array('status'=>$status));
        if ($re) {
            $info['status'] = 1;
            $info['msg'] = '修改成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '修改失败!';
        }
        $this->ajaxReturn($info);
    }

}