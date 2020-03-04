<?php
namespace app\Functionext;
use think\facade\Db;
use app\Functionext\Return_result;
use app\Functionext\Position;
use app\Functionext\Patient_info;

class App_ext_function{  
    //登录方法
    public function check_zycz($bh,$pwd,$device_id,$db_json){
        $return_res = new Return_result();
        $pwd = md5($pwd);
        //$datas = Db::query("select a.czid,a.xm, substring(sys.fn_sqlvarbasetostr(HashBytes('MD5','JNYtshlY'+convert(varchar(200),getdate(),109) +b.gh+a.xm+?)),3,32) as token from jnhisweb.dbo.zycz a,t_zycz_info b  where b.gh =? and b.mm = ?  and a.gh =b.gh",[$device_id,$bh,$pwd]);
        $datas = Db::query("select b.czid,b.xm, md5(CONCAT('JNYtshlY',convert(current_timestamp(3),char),?)) as token from t_zycz_info b where b.gh=?  and b.mm=?",[$device_id,$bh,$pwd]);
        if ($datas == null){
            $return_res->status = 'Err';
            $return_res->data_result='用户名或密码错误!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        else{ 
            $data = $datas[0];          
            $db=json_decode($db_json,true);         
            if(isset($db['x']) == false or isset($db['y']) == false ){
                $p_x=0;
                $p_y=0;
            }else{
                $p_x=$db['x'];
                $p_y=$db['y'];                
            }            
            //获取token令牌,记录日志
            Db::startTrans();
            try {
                    //Db::execute("update T_Sign_trace set Status_mark =1,Sign_out_datetime=getdate() where gh=? and Status_mark=0",[$bh]);
                    Db::execute("update T_Sign_trace set Status_mark =1,Sign_out_datetime=sysdate() where gh=? and Status_mark=0",[$bh]);                    
                    Db::execute("insert into T_Sign_trace(gh,Token_id,device_id,position_x,position_y) values(?,?,?,?,?)",[$bh,$data['token'],$device_id,$p_x,$p_y]);
                    // 提交事务
                    Db::commit();
                    $return_res->status = 'Ok';
                    $return_res->data_result=$data;
            } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    $return_res->status = 'Err';
                    $return_res->data_result='记录登录日志错误!';
            }
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);          
        } 
    }
    //验证token方法
    function check_token($token){
        $datas = Db::query("select 1 as rs,gh as gh from T_sign_Trace where token_id=? and status_mark = 0",[$token]);
        if($datas == null){
            return null;
        }else{
            $data = $datas[0];
            return $data;
        }
    }
    //获取患者信息方法
    public function get_patient_info($patient_id,$token){
        $return_res = new Return_result();
        //验证token是否有效
        if($this->check_token($token) == null){
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        $position= new Position();
        $patient_info = new Patient_info();        
        //获取患者信息
       // $datas = Db::query("select p_name as xm,p_gender as xb,position_x as x,position_y as y,getdate() as dt from T_pif where patient_id =? and exist_sign=0",[$patient_id]);
        $datas = Db::query("select p_name as xm,p_gender as xb,position_x as x,position_y as y,sysdate() as dt from T_pif where patient_id =? and exist_sign=0",[$patient_id]);
        if ($datas == null){
            $return_res->status = 'Err';
            $return_res->data_result='患者ID错误,没有查询到结果!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }else{
            $data = $datas[0];
            if($data['x'] ==null or $data['y'] ==null ){
                $position = null;
            }else{
                $position->position_x=$data['x'];
                $position->position_y=$data['y'];
                $position->now_datetime=$data['dt'];
            }
            $patient_info->xm = $data['xm'];
            $patient_info->xb = $data['xb'];
            $patient_info->position = $position;

            $return_res->status = 'Ok';
            $return_res->data_result=$patient_info;
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
    }
    //初始化患者位置方法
    public function set_patient_init($patient_id,$position_json,$token){
        $return_res = new Return_result();
        //验证token是否有效
        $token_res = $this->check_token($token);
        if($token_res == null ){
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        if(isset($token_res['gh'])){
            $gh = $token_res['gh'];
        }else{
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }       
        //更新患者地址坐标
        dump($position_json);
        $db=json_decode($position_json,true);
        dump($db);
        if(isset($db['x']) == false or isset($db['y']) == false ){
            $return_res->status = 'Err';
            $return_res->data_result='记录地址坐标错误!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }else{
            $p_x=$db['x'];
            $p_y=$db['y'];
        }
        Db::startTrans();
        try {
                //Db::execute("update T_PIF set position_x=?,position_y=?,init_gh=?,init_token=?,init_dt=getdate() where patient_id=?",[$p_x,$p_y,$gh,$token,$patient_id]);
                Db::execute("update T_PIF set position_x=?,position_y=?,init_gh=?,init_token=?,init_dt=sysdate() where patient_id=?",[$p_x,$p_y,$gh,$token,$patient_id]);
                // 提交事务
                Db::commit();
                $return_res->status = 'Ok';
                $return_res->data_result='更新地址坐标成功!';
        } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $return_res->status = 'Err';
                $return_res->data_result='保存记录地址坐标错误!';
        }
        return json_encode($return_res,JSON_UNESCAPED_UNICODE);
    }
    //开始任务方法
    public function start_job($patient_id,$db_json,$token){
        $return_res = new Return_result();
        //验证token是否有效
        $token_res = $this->check_token($token);
        if($token_res == null ){
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        if(isset($token_res['gh'])){
            $gh = $token_res['gh'];
        }else{
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        //地标下,y
        $db=json_decode($db_json,true);         
        if(isset($db['x']) == false or isset($db['y']) == false ){
            $p_x=0;
            $p_y=0;
        }else{
            $p_x=$db['x'];
            $p_y=$db['y'];                
        }
        //1判断患者信息是否存在
        $datas=Db::query('select count(1) as rs from t_pif where patient_id =? and exist_sign=0',[$patient_id]);
        if($datas[0]['rs'] <=0){
            $return_res->status = 'Err';
            $return_res->data_result='患者信息不存在!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        //2判断是否有未开始的计划任务
        //$datas=Db::query('select convert(varchar(23),plan_date,121) as p_dt, zycz_gh as gh, patient_id as p_id from t_work_plan  where patient_id =? and complete_sign = 0 ',[$patient_id]);
        $datas=Db::query('select convert(plan_date,char) as p_dt, zycz_gh as gh, patient_id as p_id from t_work_plan  where patient_id =? and complete_sign = 0 ',[$patient_id]);
        if($datas <> null ){
            $data = $datas[0];
            //有未开始的任务,更改任务状态为开始,返回开始的任务
            Db::startTrans();
            try {
                //Db::execute('update t_work_plan set complete_sign = 1,begin_datetime=getdate(),begin_position_x=?,begin_position_y=? where plan_date =? and zycz_gh =? and patient_id =?',[$p_x,$p_y,$data['p_dt'],$data['gh'],$data['p_id']]);
                Db::execute('update t_work_plan set complete_sign = 1,begin_datetime=sysdate(),begin_position_x=?,begin_position_y=? where plan_date =? and zycz_gh =? and patient_id =?',[$p_x,$p_y,$data['p_dt'],$data['gh'],$data['p_id']]);
                Db::commit();
            }catch(\Exception $e){
                Db::rollback();
                $return_res->status = 'Err';
                $return_res->data_result='更新任务标识失败!';
                return json_encode($return_res,JSON_UNESCAPED_UNICODE);
            }
            $return_res->status = 'Ok';
            $return_res->data_result = $data['p_dt'].$data['gh'].$data['p_id'];
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }else{
            //没有未开始的认任务,建立新任务
            $create_re = $this->create_plan($patient_id,$gh,$p_x,$p_y,1);
            if(stripos($create_re,'Err',1) > 0 ){
                $return_res->status = 'Err';
                $return_res->data_result='建立任务失败!'.$create_re;
                return json_encode($return_res,JSON_UNESCAPED_UNICODE);
            }else{
                Db::startTrans();
                try{
                    //Db::execute('update t_work_plan set complete_sign = 1,begin_datetime=getdate(),begin_position_x=?,begin_position_y=? where convert(varchar(23),plan_date,121)+zycz_gh+patient_id =?',[$p_x,$p_y,$create_re]);
                    Db::execute('update t_work_plan set complete_sign = 1,begin_datetime=sysdate(),begin_position_x=?,begin_position_y=? where CONCAT(convert(plan_date,char),zycz_gh,patient_id) =?',[$p_x,$p_y,$create_re]);
                    Db::commit();
                }catch(\Exception $e){
                    Db::rollback();
                    $return_res->status = 'Err';
                    $return_res->data_result='更新任务标识失败!';
                    return json_encode($return_res,JSON_UNESCAPED_UNICODE);
                }
                $return_res->status = 'Ok';
                $return_res->data_result = $create_re;
                return json_encode($return_res,JSON_UNESCAPED_UNICODE);
            }
        }
    }
    //建立新的计划任务的方法
    function create_plan($patient_id,$gh,$p_x,$p_y,$create_type){        
        //$datas=Db::query("select getdate() as dt");
        $datas=Db::query("select sysdate() as dt");
        if($datas ==null){
            return 'Err:getdate err';
        }else{
            $data = $datas[0];
            $now_dt = $data['dt'];
        }
        Db::startTrans();
        try{
            //停止已开始的任务
           //Db::execute("Update t_work_plan set complete_sign=2 ,complete_datetime=getdate(),complete_type=4,complete_position_x =?,complete_position_y =? where patient_id = ? and complete_sign = 1",[$p_x,$p_y,$patient_id]);
           Db::execute("Update t_work_plan set complete_sign=2 ,complete_datetime=sysdate(),complete_type=4,complete_position_x =?,complete_position_y =? where patient_id = ? and complete_sign = 1",[$p_x,$p_y,$patient_id]);
            //建立新的计划任务
            Db::execute('insert into t_work_plan(plan_date,zycz_gh,patient_id,create_type) values(?,?,?,?)',[$now_dt,$gh,$patient_id,$create_type]);
            Db::commit();
        }catch(\Exception $e){
            Db::rollback();
            return 'Err:insert err';
        }
        //$datas_plan_info = Db::query("select convert(varchar(23),plan_date,121) as p_dt, zycz_gh as gh, patient_id as p_id from t_work_plan where plan_date = ? and zycz_gh=? and patient_id = ? and complete_sign = 0",[$now_dt,$gh,$patient_id]);
        $datas_plan_info = Db::query("select convert(plan_date,char) as p_dt, zycz_gh as gh, patient_id as p_id from t_work_plan where plan_date = ? and zycz_gh=? and patient_id = ? and complete_sign = 0",[$now_dt,$gh,$patient_id]);
        if($datas_plan_info == null){
            return 'Err:getdata err';
        }else{
            $data_plan_info = $datas_plan_info[0];
            return $data_plan_info['p_dt'].$data_plan_info['gh'].$data_plan_info['p_id'];
        }
    }
    //结束任务方法
    public function end_job($patient_id,$db_json,$token){
        $return_res = new Return_result();
        //验证token是否有效
        $token_res = $this->check_token($token);
        if($token_res == null ){
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        if(isset($token_res['gh'])){
            $gh = $token_res['gh'];
        }else{
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        //地标下,y
        $db=json_decode($db_json,true);         
        if(isset($db['x']) == false or isset($db['y']) == false ){
            $p_x=0;
            $p_y=0;
        }else{
            $p_x=$db['x'];
            $p_y=$db['y'];                
        }
        //1判断患者信息是否存在
        $datas=Db::query('select count(1) as rs from t_pif where patient_id =? and exist_sign=0',[$patient_id]);
        if($datas[0]['rs'] <=0){
            $return_res->status = 'Err';
            $return_res->data_result='患者信息不存在!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        //2判断患者是否存在进行中的任务
        //$datas=Db::query('select convert(varchar(23),plan_date,121) as p_dt, zycz_gh as gh, patient_id as p_id from t_work_plan  where patient_id =? and complete_sign = 1 ',[$patient_id]);
        $datas=Db::query('select convert(plan_date,char) as p_dt, zycz_gh as gh, patient_id as p_id from t_work_plan  where patient_id =? and complete_sign = 1 ',[$patient_id]);
        if($datas == null ){        
            $return_res->status = 'Err';
            $return_res->data_result='该患者未发现进行中的任务!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        //结束已经开始的任务
        Db::startTrans();
        try{
            //停止已开始的任务
            //Db::execute("Update t_work_plan set complete_sign=2 ,complete_datetime=getdate(),complete_type=4,complete_position_x =?,complete_position_y =? where patient_id = ? and complete_sign = 1",[$p_x,$p_y,$patient_id]);
            Db::execute("Update t_work_plan set complete_sign=2 ,complete_datetime=sysdate(),complete_type=4,complete_position_x =?,complete_position_y =? where patient_id = ? and complete_sign = 1",[$p_x,$p_y,$patient_id]);           
            Db::commit();
            $return_res->status = 'Ok';
            $return_res->data_result='结束任务成功!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            Db::rollback();
            $return_res->status = 'Err';
            $return_res->data_result='结束任务失败!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);            
        }        
    }
    //传送定位坐标的方法
    public function send_db( $job_id,$db_json,$token,$db_time,$db_type,$send_type){
        $return_res = new Return_result();
        $job_status=0;
        //验证token是否有效
        $token_res = $this->check_token($token);
        if($token_res == null ){
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        if(isset($token_res['gh'])){
            $gh = $token_res['gh'];
        }else{
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        //地标下,y
        $db=json_decode($db_json,true);         
        if(isset($db['x']) == false or isset($db['y']) == false ){
            $p_x=0;
            $p_y=0;
        }else{
            $p_x=$db['x'];
            $p_y=$db['y'];                
        }
        //1获取任务信息
        //$datas=Db::query('select complete_sign as rs,convert(varchar(23),plan_date,121) as plan_date,zycz_gh as gh,patient_id as patient_id from t_work_plan where convert(varchar(23),plan_date,121)+zycz_gh+patient_id =?',[$job_id]);
        $datas=Db::query('select complete_sign as rs,convert(plan_date,char) as plan_date,zycz_gh as gh,patient_id as patient_id 
            from t_work_plan 
            where CONCAT(convert(plan_date,char),zycz_gh,patient_id) =?',[$job_id]);
        if($datas==null){
            $return_res->status = 'Err';
            $return_res->data_result='任务信息不存在!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }else{
            if($datas[0]['rs'] == 0){
                $return_res->status = 'Err';
                $return_res->data_result='任务未开始!';
                return json_encode($return_res,JSON_UNESCAPED_UNICODE);
            }else{
                $job_status=$datas[0]['rs'];
            }
        }        
        //记录位置信息
        Db::startTrans();
        try{
            Db::execute('insert into T_position_record(plan_date,zycz_gh,patient_id,record_datetime,position_x,position_y,record_type,get_position_type,job_status) values(?,?,?,?,?,?,?,?,?)',[$datas[0]['plan_date'],$datas[0]['gh'],$datas[0]['patient_id'],$db_time,$p_x,$p_y,$send_type,$db_type,$job_status]);
            Db::commit();
        }catch(\Exception $e){
            Db::rollback();
            $return_res->status = 'Err';
            $return_res->data_result='写入数据库失败!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
        $return_res->status = 'Ok';
        $return_res->data_result ='记录地址坐标成功';
        return json_encode($return_res,JSON_UNESCAPED_UNICODE);
    }
    //测试数据库连接
    public function test_con(){
        Db::startTrans();
        try{
            $databas=Db::query("select p_name as xm,p_gender as xb,position_x as x,position_y as y,sysdate() as dt from T_pif where exist_sign=0 LIMIT 10");
            Db::commit();
            return $databas;
        }catch(\Exception $e){
            Db::rollback();
            return $e;
        }
    }
    //获取前10患者信息的方法
    public function get_patient($token){
        $return_res = new Return_result();        
        //验证token是否有效
        if($this->check_token($token) == null){
            $return_res->status = 'Err';
            $return_res->data_result='token 失效!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }        
        //获取患者信息
       // $datas = Db::query("select p_name as xm,p_gender as xb,position_x as x,position_y as y,getdate() as dt from T_pif where patient_id =? and exist_sign=0",[$patient_id]);
        $datas = Db::query("select patient_id as p_id,p_name as xm,p_gender as xb,position_x as
                             x,position_y as y,sysdate() as dt from T_pif where exist_sign=0 LIMIT 10");
        if ($datas == null){
            $return_res->status = 'Err';
            $return_res->data_result='患者ID错误,没有查询到结果!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }else{
            return $datas;
        }
    }
    //获取所有开始的任务
    public function get_start_job(){
        $return_res = new Return_result();     
        $datas=Db::query("select b.position_x as p_x,b.position_y as p_y,a.plan_date,b.p_name,a.begin_datetime,c.xm,a.patient_id,a.zycz_gh,CONCAT(convert(a.plan_date,char),a.zycz_gh,a.patient_id) as job_id
            from t_work_plan a,t_pif b,t_zycz_info c
            where a.patient_id = b.patient_id 
            and a.zycz_gh = c.gh
            and a.complete_sign = 1");
        if($datas == null ){
            $return_res->status = 'Err';
            $return_res->data_result='没有查询到数据!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }else{
            $return_res->status = 'Ok';
            $return_res->data_result = $datas;
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
    } 
    //根据任务ID获取护理员最后一次上传位置
    public function get_job_czy_db($job_id){
        $return_res = new Return_result();     
        $datas=Db::query("select  record_datetime,position_x,position_y,record_type,create_datetime,get_position_type,job_status
            from t_position_record 
            where 
            CONCAT(convert(plan_date,char),zycz_gh,patient_id) =?            
            ORDER BY create_datetime desc
            LIMIT 1 ",[$job_id]);
        if($datas == null ){
            $return_res->status = 'Err';
            $return_res->data_result='没有查询到数据!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }else{
            $return_res->status = 'Ok';
            $return_res->data_result = $datas;
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        }
    }
    //获取所有开始任务的患者地理标点和护理员标点
    public function get_job_all_db(){
        $return_res = new Return_result();        
        $patient_db = json_decode($this->get_start_job());
        if ($patient_db->status =='Err'){
            $return_res->status = 'Err';
            $return_res->data_result='没有查询到数据!';
            return json_encode($return_res,JSON_UNESCAPED_UNICODE);
        };
        $p_result = $patient_db->data_result;
        $job_all_db = array(new job_all_db());    
        $count = 0;
        foreach($p_result as $item){           
            $job_id = $item->job_id;
            $job_all_db[$count]->job_id = $job_id;
            $job_all_db[$count]->p_info = $item;
            $u_res = json_decode($this->get_job_czy_db($job_id));          
            if ($u_res->status == 'Err'){ 
                $count += 1;
                continue;}
            $job_all_db[$count]->u_info = $u_res->data_result;
            $count += 1;
        }
        $return_res->status = 'Ok';
        $return_res->data_result = $job_all_db;
        return json_encode($return_res,JSON_UNESCAPED_UNICODE);
    }
}
//任务的患者和护理员信息
class job_all_db{    
    public $job_id;
    public $p_info;
    public $u_info;
}