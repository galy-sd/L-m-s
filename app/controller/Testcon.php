<?php
namespace app\controller;
use think\Request;
use app\Functionext\App_ext_function;

class Testcon
{
    protected $request;
    public function __construct(Request $request){
		  $this->request = $request;
    }
//函数测试视图
    public function test(){
      //   return view('test',['name'=>session('login_name')]);  
      return view('test');
    }
//地图测试视图
    public function map_test(){
        return view('test_map');
    }
//获取操作人员信息,操作员编号_密码_设备ID_地标(json)参数,参数名称:bh,pwd,device_id,db_json
    public function get_czy_info(){
        $bh = $this->request->param('bh');
        $pwd = $this->request->param('pwd');
        $device_id = $this->request->param('device_id');
        $db_json = $this->request->param('db_json');
        $check = new App_ext_function(); 
        $res = $check->check_zycz($bh,$pwd,$device_id,$db_json);
        if ($res == null) {
            return null;
        }
        else{
            header('Content-Type:application/json; charset=utf-8');
            return $res;
        }        
    }
//获取患者信息,二码参数,参数名称:patient_id
    public function get_patient_info(){
        $patient_id = $this->request->param('patient_id');
        $token = $this->request->param('token');
        $check = new App_ext_function();
        $res = $check->get_patient_info($patient_id,$token);
        header('Content-Type:application/json; charset=utf-8');
        return $res;
    }
//设置初始患者地址坐标,二码_地标(json)参数,参数名称:patient_id,position_json([x:value,y:value,dt:value])
    public function set_patient_init(){
        $patient_id = $this->request->param('patient_id');
        $position_json = $this->request->param('db_json');
        $token = $this->request->param('token');
        $check = new App_ext_function();
        $res = $check->set_patient_init($patient_id,$position_json,$token);
        header('Content-Type:application/json; charset=utf-8');
        return $res;
    }
//开始一个新任务,二码_工号_地标(json)参数,参数名称:patient_id,db_json([x:value,y:value,dt:value])
    public function start_new_job(){
        $patient_id = $this->request->param('patient_id');        
        $db_json = $this->request->param('db_json');
        $token = $this->request->param('token');
        $check = new App_ext_function();
        $res=$check->start_job($patient_id,$db_json,$token);
        header('Content-Type:application/json; charset=utf-8');
        return $res;
    }
//结束已经开始的任务
    public function end_job(){
        $patient_id = $this->request->param('patient_id');        
        $db_json = $this->request->param('db_json');
        $token = $this->request->param('token');
        $check = new App_ext_function();
        $res=$check->end_job($patient_id,$db_json,$token);
        header('Content-Type:application/json; charset=utf-8');
        return $res;
    }
//发送当前位置
    public function send_db(){
        $job_id = $this->request->param('job_id');        
        $db_json = $this->request->param('db_json');
        $token = $this->request->param('token');
        $db_time = $this->request->param('db_time');
        $db_type = $this->request->param('db_type');
        $send_type = $this->request->param('send_type');
        $check = new App_ext_function();
        $res=$check->send_db($job_id,$db_json,$token,$db_time,$db_type,$send_type);
        header('Content-Type:application/json; charset=utf-8');
        return $res;
    }
//测试数据库连接
    public function test_con(){
        $check = new App_ext_function();
        $res=$check->test_con();
        dump($res);
    }
//获取10个患者的信息
    public function get_patient(){
        $token = $this->request->param('token');
        $check = new App_ext_function();
        $res=$check->get_patient($token);
        dump($res);
    }
//获取10个患者的信息并以gridView返回
    public function get_patient_view(){
        $token = $this->request->param('token');
        $check = new App_ext_function();
        $res=$check->get_patient($token);
        return view('gridView',[
            'data'=>$res]);        
    }
//获取所有开始状态的任务
    public function get_start_job(){        
        $check = new App_ext_function();
        $res=$check->get_start_job();
        dump($res);
        // return view('gridView',[
        //     'data'=>$res]);        
    }
//根据JOB获取护理院最后一次上传的位置
    public function  get_job_czy_db(){
        $job_id = $this->request->param('job_id');        
        $check = new App_ext_function();
        $res=$check->get_job_czy_db($job_id);
        dump($res); 
    }
//获取开始状态的任务的全部信息
    public function get_job_all_info(){
        $check = new App_ext_function();
        $res=$check->get_job_all_db();
        header('Content-Type:application/json; charset=utf-8');
        return($res);
    }
}