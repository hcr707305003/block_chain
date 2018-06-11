<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CodeController as code;
use DB;

class HistoryController extends TokenController
{

	//引入父类方法
    public function __construct(Request $request)
    {
        \DB::connection()->enableQueryLog(); // 开启查询日志 
        $this->msg = parent::exists_token($request);
    }

    //签到记录
    public function login_record(Request $request)
    {
    	$data = $request->all();
        if (json_decode($this->msg)->msg != 'success') {
            return code::code(1004);
        }
        //获取到token和id
        $user_data = parent::get_header_token_uid();
    	$uid = $user_data['uid'];
    	$data['exists'] = isset($data['exists'])?$data['exists']:1;
    	$limit = isset($data['limit'])?$data['limit']:1;
    	//查询数据库
    	if ($data['exists'] == 1) {//这里1为元石
    		$user_power_stone_history =  DB::table('admin_stone')->where('uid', '=', $uid)->limit(intval($limit))->get();

    	} else if($data['exists'] == 2) {//这里2为元力
    		$user_power_stone_history =  DB::table('admin_power')->where('uid', '=', $uid)->limit(intval($limit))->get();
    	} else {
    		return code::code('1028');
    	}
      

        $powerNum = DB::table('admin_main_property')->where('uid', '=', $uid)->select('total_power','total_stone')->first(); //查询该用户当前的元石
        // var_dump($powerNum);die;
        if ($data['exists'] == 1) {//这里1为元石
            $ore = $powerNum->total_stone;

        } else if($data['exists'] == 2) {//这里2为元力
            $ore = $powerNum->total_power;
            
        } else {
            return code::code('1028');
        }
    	return json_encode([
    		'msg' => 'success',
    		'data' => [
                'ore' => $ore,
                'history' => $user_power_stone_history
            ],
    	]);
    }
}