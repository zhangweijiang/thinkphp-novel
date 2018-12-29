<?php
/**
 * User: hxb
 * Date: 2017-10-05
 * Time: 19:45
 * Class: 登录控制器类
 */
namespace Pc\Controller;
class LoginController extends BaseController
{
    public function index()
    {
        if(IS_POST){   //登录页面信息提交
            $data = I('post.');
            $verify = new \Think\Verify();
            if (!$verify->check($data['verify'])) {
                $this->error('请正确填写验证码！');
            }
            $where = array(
              'name' => $data['name'],
            );
            $user = M('user')->where($where)->find();
            if($user){ //用户存在
                if($user['password']==md5(I('post.password'))){
                    if($user['status']==1){  //0表示禁用,表示启用
                        session('user',$user);
                        if($_SESSION['url']){
                            header('location:'.$_SESSION['url']);
                        }else{
                            $this->redirect('Pc/Index/index');//跳转到首页
                        }
                    }else{
                        $this->error('该用户已被禁用，请联系平台！');
                    }
                }else{
                    $this->error('您输入的密码不正确，请重新输入！');
                }
            }else{ //用户不存在
                $this->error('用户不存在！');
            }
        }else{   //登录页面
            $this->display();
        }
    }

    /**
     * 通用验证码
     */
    public function verify()
    {
        $Verify = new \Think\Verify();
        $Verify->codeSet = '0123456789';
        $Verify->length = 4;
        $Verify->imageH = 0;
        $Verify->entry();
    }

    /**
     * 用户退出
     */
    public function logout(){
        session('user',null);//清空session
        $this->redirect('Pc/Login/index');
    }

    /**
     * 忘记密码邮箱发送密码
     */
    public function sendPassword(){
        if (IS_POST) {
            $result = M('user')->where(array('email'=>I('post.email')))->find();
            if ($result) {
                $code = generate_password(8);
                $to = $_POST["email"];
                $subject = "小数管理系统【忘记密码】邮件";
                $body = "您好，我们已对您在小说管理系统网站上对应邮箱帐号的密码进行重置，重置的密码为：" . $code . "。请及时登录修改密码！";
                if (think_send_mail($to,'黄晓滨', $subject, $body)) {
                    $res = M('user')->where(array('name'=>$result['name']))->save(array('password'=>md5($code)));
                    if ($res) {
                        $msg = "重置密码成功！";
                        ajaxReturn('',$msg,1);
                    } else {
                        $msg = "重置密码失败！";
                        ajaxReturn('',$msg,0);
                    }

                } else {
                    $msg = "邮件发送失败！";
                    ajaxReturn('',$msg,0);
                }

            } else {
                $msg = "该邮箱帐号不存在，请重新输入！";
                ajaxReturn('',$msg,0);
            }
        }
    }

}
