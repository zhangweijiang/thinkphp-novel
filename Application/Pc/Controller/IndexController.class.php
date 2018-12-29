<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 首页
 */
namespace Pc\Controller;
class IndexController extends BaseController
{
    public function index()
    {
        $icon = array('icomoon-xuanhuan','icomoon-qihuan','icomoon-xianxia','icomoon-wuxia',
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
        $this->assign('bookClassList',$bookClassList);//作品分类

        $hotList = M('book')->where(array('commend'=>1))->limit(5)->select();
        $this->assign('hotList',$hotList);//热门作品

        $collectionList = M('book')->order('collection desc')->limit(2)->select();
        $this->assign('collectionList',$collectionList);//人气最高

        $bookClass = M('book_class')->order('id asc')->limit(14)->select();
        $classId = array();
        $id = array();
        $k = 0;
        for($j=0;$j<count($bookClass);$j++){
            if($k==1){
                $id[] = $bookClass[$j]['id'];
                $classId[] = $id;
                $k=0;
                $id = array();
            }else{
                $id[] = $bookClass[$j]['id'];
                $k++;
            }
        }
        $bookList = array();
        foreach($classId as $v){
            $data = array();
            $data['book'] = M('book')->where(array('class_id'=>array('in',$v)))->field('id,name,word,class_id,class_name')->limit(5)->order('class_id asc,id desc')->select();
            foreach($v as &$vv){
                $vv = M('book_class')->where(array('id'=>$vv))->find()['name'];
            }
            $data['bookClass'] = $v;
            $bookList[] = $data;
        }
        $this->assign('bookList',$bookList);
        $this->display();
    }

    public function test(){
        var_dump(think_send_mail('982215226@qq.com','sm','love','34'));
    }


}
