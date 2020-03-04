<?php
//declare (strict_types = 1);

namespace app\middleware;

class Check
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next){
        //后置行为中间件,先完成登陆验证
        $response = $next($request);
        //判断session[login_name,是否存在],不存在跳转到login页面.                 
        if((session('login_name') == '' or session('token_key') == '' )) {
            // return  redirect('/login')->remember();     
            return   $response; 
        }   
        else{
            //
            return   $response;
        }    
       
    }
}
