<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 书籍类
 */
namespace Admin\Controller;

class BookController extends BaseController
{

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
    }

    //书籍列表
    public function bookList()
    {
        

        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '书籍列表',
                'url' => U('Admin/Book/bookList')
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('book');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $map = array();
        $name = I('name');
        $class_id = I('class_id');
        $on_sale = I('on_sale');
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        if($class_id){
            $map['class_id'] = $class_id;
            $this->assign('class_id', $class_id);
        }
        if($on_sale!==""){
            $map['on_sale'] = $on_sale;
            $this->assign('on_sale',$on_sale);
        }
        $psize = C('PAGE') ? C('PAGE') : 10;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '书籍列表', 'App-search');
        $this->assign('cache', $cache);
        //获取书籍分类
        $bookClassList = M('book_class')->select();
        $this->assign('bookClassList',$bookClassList);
        $this->display();
    }

    //删除书籍
   /* public function bookDel()
    {
        $id = $_GET['id'];//必须使用get方法
        $m = M('book');
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
    }*/

    //书籍分类列表
    public function bookClassList()
    {


        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '分类列表',
                'url' => U('Admin/Book/bookClassList')
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('book_class');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $map = array();
        $name = I('name');
        $type = I('type');
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        if ($type) {
            $map['type'] = $type;
            $this->assign('type', $type);
        }
        $psize = C('PAGE') ? C('PAGE') : 10;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '分类列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //删除分类书籍
    public function bookClassDel()
    {
        $id = $_GET['id'];//必须使用get方法
        $m = M('book_class');
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

    public function bookClassSet()
    {
        $id = I('id');
        $m = M('book_class');
        //dump($m);
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '分类列表',
                'url' => U('Admin/Book/bookClassList')
            ),
            '1' => array(
                'name' => '设置分类',
                'url' => U('Admin/book/bookClassSet', array('id' => $id))
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

    //章节列表
    public function bookChapterList()
    {

        Load('extend');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '章节列表',
                'url' => U('Admin/Book/bookChapterList')
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('book_chapter');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $map = array();
        $title = I('title');
        $status = I('status')?I('status'):0;
        $book_name =I('book_name');
        if ($title) {
            $map['title'] = array('like', "%$title%");
            $this->assign('title', $title);
        }
        if($_GET['book_id']){
            $map['book_id'] = $_GET['book_id'];
        }
        if($_GET['book_name']){
            $map['book_name'] = array('like', "%$book_name%");
            $this->assign('book_name', $book_name);
        }
        $map['status'] =  $status;
        $this->assign('status',$status);
        $map['trash'] = 0;//章节没有加入回收站
        $psize = C('PAGE') ? C('PAGE') : 10;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '章节列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //删除章节
    public function bookChapterDel()
    {
        $id = $_GET['id'];//必须使用get方法
        $m = M('book_chapter');
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

    //修改章节状态(通过或不通过)
    public function updateBookChapterStatus(){
        $id = I('post.id');
        $status = I('post.status');
        $reason = I('post.reason');

        //status的值1表示通过，2表示不通过
        if($status==1){
            $re = M('book_chapter')->where(array('id'=>$id))->save(array('status'=>$status,'update_time'=>time()));
        }else if($status==2){
            $re = M('book_chapter')->where(array('id'=>$id))->save(array('status'=>$status,'reason'=>$reason));
        }
        if ($re) {
            $info['status'] = 1;
            $info['msg'] = '修改成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '修改失败!';
        }
        $this->ajaxReturn($info);
    }

    //上下架
    public function updateBookOnSale(){
        $id = I('post.id');
        $on_sale = I('post.on_sale');
        $data = array();
        $data['on_sale'] = $on_sale;
        if($on_sale==1){
            $book = M('book')->where(array('id'=>$id))->find();
            if(empty($book['on_sale_time'])){ //该书上架时间
                $data['on_sale_time'] = time();
            }
        }
        $re = M('book')->where(array('id'=>$id))->save($data);
        if($re!==FALSE){
            $info['status'] = 1;
            $info['msg'] = '修改成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '修改失败!';
        }
        $this->ajaxReturn($info);
    }


}