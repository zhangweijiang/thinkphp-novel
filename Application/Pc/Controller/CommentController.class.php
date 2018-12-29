<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 消息中心
 */
namespace Pc\Controller;

class CommentController extends BaseController
{

    //消息中心页面
    public function index(){
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
        $this->assign('title','消息中心');

        //我的消息全部置为已读
        $message = M('comment')->where(array('author_id'=>$_SESSION['user']['id'],'read'=>0))->select();
        foreach($message as $m){
            M('comment')->where(array('id'=>$m['id']))->save(array('read'=>1));
        }

        //我收藏的书籍
        $user_id = $_SESSION['user']['id'];
        $where['author_id'] = $user_id;
        import('Think.Page');// 导入分页类
        $m = M('comment');
        $count = $m->where($where)->count();
        $Page = new \Think\Page($count);// 实例化分页类 传入总记录数
        $Page->listRows = 6;  //每页6条
        $Page->firstRow = (I('p') ? I('p')-1:0) * $Page->listRows;
        $show = $Page->show();// 分页显示输出
        $commentList = $m->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();// $Page->firstRow 起始条数 $Page->listRows 获取多少条
        $this->assign('commentList',$commentList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    //作者回复评论
    public function reply(){
        $id = I('post.id');
        $reply = I('post.reply');
        $re = M('comment')->where(array('id'=>$id))->save(array('reply'=>$reply,'reply_time'=>time()));
        if($re!==FALSE){
            ajaxReturn($re,'评论回复成功',1);
        }else{
            ajaxReturn($re,'评论回复失败',1);
        }
    }

    //作者删除自己的回复
    public function deleteReply(){
        $id = I('post.id');
        $re = M('comment')->where(array('id'=>$id))->save(array('reply'=>NULL,'reply_time'=>NULL));
        if($re!==FALSE){
            ajaxReturn($re,'评论删除成功',1);
        }else{
            ajaxReturn($re,'评论删除失败',1);
        }
    }


}