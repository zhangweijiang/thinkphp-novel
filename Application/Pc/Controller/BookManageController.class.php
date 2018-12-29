<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 作品管理
 */
namespace Pc\Controller;
class BookManageController extends BaseController
{
    //作品管理页面
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
        $this->assign('title','作品管理');

        //分类
        $bookClassList = M('book_class')->select();
        $this->assign('bookClassList',$bookClassList);

        //我的书籍
        $user_id = $_SESSION['user']['id'];
        $where['user_id'] = $user_id;
        import('Think.Page');// 导入分页类
        $m = M('book');
        $count = $m->where($where)->count();
        $Page = new \Think\Page($count);// 实例化分页类 传入总记录数
        $Page->listRows = 2;  //每页2条
        $Page->firstRow = (I('p') ? I('p')-1:0) * $Page->listRows;
        $show = $Page->show();// 分页显示输出
        $bookList = $m->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();// $Page->firstRow 起始条数 $Page->listRows 获取多少条
        foreach($bookList as &$v){
            $v['chapter'] = M('book_chapter')->where(array('book_id'=>$v['id'],'publish'=>1))->order('id desc')->find();
        }
        $this->assign('bookList',$bookList);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    //创建作品保存
    public function addBook(){
        $config = array(
            'mimes' => array(), //允许上传的文件MiMe类型
            'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
            'exts' => array('jpg','jpeg','png','gif'), //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './Upload/', //保存根路径
            'savePath' => 'img/', //保存路径
            'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt' => '', //文件保存后缀，空则使用原后缀
            'replace' => false, //存在同名是否覆盖
            'hash' => true, //是否生成hash编码
            'callback' => false, //检测文件是否存在回调，如果存在返回文件信息数组
            'driver' => '', // 文件上传驱动
            'driverConfig' => array(), // 上传驱动配置
        );
        $up = new \Util\Upload($config);
        $result = $up->upload($_FILES);
        if($result){ //图片上传成功
            $img = $result['bookImage']['savepath'].$result['bookImage']['savename'];
            $data = array(
                'img' => $img,
                'name'=> I('post.bookName'),
                'class_id' => I('post.bookType'),
                'class_name' => M('book_class')->where(array('id'=>I('post.bookType')))->find()['name'],
                'status' => 0,
                'label' => I('post.bookLabel'),
                'intro' => I('post.bookIntro'),
                'word' => I('post.bookWord'),
                'ctime' => time(),
                'user_id' => $_SESSION['user']['id'],
                'username' => $_SESSION['user']['name']
            );
            $re = M('book')->add($data);
            if($re>0){
                ajaxReturn($re,'创建作品成功',1);
            }else{
                ajaxReturn($re,'创建作品失败',0);
            }
        }else{
            ajaxReturn('',$up->getError(),0);
        }

    }

    //作品设置保存
    public function editBook(){
        $config = array(
            'mimes' => array(), //允许上传的文件MiMe类型
            'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
            'exts' => array('jpg','jpeg','png','gif'), //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './Upload/', //保存根路径
            'savePath' => 'img/', //保存路径
            'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt' => '', //文件保存后缀，空则使用原后缀
            'replace' => false, //存在同名是否覆盖
            'hash' => true, //是否生成hash编码
            'callback' => false, //检测文件是否存在回调，如果存在返回文件信息数组
            'driver' => '', // 文件上传驱动
            'driverConfig' => array(), // 上传驱动配置
        );
       $up = new \Util\Upload($config);
       if($_FILES['bookImage']['error']==0){//需要上传图片
           //删除原来的旧图片
           $book = M('book')->where(array('id'=>I('bookId')))->find();
           deleteFile('./Upload/'.$book['img']);
           $result = $up->upload($_FILES);
           $img = $result['bookImage']['savepath'].$result['bookImage']['savename'];
           $data = array(
               'img' => $img,
               'name'=> I('post.bookName'),
               'class_id' => I('post.bookType'),
               'class_name' => M('book_class')->where(array('id'=>I('post.bookType')))->find()['name'],
               'status' => 0,
               'label' => I('post.bookLabel'),
               'intro' => I('post.bookIntro'),
               'word' => I('post.bookWord'),
               'ctime' => time(),
               'user_id' => $_SESSION['user']['id'],
               'username' => $_SESSION['user']['name']
           );
       }else{
           $data = array(
               'name'=> I('post.bookName'),
               'class_id' => I('post.bookType'),
               'class_name' => M('book_class')->where(array('id'=>I('post.bookType')))->find()['name'],
               'status' => 0,
               'label' => I('post.bookLabel'),
               'intro' => I('post.bookIntro'),
               'word' => I('post.bookWord'),
           );
       }
        $re = M('book')->where(array('id'=>I('post.bookId')))->save($data);
        if($re!==FALSE){
            ajaxReturn($re,'作品修改成功',1);
        }else{
            ajaxReturn($re,'作品修改失败',0);
        }


    }

    //作品管理-草稿箱
    public function chapter(){
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
        $this->assign('title','作品管理');

        $book_id = I('get.id');
        //草稿箱章节信息
        $draftChapterList = M('book_chapter')->where(array('book_id'=>$book_id,'trash'=>0,'publish'=>0))->order('id desc')->select();//trash未1表示加入回收站，为0表示正常
        $this->assign('draftChapterList',$draftChapterList);
        $this->assign('draftChapterCount',count($draftChapterList));//章节数

        //已发布章节信息
        $publishChapterList = M('book_chapter')->where(array('book_id'=>$book_id,'trash'=>0,'publish'=>1))->order('id desc')->select();//trash未1表示加入回收站，为0表示正常
        $this->assign('publishChapterList',$publishChapterList);
        $this->assign('publishChapterCount',count($publishChapterList));//章节数

        //回收站章节信息
        $trashChapterList = M('book_chapter')->where(array('book_id'=>$book_id,'trash'=>1))->order('id desc')->select();//trash未1表示加入回收站，为0表示正常
        $this->assign('trashChapterList',$trashChapterList);
        $this->assign('trashChapterCount',count($trashChapterList));//章节数

        //作品信息
        $book = M('book')->where(array('id'=>$book_id))->find();
        $this->assign('book',$book);

        //作品分类
        $bookClassList = M('book_class')->select();
        $this->assign('bookClassList',$bookClassList);
        $this->display();

    }


    //作品管理--ajax请求--获取对应章节内容
    public function chapterContent(){
        $chapter_id = I('post.chapter_id');
        $chapter = M('book_chapter')->where(array('id'=>$chapter_id))->find();
        if($chapter){
            ajaxReturn($chapter,'success',1);
        }else{
            ajaxReturn($chapter,'error',0);
        }
    }

    //作品管理--草稿箱--ajax请求-章节保存
    public function chapterSave(){
        $id = I('post.id');
        if($id){  //修改章节内容
            $data = I('post.');
            $data['utime'] = I('post.utime');
            $re = M('book_chapter')->where(array('id'=>$id))->save($data);
            if($re!==FALSE){
                ajaxReturn($re,'保存成功',1);
            }else{
                ajaxReturn($re,'保存失败',0);
            }
        }else{
            $data = I('post.');
            $data['book_name'] = M('book')->where(array('id'=>I('post.book_id')))->find()['name'];
            $data['book_id'] = I('post.book_id');
            $data['ctime'] = time();
            $data['trash'] = 0;
            $data['status'] = 0;
            $re = M('book_chapter')->add($data);
            if($re>0){
                ajaxReturn($re,'保存成功',1);
            }else{
                ajaxReturn($re,'保存失败',0);
            }
        }
    }

    //作品管理--草稿箱--ajax请求-章节删除(trash置为1)
    public function chapterDelete(){
        $id = I('post.id');
        $re = M('book_chapter')->where(array('id'=>$id))->save(array('trash'=>1));
        if($re!==FALSE){
            ajaxReturn($re,'删除成功，回收站内可找回',1);
        }else{
            ajaxReturn($re,'删除失败',0);
        }
    }

    //作品管理--草稿箱--ajax请求-章节发布(publish置为1)
    public function chapterPublish(){
        $id = I('post.id');
        if($id){
            $data = I('post.');
            $data['publish'] = 1;
            $data['publish_time'] = time();
            $re = M('book_chapter')->where(array('id'=>$id))->save($data);
            if($re!==FALSE){
                ajaxReturn($re,'发布成功，可在已发布章节内查看',1);
            }else{
                ajaxReturn($re,'操作失败',0);
            }
        }else{
            $data = I('post.');
            $data['book_name'] = M('book')->where(array('id'=>I('post.book_id')))->find()['name'];
            $data['publish'] = 1;
            $data['status'] = 0;
            $data['publish_time'] = time();
            $re = M('book_chapter')->add($data);
            if($re>0){
                ajaxReturn($re,'发布成功，可在已发布章节内查看',1);
            }else{
                ajaxReturn($re,'操作失败',0);
            }
        }
    }

    //作品管理--回收站--ajax请求-章节恢复至草稿(trash置为0)
    public function chapterRecover(){
        $id = I('post.id');
        $re = M('book_chapter')->where(array('id'=>$id))->save(array('trash'=>0,'publish'=>0));
        if($re!==FALSE){
            ajaxReturn($re,'章节已恢复至草稿箱',1);
        }else{
            ajaxReturn($re,'操作失败',0);
        }
    }

    //作品管理--回收站--ajax请求-章节彻底删除
    public function chapterRemove(){
        $id = I('post.id');
        $re = M('book_chapter')->where(array('id'=>$id))->delete();
        if($re!==FALSE){
            ajaxReturn($re,'操作成功',1);
        }else{
            ajaxReturn($re,'操作失败',0);
        }
    }


    public function test(){
        $cache = M('book_chapter')->select();
        foreach($cache as $v){
            $v['content'] = str_replace(array("\r\n", "\r", "\n"),"\n",$v['content']);
            M('book_chapter')->where(array('id'=>$v['id']))->save(array('content'=>$v['content']));
        }
        echo 3;
    }




}
