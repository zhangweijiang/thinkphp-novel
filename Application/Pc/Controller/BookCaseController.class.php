<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 我的书架
 */
namespace Pc\Controller;

class BookCaseController extends BaseController
{

    //我的书架页面
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
        $this->assign('title','我的书架');

        //我收藏的书籍
        $user_id = $_SESSION['user']['id'];
        $where['user_id'] = $user_id;
        import('Think.Page');// 导入分页类
        $m = M('collection');
        $count = $m->where($where)->count();
        $Page = new \Think\Page($count);// 实例化分页类 传入总记录数
        $Page->listRows = 10;  //每页10条
        $Page->firstRow = (I('p') ? I('p')-1:0) * $Page->listRows;
        $show = $Page->show();// 分页显示输出
        $bookList = $m->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();// $Page->firstRow 起始条数 $Page->listRows 获取多少条
        foreach($bookList as &$v){
            $v['book'] = M('book')->where(array('id'=>$v['book_id']))->find();
            $v['bookChapter'] = M('book_chapter')->where(array('book_id'=>$v['book_id'],'status'=>1))->order('update_time desc')->find();
        }
        $this->assign('bookList',$bookList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    //作品删除（从我的收藏夹移除）
    public function delete(){
        $book_id = I('post.book_id');
        $user_id = $_SESSION['user']['id'];
        $re = M('collection')->where(array('user_id'=>$user_id,'book_id'=>$book_id))->delete();
        if($re){
           ajaxReturn($re,'移除成功',1);
        }else{
            ajaxReturn($re,'移除失败',0);
        }
    }

    //加入书夹
    public function addCollection(){
        if($this->isLogin()==false){ //判断是否登录，没登录跳转到登录页面
            $_SESSION['url'] = U('Pc/BookCase/index');
            ajaxReturn('redirect','您还未登录，请登录！',0);
        }
        $user_id = $_SESSION['user']['id'];
        $user_name = $_SESSION['user']['name'];
        $book_id = I('post.book_id');
        $data = array(
            'user_id' => $user_id,
            'book_id' => $book_id,
            'user_name'=> $user_name,
            'ctime'  => time()
        );
        $re = M('collection')->add($data);
        if($re>0){
            ajaxReturn($re,'成功加入书架',1);
        }else{
            ajaxReturn($re,'加入书架失败',0);
        }
    }

}