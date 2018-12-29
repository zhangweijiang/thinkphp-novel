<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 用户后台基础类
 */
namespace Admin\Controller;

use Think\Controller;

class BaseController extends Controller
{
    protected static $CMS; //CMS全局静态变量

    //初始化验证模块
    protected function _initialize()
    {

        //检测登陆状态
        $check = $this->checkLogin();
    }

    //检查用户是否登陆,返回TRUE或跳转登陆
    public function checkLogin()
    {
        $passlist = array('login', 'logout', 'reg', 'verify'); //不检测登陆状态的操作
        $check = in_array(ACTION_NAME, $passlist);
        if (!$check) {
            if (!isset($_SESSION['CMS']['uid'])) {
                $this->redirect('Admin/Public/login');
            }
        } else {
            return TRUE;
        }
    }

    //拼装面包导航
    public function getBread($bread)
    {
        if ($bread) {
            $this->assign('bread', $bread);
            return $this->fetch('Base_bread');
        } else {
            $this->error('请传入面包导航！');
        }
    }

    //封装分页类
    public function getPage($count, $psize, $loader, $loadername, $searchname, $map)
    {
        if (!$count && !$psize || !$loader || !$loadername) {
            die('缺少分页参数!');
        }
        $page = new \Util\Pagecms($count, $psize); // 实例化分页类 传入总记录数和每页显示的记录数
        $page->setConfig('loader', $loader);
        $page->setConfig('loadername', $loadername);
        //绑定前端form搜索表单ID,默认为#App-search
        if ($searchname) {
            $page->setConfig('searchname', $searchname);
        }
        if ($map) {
            foreach ($map as $key => $val) {
                $page->parameter[$key] = urlencode($val);
            }
        }
        $show = $page->show(); // 分页显示输出
        $this->assign('page', $show);
        return true;
    }

    //获取单张图片
    public function getPic($id)
    {
        $m = M('Upload_img');
        $map['id'] = $id;
        $list = $m->where($map)->find();
        if ($list) {
            $list['imgurl'] = __ROOT__ . "/Upload/" . $list['savepath'] . $list['savename'];
        }
        return $list ? $list : "";
    }

    //获取图集合
    public function getAlbum($ids)
    {
        $m = M('Upload_img');
        $map['id'] = array('in', in_parse_str($ids));
        $list = $m->where($map)->select();
        foreach ($list as $k => $v) {
            $list[$k]['imgurl'] = "/upload/" . $list[$k]['savepath'] . $list[$k]['savename'];
        }
        return $list ? $list : "";
    }


}