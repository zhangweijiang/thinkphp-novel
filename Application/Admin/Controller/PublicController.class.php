<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 公共类
 */
namespace Admin\Controller;

class PublicController extends BaseController
{

    //默认跳转至登陆页面
    public function index()
    {
        $this->redirect('Admin/Public/login');
    }

    //通用注册页面
    public function reg()
    {
        $this->display();
    }

    //通用登陆页面
    public function login()
    {
        if (IS_POST) {
            $data = I('post.');

            $verify = new \Think\Verify();
            if (!$verify->check($data['verify'])) {
                $this->error('请正确填写验证码！');
            }
            $admin = M('admin')->where(array('username' => $data['username'], 'userpass' => md5($data['userpass'])))->find();
            if ($admin) {
                self::$CMS['uid'] = $_SESSION['CMS']['uid'] = $admin['id'];
                self::$CMS['user'] = $_SESSION['CMS']['user'] = $admin;
                self::$CMS['homeurl'] = $_SESSION['CMS']['homeurl'] = U('Admin/Index/index');
                self::$CMS['backurl'] = $_SESSION['CMS']['backurl'] = FALSE;
                $this->redirect('Admin/Index/index');
            } else {
                $this->error('用户不存在，或密码错误！');
            }
        }
        if ($_SESSION['CMS']['uid']) {
            $this->redirect('Admin/Index/index');
        }
        $arr = array(4, 5, 7, 7, 7, 10, 11, 12);
        $get = $arr[mt_rand(0, count($arr) - 1)];
        $wallpaper = ROOT_URL . "Statics/Admin/img/WallPage/" . $get . ".jpg";
        $this->assign('wallpaper', $wallpaper);
        $this->display();
    }

    public function logout()
    {
        session(null);
        $this->redirect('Admin/Public/login');
    }

    //通用验证码
    public function verify()
    {
        $Verify = new \Think\Verify();
        $Verify->codeSet = '0123456789';
        $Verify->length = 4;
        $Verify->imageH = 0;
        $Verify->entry();
    }






}