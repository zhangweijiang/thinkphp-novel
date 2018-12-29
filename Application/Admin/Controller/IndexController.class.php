<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 首页-管理员类
 */
namespace Admin\Controller;

class IndexController extends BaseController
{

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
    }

    //后台框架入口
    public function index()
    {
        //获取管理员基本信息
        $admin = $_SESSION['CMS']['user'];
        //获取用户头像
        $headimg = M('upload_img')->where(array('id'=>$admin['headimg']))->find();
        $admin['headimg'] = $headimg['savepath'].$headimg['savename'];
        $this->assign('admin',$admin);
        //获取未处理留言数--status的值为1表示未处理，为2表示已处理
        $messageCount = M('message')->where(array('status'=>1))->count();
        $this->assign('messageCount',$messageCount);
        //获取未处理章节数
        $chapterCount = M('book_chapter')->where(array('status'=>0))->count();
        $this->assign('chapterCount',$chapterCount);
        $this->display();
    }

    //系统管理员
    public function adminList()
    {

        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '管理员列表',
                'url' => U('Admin/Index/adminList')
            )
        );

        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('admin');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $username = I('username');
        if ($username) {
            $map['username'] = array('like', "%$username%");
            $this->assign('name', $username);
        }
        if(I('id')){
            $map['id'] = I('id');
        }
        $psize = C('PAGE') ? C('PAGE') : 10;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '管理员列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //管理员列表
    public function adminSet()
    {
        $id = I('id');
        $m = M('admin');
        //dump($m);
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '管理员中心',
                'url' => U('Admin/Index/adminList')
            ),
            '1' => array(
                'name' => '管理员编辑',
                'url' => U('Admin/User/userSet', array('id' => $id))
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            //die('aa');
            $data = I('post.');
            // print_r($data);die;
            if ($id) {
                if ($data['userpass']) {
                    $data['userpass'] = md5($data['userpass']);
                } else {
                    unset($data['userpass']);
                }
                $re = $m->save($data);
                if (FALSE !== $re) {
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！'.$data['iskj'];
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            } else {
                $data['userpass'] = md5($data['userpass']);
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

    public function adminDel()
    {
        $id = $_GET['id'];//必须使用get方法
        $m = M('admin');
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

    //后台图片浏览器
    public function appImgviewer()
    {
        $ids = I('ids');
        //dump($ids);
        $m = M('UploadImg');
        $map['id'] = array('in', in_parse_str($ids));
        $cache = $m->where($map)->select();
        $this->assign('cache', $cache);
        $this->ajaxReturn($this->fetch());
    }

    //ajax请求获取未处理留言和未审核章节
    public function timeout(){
        //获取未处理留言数--status的值为1表示未处理，为2表示已处理
        $messageCount = M('message')->where(array('status'=>1))->count();
        //获取未处理章节数
        $chapterCount = M('book_chapter')->where(array('status'=>0))->count();
        $info['messageCount'] = $messageCount;
        $info['chapterCount'] = $chapterCount;
        $this->ajaxReturn($info);

    }

    //首页
    public function main(){
        $lang = L();
        $mysql = M('admin')->query("select VERSION() as version");
        $mysql = $mysql[0]['version'];
        $mysql = empty($mysql) ? lang('UNKNOWN') : $mysql;
        $info = [
            $lang['OPERATING_SYSTEM']      => PHP_OS,
            $lang['OPERATING_ENVIRONMENT'] => $_SERVER["SERVER_SOFTWARE"],
            $lang['PHP_RUN_MODE']          => php_sapi_name(),
            $lang['PHP_VERSION']           => phpversion(),
            $lang['MYSQL_VERSION']         => $mysql,
            'ThinkPHP'                    => THINK_VERSION,
            $lang['UPLOAD_MAX_FILESIZE']   => ini_get('upload_max_filesize'),
            $lang['MAX_EXECUTION_TIME']    => ini_get('max_execution_time') . "s",
            //TODO 增加更多信息
            $lang['DISK_FREE_SPACE']       => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
        ];
        $this->assign('info',$info);

        $info1 = [
            '开发者'      => '张伟江',
            '邮箱'        => '982215226@qq.com',
            '电话'        => '13665994204',
        ];
        $this->assign('info1',$info1);
        $this->display();
    }



}