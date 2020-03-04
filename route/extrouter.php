<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;
Route::miss('Error');
Route::rule('/ext/t', 'Testcon/get_czy_info','POST')->ext();
Route::rule('/ext/get_patient_info', 'Testcon/get_patient_info','POST')->ext();
Route::rule('/ext/set_patient_init', 'Testcon/set_patient_init','POST')->ext();
Route::rule('/ext/start_job', 'Testcon/start_new_job','POST')->ext();
Route::rule('/ext/end_job', 'Testcon/end_job','POST')->ext();
Route::rule('/ext/send_db', 'Testcon/send_db','POST')->ext();
Route::rule('/ext/get_patient', 'Testcon/get_patient_view','POST')->ext();
Route::rule('/ext/get_job_czy_db', 'Testcon/get_job_czy_db','POST')->ext();
// Route::rule('/ext/check_token', 'Testcon/check_token','POST')->ext();
Route::get('/test/get_job_all_info', 'Testcon/get_job_all_info');
Route::get('/test/get_start_job', 'Testcon/get_start_job');
Route::get('/test', 'Testcon/test');
Route::get('/map_test','Testcon/map_test');
Route::get('/test_con','Testcon/test_con');

// Route::rule('login', 'Extcontorller/login','GET')->ext();
// Route::get('/login_err/:err', 'Extcontorller/login_err');
// Route::rule('login_verify', 'Extcontorller/login_verify','POST')->ext();
// Route::rule('error_bower', 'Extcontorller/bw_error','GET')->ext();
// Route::rule('query_dwxx_all', 'Extcontorller/query_tjdwxx_all')->ext();
// Route::rule('query_tjlb_all', 'Extcontorller/query_tjlb_all')->ext();
?>