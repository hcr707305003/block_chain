<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\CodeController as code;
use App\Http\Controllers\TokenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SharingController;
use App\Http\Controllers\TaskController as task;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ATOBController as a_to_b;

class UserController extends TokenController
{
    //引入父类方法
    public function __construct(Request $request)
    {
        $this->msg = parent::exists_token($request);
    }

    /**
     * @param Request $request
     * @return string
     * 实名认证
     */
    public function Realcheck(Request $request)
    {
        if ($this->msg !== '{"msg":"success"}') {
            return code::code(1004);
        }
        if (!$request->token) {
            return code::code(1004);
        }
        //检车是否认证过
        $admin_visit = DB::table('admin_visit')->where('token', '=', $request->token)->first();
        $check_uid = DB::table('admin_real_check')->where('uid', '=', $admin_visit->u_id)->first();
        if ($check_uid) {  //如果认证过返回
            return code::code(1013);
        }
        //检测真实姓名不能为空
        if (!isset($request->real_name)) {
            return code::code(1014);
        }
        //检测身份证不能为空
        if (!isset($request->IDCard)) {
            return code::code(1015);
        }
        $admin_visit = DB::table('admin_visit')->where('token', '=', $request->token)->first();
        //用户id

        $uid = $admin_visit->u_id;
        $real_name = $request->real_name;
        //转化成整型
        $IDCard = intval($request->IDCard);
        //检测身份证是否符合规则
        $preg_card = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";  //18位验证
        $isIDCard1 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/"; //15位验证
        if (preg_match($preg_card, $IDCard) || preg_match($isIDCard1, $IDCard)) {
            //插入到实名认证表
            $data['uid'] = $uid;
            $data['real_name'] = $real_name;
            $data['IDCard'] = $IDCard;
            $data['created_at'] = date('Y-m-d H:i:s', time());
            $data['updated_at'] = date('Y-m-d H:i:s', time());
            $insrt = DB::table('admin_real_check')->insert($data);
            //查询元力 资产  // 日志元力
            $property_power  = DB::table('admin_main_property')->where('uid', '=', $admin_visit->u_id)->value('total_power'); //资产主表元力
            $log_power = DB::table('admin_power')->where('uid', '=', $admin_visit->u_id)->value('power');  //元力日志表
            //实名认证+20
            $prope_power['total_power'] = $property_power + 20;  //资产主表元力+20
            $logs_power['power'] = $log_power + 20; //元力日志表+20
            $data_prope_power = DB::table('admin_main_property')->where('uid', '=', $admin_visit->u_id)->update($prope_power);
            $data_log_power = DB::table('admin_power')->where('uid', '=', $admin_visit->u_id)->update($logs_power);
            if ($insrt && $data_prope_power && $data_log_power) {
                return json_encode([
                    'msg' => 'success'
                ]);
            }else{
                return code::code(1030);
            }
        } else {
            return code::code(1016);
        }

    }

    /**
     * 用户信息列表展示
     */
    public function UserInfo(Request $request)
    {
        if ($this->msg !== '{"msg":"success"}') {
            return code::code(1004);
        }

        if (!$request->token) {
            return code::code(1004);
        }
        //检车是否认证过
        $admin_visit = DB::table('admin_visit')->where('token', '=', $request->token)->first();
        $check_uid = DB::table('admin_real_check')->where('uid', '=', $admin_visit->u_id)->first();
        if ($check_uid == null) {  //没有实名认证
            $info = DB::table('admin_reception_users')->where('id', '=', $admin_visit->u_id)->first();
            $new_tel1 = substr($info->phone, 0, 3) . '****' . substr($info->phone, 7);
            if ($info) {
                $Uinfo['phone'] = $new_tel1;
                $Uinfo['real_name'] = '';
                $Uinfo['IDCard'] = '';
                $Uinfo['version'] = '1.0.1';
                $Uinfo['contact'] = '400-000-0000';
                return response()->json([
                    "msg" => "success",
                    "data" => $Uinfo,
                ]);
            }
        }else{
            //如果是实名认证的
            $info = DB::table('admin_reception_users as ru')
                ->leftJoin('admin_real_check as rc', 'ru.id', '=', 'rc.uid')
                ->where('ru.id', '=', $admin_visit->u_id)
                ->select('ru.phone','rc.real_name','rc.IDCard')
                ->first();

            $new_tel1 = substr($info->phone, 0, 3) . '****' . substr($info->phone, 7);  //电话号打马赛克
            $new_IDCard = substr($info->IDCard, 0, 6) . '****' . substr($info->IDCard, 14); //身份证打马赛克
            $new_name = substr($info->real_name, 0) . '*' . substr($info->real_name, 1); //身份证打马赛克
            if ($info) {
                $Uinfo['phone'] = $new_tel1;
                $Uinfo['real_name'] = $info->real_name;
                $Uinfo['IDCard'] = $new_IDCard;
                $Uinfo['version'] = '1.0.1';
                $Uinfo['contact'] = '400-000-0000';
                return response()->json([
                    "msg" => "success",
                    "data" => $Uinfo,
                ]);
            }
        }
    }

    /**
     * 任务展示接口
     */
    public function raskList(Request $request)
    {
        if ($this->msg !== '{"msg":"success"}') {
            return code::code(1004);
        }

        //检车是否认证过
        $admin_visit = DB::table('admin_visit')->where('token', '=', $request->token)->first();
        $check_uid = DB::table('admin_real_check')->where('uid', '=', $admin_visit->u_id)->first();
        if ($check_uid == true){
            $exist = 0;
        }else{
            $exist = 1;
        }
        $data['real_name'] = [
            'data' => '实名认证',
            'state' => $exist
        ];
        //判断今天是否签到
        $login = DB::table('admin_stone')->where('uid', '=', $admin_visit->u_id)->orderby('created_at', 'desc')->first();
        if ($login) {
            $user_date_time = strtotime($login->updated_at);
            $Tomorrow = strtotime(date('Y-m-d', time()));
            $Yesterday = strtotime(date('Y-m-d', time()-60*60*24));
            if ($user_date_time > $Yesterday || $user_date_time < $Tomorrow) {
                $exist_login = 0;
            } else {
                $exist_login = 1;
            }
        }
        $data['login'] = [
            'data' => '签到',
            'state' => $exist_login
        ];

        //判断是否邀请
        $user_data = DB::table('admin_reception_users')->where('id', '=', $admin_visit->u_id)->first();
        // var_dump($user_data->invitation_once);die;
        if ($user_data) {
            if ($user_data->invitation_once >= 10) {
                $exist = 0;
            } else {
                $exist = 1;
            }
        }   
        $data['share'] = [
            'data' => '邀请好友',
            'state' => $exist
        ];
        
        //判断游戏
        $data['game'] = [
            'data' => '游戏',
            'state' => 1
        ];
        return json_encode($data);
    }


    /*
     * 修改用户信息接口
     */
//    public function UpdateUser(Request $request)
//    {
//        if ($this->msg !== '{"msg":"success"}') {
//            return code::code(1004);
//        }
//        $uid = $request->uid;
//        $username = $request->username;
//        $phone = $request->phone;
//        $IDCard = $request->IDCard;
//        if (isset($username)) {
//            $user['username'] = $username;
//            $result = DB::table('admin_reception_users')->where('uid', '=', $uid)->update($user);
//            if ($result){
//                return response()->json([
//                    'msg' => 'success'
//                ]);
//            }
//        }
//        if (isset($IDCard)) {
//            //转化成整型
//            $IDCard = intval($IDCard);
//            //检测身份证是否符合规则
//            $preg_card = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";  //18位验证
//            $isIDCard1 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/"; //15位验证
//            if (preg_match($preg_card, $IDCard) || preg_match($isIDCard1, $IDCard)) {
//                $IDCard_data['IDCard'] = $IDCard;
//                $result = DB::table('admin_real_check')->where('uid', '=', $uid)->update($IDCard_data);
//                if ($result){
//                    return response()->json([
//                        'msg' => 'success'
//                    ]);
//                }
//            }
//        }
//        if (isset($phone)) {
//            $data = (new LoginController())->phoneregister($phone);
//        }
//    }
}
