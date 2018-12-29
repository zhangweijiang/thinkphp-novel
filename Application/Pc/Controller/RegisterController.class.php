<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 注册控制器类
 */
namespace Pc\Controller;
class RegisterController extends BaseController
{
    public function index()
    {
        if(IS_POST){   //注册页面信息提交
            if ($this->isLogin() !== false) {//已登录-直接跳转到网站主页面
                $this->redirect('Pc/index/index');
            }
            $data = I('post.');
            $user = M('user')->where(array('name'=>I('post.name')))->find();
            if($user){
                $this->error('该用户已被注册，请重新申请');
            }
            $insert = array(
                'name' => $data['name'],
                'password' => md5($data['password'])
            );
            $result = M('user')->add($insert);
            if($result>0){ //$result为添加成功后返回的主键id，大于0表示添加成功
                header('content-type:text/html;charset=utf-8');
                echo "<script>
                            alert('恭喜您注册成功，请登录');
                            window.location.href = '/Pc/Login/index';
                      </script>";
            }
        }else{   //注册页面
            $this->display();
        }
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
