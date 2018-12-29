<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 个人设置
 */
namespace Pc\Controller;
class PersonSettingController extends BaseController
{
    //个人设置页面
    public function index()
    {
        if($this->isLogin()==false){ //判断是否登录，没登录跳转到登录页面
            $_SESSION['url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->redirect('Pc/Login/index');
        }
        //导航栏分类
        $bookClassList1 = M('book_class')->where(array('type'=>1))->limit(7)->order('id asc')->select();
        $bookClassList2 = M('book_class')->where(array('type'=>1))->limit(8,14)->order('id asc')->select();
        $this->assign('bookClassList1',$bookClassList1);
        $this->assign('bookClassList2',$bookClassList2);

        //标题
        $this->assign('title','个人设置');

        //个人信息
        $user_id = $_SESSION['user']['id'];
        $user = M('user')->where(array('id'=>$user_id))->find();
        $user['book'] = M('book')->where(array('user_id'=>$user_id))->count();
        $user['collection'] = M('collection')->where(array('user_id'=>$user_id))->count();
        $this->assign('user',$user);
        $this->display();
    }
    //个人信息保存
    public function save(){
        $data = I('post.');
        $data['birthday'] = strtotime($data['birthday']);
        //判断昵称是否已经存在
        $where = array();
        $where['id'] = array('neq',$data['id']);
        $where['name'] = $data['name'];
        $user = M('user')->where($where)->find();
        if($user){//该昵称已存在
            ajaxReturn($data,'该昵称已存在，请重新设置',0);
        }
        $re = M('user')->where(array('id'=>I('post.id')))->save($data);
        if($re!==FALSE){//修改成功
            ajaxReturn($re,'成功',1);
        }else{//修改失败
            ajaxReturn($re,'修改失败',0);
        }
    }

    //修改密码
    public function savePwd(){
        $id = I('post.id');
        $password = md5(I('post.password'));
        $re = M('user')->where(array('id'=>$id))->save(array('password'=>$password));
        if($re!==FALSE){//修改成功
            ajaxReturn($re,'修改成功,请重新登录',1);
        }else{//修改失败
            ajaxReturn($re,'修改失败',0);
        }
    }


}
