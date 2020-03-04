<?php
namespace app\Functionext;
use think\facade\Db;

class Verify{

    public function verify($ysbh='',$pwd=''){
        session('login_name',null);
        session('token_key', null);
        $users = Db::query("select top 1 ysxm,getdate() as nowdt from tjys where ysbh =? and password=?",[$ysbh,$pwd]);
        if ($users == null){
            return "Fail";
        }
        else{ 
            $user = $users[0];
            $nowdt = $ysbh.$user['nowdt'];
            $tokenkey = md5('@#*CDFd^^%#Tsdfe'.$user['ysxm'].$nowdt);
            $re=Db::execute("update tjys set LastloginDt=getdate() ,TokenKey=? where ysbh=? and password =? ",[$tokenkey,$ysbh,$pwd]);
            if($re > 0){
                session('login_name', $user['ysxm']);
                session('token_key', $tokenkey);                           
                return "Success";           
            }
            else{
                return "Fail";
            }            
        } 
    }
    
}

