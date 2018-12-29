<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 章节
 */
namespace Pc\Controller;

class ChapterController extends BaseController
{

   public function index(){
       //导航栏分类
       $bookClassList1 = M('book_class')->where(array('type'=>1))->limit(7)->order('id asc')->select();
       $bookClassList2 = M('book_class')->where(array('type'=>1))->limit(8,14)->order('id asc')->select();
       $this->assign('bookClassList1',$bookClassList1);
       $this->assign('bookClassList2',$bookClassList2);

       //标题
       $this->assign('title','小说阅读页');

       //书籍信息
       $book_id = base64_decode(I('get.book_id'));
       $id = base64_decode(I('get.id'));
       $book = M('book')->where(array('id'=>$book_id))->find();
       if($book['count']>10000){
           $book['count'] = round($book['count']/10000,2).'万';
       }
       if($book['collection']>10000){
           $book['collection'] = round($book['collection']/10000,2).'万';
       }
       //该书的第一章节id
       $book['chapter_first_id'] = M('book_chapter')->where(array('book_id'=>$book['id'],'status'=>1))->order('update_time asc')->find()['id'];
       $book['is_collection'] = M('collection')->where(array('user_id'=>$_SESSION['user']['id'],'book_id'=>$book['id']))->find();//是否在书架,为空表示不在书架
       $this->assign('id',$id);//章节id
       $this->assign('book_id',$book_id);//书籍id
       $this->assign('book',$book);

       //章节信息
       $chapter = M('book_chapter')->where(array('id'=>$id))->find();
       $chapter['content'] = str_replace(array("\r\n", "\r", "\n"),"\n",$chapter['content']);
       $chapter['content_arr'] = explode("\n",$chapter['content']);
       $this->assign('chapter',$chapter);

       //免费的目录
       $freeDir = M('book_chapter')->where(array('book_id'=>$book['id'],'status'=>1))->limit(0,$book['outh'])->order('update_time asc,id asc')->select();
       $this->assign('freeDir',$freeDir);

       //vip目录
       $vipDir = M('book_chapter')->where(array('book_id'=>$book['id'],'status'=>1))->limit($book['outh'],10000000)->order('update_time asc,id asc')->select();
       $this->assign('vipDir',$vipDir);

       //vip目录需要登录
       $vip = array();
       foreach($vipDir as $vv){
           $vip[] = $vv['id'];
       }
       if(in_array($id,$vip)){
           if($this->isLogin()==false){ //判断是否登录，没登录跳转到登录页面
               $_SESSION['url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
               $this->redirect('Pc/Login/index');
           }
       }
       $this->display();

   }

    //章节评论
    public function comment(){
       $data = I('post.');
       $book = M('book')->where(array('id'=>$data['book_id']))->find();
       $data['book_name'] = $book['name'];
       $data['author_id'] = $book['user_id'];
       $data['author_name'] = $book['username'];
       $data['user_id'] = $_SESSION['user']['id'];
       $data['user_name'] = $_SESSION['user']['name'];
       $data['user_img'] = $_SESSION['user']['img'];
       $data['content_time'] = time();
       $data['chapter_title'] = M('book_chapter')->where(array('id'=>$data['chapter_id']))->find()['title'];
       $re = M('comment')->add($data);
       if($re>0){
          ajaxReturn($re,'评论成功',1);
       }else{
           ajaxReturn($re,'评论失败',0);
       }
    }





}