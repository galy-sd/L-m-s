<?php
namespace app\Functionext;
use think\facade\Db;

class Tjlb{

    public function query_all(){        
        $datas = Db::query("select bm,mc from tjlb");
        if ($datas == null){
            return null;
        }
        else{ 
            $data = $datas[0];
            return $datas;           
        } 
    }
    
}