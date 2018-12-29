<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 留言类
 */
namespace Admin\Controller;

class MessageController extends BaseController
{

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
    }


    public function messageList()
    {
        

        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '留言列表',
                'url' => U('Admin/Message/messageList')
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('message');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $map = array();
        $title = I('title');
        if ($title) {
            $map['title'] = array('like', "%$title%");
            $this->assign('title', $title);
        }
        $psize = C('PAGE') ? C('PAGE') : 10;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '留言列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //删除留言
    public function messageDel()
    {
        $id = $_GET['id'];//必须使用get方法
        $m = M('message');
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
        //status的值1表示未处理，2表示已处理
        $re = M('message')->where(array('id'=>$id))->save(array('status'=>$status));
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