<?php
namespace app\controller;
use app\BaseController;

class Pagecontorller extends BaseController
{
    //检查登陆状态,中间件注册,测试时暂时屏蔽
    protected $middleware = ['check'];

    //主页
    // public function index(){              
    //     return view('index',['name'=>session('login_name')]);       
    // }  
    
    // public function Test(){
    //     return session('login_name').'-----'.session('token_key');
    // }
    
    // //主页的左边栏
    // public function index_left(){        
    //     return view('index_left',['name'=>session('login_name')]); 
    // }

    // //paln_age
    // public function plan_page(){        
    //     return view('plan_page'); 
    // }
}
