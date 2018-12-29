<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 搜索页
 */
namespace Pc\Controller;
class SearchController extends BaseController
{
    public function index()
    {
        //导航栏分类
        $bookClassList1 = M('book_class')->where(array('type'=>1))->limit(7)->order('id asc')->select();
        $bookClassList2 = M('book_class')->where(array('type'=>1))->limit(8,14)->order('id asc')->select();
        $this->assign('bookClassList1',$bookClassList1);
        $this->assign('bookClassList2',$bookClassList2);

        //标题
        $this->assign('title','小说搜索页');

        $where = array();
        if(empty(I('get.type'))){
            $_GET['type'] = 1;
        }
        //男生-女生分类
        $sexClassList = M('book_class')->where(array('type'=>I('type')))->order('id asc')->select();
        foreach($sexClassList as &$sex){
            $sex['count'] = M('book')->where(array('class_id'=>$sex['id']))->count();
        }
        $this->assign('sexClassList',$sexClassList);

        if(I('class_id')){
            $where['class_id'] = I('class_id');
        }else{
            $bookClass = M('book_class')->where(array('type'=>$_GET['type']))->select();
            $class_id = array();
            foreach($bookClass as $bc){
                $class_id[] = $bc['id'];
            }
            $where['class_id'] = array('in',$class_id);
        }
        if(I('name')){
            $where['name'] = array('like','%'.I('name').'%');
        }
        if(I('status')){
            $where['status'] = I('status');
        }
        if(I('count')){
            if(I('count')==1){
                $where['count'] = array('lt','300000');
            }else if(I('count')==2){
                $where['count'] = array('between','300000,500000');
            }else if(I('count')==3){
                $where['count'] = array('between','500000,1000000');
            }else if(I('count')==4){
                $where['count'] = array('between','1000000,2000000');
            }else if(I('count')==5){
                $where['count'] = array('gt','2000000');
            }
        }
        import('Think.Page');// 导入分页类
        $m = M('book');
        $count = $m->where($where)->count();
        $Page = new \Think\Page($count);// 实例化分页类 传入总记录数
        $Page->listRows = 3;  //每页3条
        $Page->firstRow = (I('p') ? I('p')-1:0) * $Page->listRows;
        $show = $Page->show();// 分页显示输出
        $bookList = $m->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();// $Page->firstRow 起始条数 $Page->listRows 获取多少条
        foreach($bookList as &$v){
            $v['bookChapter'] = M('book_chapter')->where(array('book_id'=>$v['id'],'status'=>1))->order('update_time desc')->find();
            $v['is_collection'] = M('collection')->where(array('user_id'=>$_SESSION['user']['id'],'book_id'=>$v['id']))->find();
            if($v['count']>10000){
                $v['count'] = round($v['count']/10000,2).'万';
            }
        }
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('bookList',$bookList);

        //搜这本书的还看
        $where = array();
        if($bookList){
            $where['class_id'] = $bookList[0]['class_id'];
        }
        $searchBookList = M('book')->where($where)->order('collection desc,id desc')->limit(6)->select();//取出与该书籍相同分类并且收藏人数最多的前6条数据
        $this->assign('searchBookList',$searchBookList);

        //状态
        $bookStatus = array(
            array(
                'id' =>1,
                'count'=> M('book')->where(array('status'=>1))->count(),
                'name' => '连载'
            ),
            array(
                'id' =>2,
                'count'=> M('book')->where(array('status'=>2))->count(),
                'name' => '完本'
            ),
        );
        $this->assign('bookStatus',$bookStatus);

        //字数
        $bookCount = array(
            array(
                'id' =>1,
                'count'=> M('book')->where(array('count'=>array('lt',300000)))->count(),
                'name' => '30万字以下'
            ),
            array(
                'id' =>2,
                'count'=> M('book')->where(array('count'=>array('between','300000,500000')))->count(),
                'name' => '30-50万字'
            ),
            array(
                'id' =>3,
                'count'=> M('book')->where(array('count'=>array('between','500000,1000000')))->count(),
                'name' => '50-100万字'
            ),
            array(
                'id' =>4,
                'count'=> M('book')->where(array('count'=>array('between','1000000,2000000')))->count(),
                'name' => '100-200万字'
            ),
            array(
                'id' =>5,
                'count'=> M('book')->where(array('count'=>array('gt',2000000)))->count(),
                'name' => '200万字以上'
            )
        );
        $this->assign('bookCount',$bookCount);
        $this->display();
    }


}
