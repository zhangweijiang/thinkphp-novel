<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 小说详情页
 */
namespace Pc\Controller;
class BookController extends BaseController
{
    public function index()
    {
        //导航栏分类
        $bookClassList1 = M('book_class')->where(array('type'=>1))->limit(7)->order('id asc')->select();
        $bookClassList2 = M('book_class')->where(array('type'=>1))->limit(8,14)->order('id asc')->select();
        $this->assign('bookClassList1',$bookClassList1);
        $this->assign('bookClassList2',$bookClassList2);

        //标题
        $this->assign('title','小说详情页');

        //小说详情
        $id = base64_decode(I('id'));
        $book = M('book')->where(array('id'=>$id))->find();
        if($book['count']>10000){
            $book['count'] = round($book['count']/10000,2).'万';//字数
        }
        $book['label'] = explode(',',$book['label']);//标签
        $book['update'] = M('book_chapter')->where(array('book_id'=>$book['id'],'status'=>1))->order('id desc')->find();//最近更新
        $book['is_collection'] = M('collection')->where(array('user_id'=>$_SESSION['user']['id'],'book_id'=>$book['id']))->find();
        $book['chapter_first'] = M('book_chapter')->where(array('book_id'=>$book['id'],'status'=>1))->order('update_time asc')->find();
        $this->assign('book',$book);

        //喜欢这本书的人还喜欢
        $loveList = M('book')->where(array('class_id'=>$book['class_id'],'id'=>array('neq',$book['id'])))->order('collection desc,id desc')->limit(6)->select();//取出与该书籍相同分类并且收藏人数最多的前6条数据
        $this->assign('loveList',$loveList);
        //本周推荐
        $commendList = M('book')->where(array('class_id'=>$book['class_id'],'commend'=>1,'id'=>array('neq',$book['id'])))->order('collection desc,id desc')->limit(10)->select();//取出与该书籍相同分类,为推荐状态并且收藏人数最多的前10条数据
        $this->assign('commendList',$commendList);

        //免费的目录
        $freeDir = M('book_chapter')->where(array('book_id'=>$book['id'],'status'=>1))->limit(0,$book['outh'])->select();
        $freeCount = 0;
        foreach($freeDir as $v1){
            $freeCount += $v1['count'];
        }
        $this->assign('freeCount',$freeCount);//免费目录共多少字
        $this->assign('freeDir',$freeDir);

        //vip目录
        $vipDir = M('book_chapter')->where(array('book_id'=>$book['id'],'status'=>1))->limit($book['outh'],10000000)->select();

        $vipCount = 0;
        foreach($vipDir as $v2){
            $vipCount += $v2['count'];
        }
        $this->assign('vipCount',$vipCount);//vip目录共多少字
        $this->assign('vipDir',$vipDir);
        //这本书籍共几章
        $this->assign('dirCount',count($freeDir)+count($vipDir));

        //书籍评论
        import('Think.Page');// 导入分页类
        $m = M('comment');
        $count = $m->where(array('book_id'=>$book['id']))->count();
        $Page = new \Think\Page($count);// 实例化分页类 传入总记录数
        $Page->listRows = 3;  //每页3条
        $Page->firstRow = (I('p') ? I('p')-1:0) * $Page->listRows;
        $show = $Page->show();// 分页显示输出
        $commentList = $m->where(array('book_id'=>$book['id']))->limit($Page->firstRow.','.$Page->listRows)->select();// $Page->firstRow 起始条数 $Page->listRows 获取多少条
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('commentList',$commentList);
        $this->display();
    }

    //加入书夹
    public function addCollection(){
        if($this->isLogin()==false){ //判断是否登录，没登录跳转到登录页面
            $_SESSION['url'] = U('Pc/Book/index')."/id/".base64_encode(I('post.book_id'));
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
