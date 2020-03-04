<?php
namespace app\controller;
use think\Request;
use app\Functionext\Verify;


class Extcontorller
{
    protected $request;
    public function __construct(Request $request)
    {
		  $this->request = $request;
    }
    //登陆认证
    public function login_verify(){
       $check = new Verify();     
       $ysbh = $this->request->param('ysbh');
       $pwd = $this->request->param('pwd');
       $str_re = $check->verify($ysbh,$pwd);
       if ($str_re == 'Success'){                     
            $user_name = session('login_name');  
            //调转到原来的页面
            return redirect('/')->restore();             
       }
       //登陆失败
       return redirect('/login_err/01')->remember();
    }       
}
