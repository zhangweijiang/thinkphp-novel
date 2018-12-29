<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 收藏类
 */
namespace Admin\Controller;

class CollectionController extends BaseController
{

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
    }


    public function collectionList()
    {
        

        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '收藏列表',
                'url' => U('Admin/Collection/collectionList')
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('collection');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $map = array();
        $name = I('name');//书籍名称
        if ($name) {
            $map['b.name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $psize = C('PAGE') ? C('PAGE') : 10;
        $cache = $m->table(C('DB_PREFIX').'collection c')
            ->join(C('DB_PREFIX').'book b on c.book_id=b.id ','inner')->field('c.id,c.user_name,c.ctime,b.name,b.img,b.username')
            ->where($map)->page($p, $psize)->select();
        $count = $m->table(C('DB_PREFIX').'collection c')
            ->join(C('DB_PREFIX').'book b on c.book_id=b.id ','inner')->field('c.id,c.user_name,c.ctime,b.name,b.img,b.username')
            ->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '收藏列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }





}