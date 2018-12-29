<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 列表页
 */
namespace Pc\Controller;
class ListController extends BaseController
{
    public function index()
    {
        $icon = array('icomoon-xuanhuan','icomoon-qihuan','icomoon-wuxia','icomoon-xianxia',
                       'icomoon-dushi','icomoon-xianshi','icomoon-junshi','icomoon-lishi',
                       'icomoon-youxi','icomoon-tiyu','icomoon-kehuan','icomoon-lingyi',
                       'icomoon-nvsheng','icomoon-erciyuan');
        $bookClassList = M('book_class')->where(array('type'=>1))->limit(14)->order('id asc')->select();
        for($i=0;$i<count($bookClassList);$i++){
            $bookClassList[$i]['icon'] = $icon[$i];
            $count = M('book')->where(array('class_id'=>$bookClassList[$i]['id']))->count();
            if($count==0){
                $count = null;
            }
            $bookClassList[$i]['count'] = $count;
        }
        $this->assign('bookClassList',$bookClassList);//作品分类-男生

        //男生分类
        $maleClassList = M('book_class')->where(array('type'=>1))->order('id asc')->select();
        $this->assign('maleClassList',$maleClassList);

        //女生分类
        $femaleClassList = M('book_class')->where(array('type'=>2))->order('id asc')->select();
        $this->assign('femaleClassList',$femaleClassList);

        //作品列表
        $where = array();
        if(empty(I('get.type'))){
            $_GET['type'] = 1;
        }
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
        $Page->listRows = 10;  //每页10条
        $Page->firstRow = (I('p') ? I('p')-1:0) * $Page->listRows;
        $show = $Page->show();// 分页显示输出
        $bookList = $m->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();// $Page->firstRow 起始条数 $Page->listRows 获取多少条
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('bookList',$bookList);


        $this->display();
    }


}
