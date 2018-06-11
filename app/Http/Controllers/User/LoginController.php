<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\TokenController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\decController as dec;
use App\Http\Controllers\CodeController as code;
use App\Http\Controllers\TokenController as token;
class LoginController extends TokenController
{


    public function __construct() 
    {
        session_start();
    }

    //手机获取验证码
    public function phoneregister(Request $request)
    {
        $data = $request->all();
        if (!isset($data['phone'])) {
            return code::code(1005);
        }
        $phone = $data['phone'];
        $data['time'] = time();
        preg_match('/^1[34578]\d{9}$/', $phone, $exists_phone);
        if (isset($exists_phone[0])) {
            $code = rand(10000,99999);
            $xcode = md5($phone.$code);
            Session::put('phone', $data);
            $get_information = $this->_send_phone($phone, $code);
            if (json_decode($get_information)->msg == "ok") {
                return json_encode([
                    'msg' => 'success',
                    'data' => [
                        'signature' => $xcode,
                    ]
                ]);
            } else {
                return code::code(1002);
            } 
        } else {
            return code::code(1011);
        } 
    }

    /*
     * 验证手机验证码和手机号码是否一致(注册)
     * param@ code 验证码  phone 手机
     */
    public function phoneexists(Request $request)
    {
        $data = $request->all();
        if (!isset($data['phone'])) {
            return code::code(1005);
        }
        $phone = Session::get('phone');
        if ($phone['time']+5*30 < time()) {
            return code::code(1008);
        }
        if (!isset($data['signature'])) {
            return code::code(1006);
        }
        if (isset($data['inviter'])) {
            $arr['inviter'] = $data['inviter'];//可选参数
        }

        if (md5($data['phone'].$data['code']) == $data['signature']) {
            $arr = array();
            $arr['username'] = $data['phone'];
            $arr['phone'] = $data['phone'];
            $arr['created_at'] = date('Y-m-d H:i:s', time());
            // 插入数据 返回插入数据的id
            $insert_id = DB::table('admin_reception_users')->insertGetId($arr);
            if ($insert_id) {
                $Invitation = dec::dec2s4($insert_id);
                if (DB::table('admin_reception_users')->where('id', $insert_id)->update(['code' => $Invitation, 'updated_at' => date('Y-m-d H:i:s', time())])) {
                    Session::forget('phone');
                    Cookie::make('uid', $insert_id, 60*24);
                    $token = $this->_token($insert_id);
                    if ($token) {
                        $session_data = array(
                            'uid' => $insert_id,
                            'token' => $token
                        );
                        if ($this->failure_time($session_data) == "success") {
                            return json_encode([
                                'msg' => 'success',
                                'user' => [
                                    'id' => $insert_id,
                                    'name' => $data['phone'],
                                    'token' => $token,
                                ],
                            ]);
                        }
                    }
                }
            }
            return code::code(1002);
        } else {
            return code::code(1008);
        }
    }

    //手机登录
    public function phonelogin(Request $request)
    {
        $data = $request->all();
        if (!isset($data['signature'])) {
            return code::code(1006);
        }

        if (!isset($data['phone'])) {
            return code::code(1005);
        }

        if (!isset($data['code'])) {
            return code::code(1007);
        }

        $phone = $data['phone'];
        preg_match('/^1[34578]\d{9}$/', $phone, $exists_phone);
        if (isset($exists_phone[0])) {
            $get_user_data = DB::table('admin_reception_users')->where('phone', '=', $phone)->first();
            if ($get_user_data) {
                $phone = Session::get('phone');
                /*if ($phone['time']+5*60 < time()) {
                    return code::code(1008);
                } else {*/
                    if (md5($data['phone'].$data['code']) == $data['signature']) {
                        $token = $this->_token($get_user_data->id);
                        $ip = $_SERVER["REMOTE_ADDR"];
                        $get_phone = DB::table('admin_visit')->where('u_id', '=', $get_user_data->id)->update([
                            'updated_at' => date('Y-m-d H:i:s', time()),
                            'token' => $token,
                            'ip' => $ip
                        ]);
                        /*Session::forget('phone');
                        Cookie::make('uid', $get_user_data->id, 60*60*24);*/
                        return json_encode([
                            'msg' => 'success',
                            'user' => [
                                'id' => $get_user_data->id,
                                'name' => $data['phone'],
                                'token' => $token,
                            ],
                        ]);
                    } else {
                        return code::code(1008);
                    }
                // }
            } else {
                if (md5($data['phone'].$data['code']) == $data['signature']) {
                    $arr = array();
                    $arr['username'] = $data['phone'];
                    $arr['phone'] = $data['phone'];
                    $arr['created_at'] = date('Y-m-d H:i:s', time());
                    // 插入数据 返回插入数据的id
                    $insert_id = DB::table('admin_reception_users')->insertGetId($arr);
                    if ($insert_id) {
                        // =======================================================
                        // 为用户创建每张表的数据
                        // admin_stone表
                        DB::table('admin_stone')->insert([
                            'uid' => $insert_id,
                            'appid' => 0,
                            'resid' => 0,
                            'actid' => 0,
                            'stone' =>50,
                            'created_at' => date('Y-m-d H:i:s', time()),
                            'stone_balance' => 50,
                            'updated_at' => date('Y-m-d H:i:s', time()),
                        ]);

                        //admin_power表
                        DB::table('admin_power')->insert([
                            'uid' => $insert_id,
                            'appid' => 0,
                            'resid' => 0,
                            'actid' => 0,
                            'power' => 2,
                            'created_at' => date('Y-m-d H:i:s', time()),
                            'power_balance' => 2,
                            'updated_at' => date('Y-m-d H:i:s', time()),
                        ]);

                        //admin_main_property表
                        DB::table('admin_main_property')->insert([
                            'uid' => $insert_id,
                            'total_stone' => 50,
                            'total_power' => 2,
                            'created_at' => date('Y-m-d H:i:s', time()),
                            'updated_at' => date('Y-m-d H:i:s', time()),
                        ]);
                        // =======================================================

                        $Invitation = dec::dec2s4($insert_id);
                        $update = DB::table('admin_reception_users')->where('id', $insert_id)->update(['code' => $Invitation, 'updated_at' => date('Y-m-d H:i:s', time())]);
                        sleep(1);
                        /*Session::forget('phone');
                        Cookie::make('uid', $insert_id, 60*24);*/
                        $token = $this->_token($insert_id);
                        if ($token) {
                            $session_data = array(
                                'uid' => $insert_id,
                                'token' => $token
                            );
                            // if ($this->failure_time($session_data) == "success") {
                            return json_encode([
                                'msg' => 'success',
                                'user' => [
                                    'id' => $insert_id,
                                    'name' => $data['phone'],
                                    'token' => $token,
                                ],
                            ]);
                            // }
                        } else {
                            return code::code(1004);
                        }
                    } else {
                        return code::code(1002);
                    }
                } else {
                    return code::code(1008);
                }
            }
        } else {
            return code::code(1011);
        }
    }

    /*
     * 退出登录接口
     * param@ uid用户id 判断token是否在header头内
     * action get
     */
    public function phonelogout(Request $request)
    {
        $data = $request->all();
        //判断用户id是否存在
        if (!$data['uid']) {
            return code::code(1003);
        }
        $headers = token::em_getallheaders();
        if (isset($headers['Authorization'])) {
            //获取到header头的token
            $get_token = trim(ltrim($headers['Authorization'], 'Bearer'));
            if ($get_token) {
                $arr = array(
                    'uid' => $data['uid'],
                    'token' => $get_token
                );
                if (token::set_failure_time($arr) == "success") {
                    return json_encode([
                        'msg' => 'success',
                    ]);
                } else {
                    return code::code(1002);
                }
            } else {
                return code::code(1001);
            }
        } else {
            return code::code(1001);
        }
    }

    //手机第三方验证接口
    private function _send_phone($phone, $message)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");

        curl_setopt($ch, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_0 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD  , 'api:key-0124f09fad7fe05116959d556d7a1ece');

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $phone,'message' => '验证码：'.$message.'【太域联盟】'));

        $res = curl_exec( $ch );
        curl_close( $ch );
        //$res  = curl_error( $ch );
        // var_dump($res);
        return $res;
    }


 

    /*
     * 手机找回密码
     * param@ phone 手机 code验证码 signature 答名 passwd 密码
     * function get
     */
    public function retrieve_password(Request $request)
    {
        $data = $request->all();
        if (!$data['phone']) {
            return code::code(1005);
        }

        if (!$data['code']) {
            return code::code(1007);
        }

        if (!isset($data['signature'])) {
            return code::code(1006);
        }

        if (!isset($data['passwd'])) {
            return code::code(1006);
        }

        $user_data = DB::table('admin_reception_users')->where('phone', '=', $data['phone'])->first();
        if ($user_data) {
            if (md5($data['phone'].$data['code']) == $data['signature']) {
                    $user_data = DB::table('admin_reception_users')->where('phone', '=', $data['phone'])->update([
                        'password' => md5($data['passwd']),
                    ]);
                    if ($user_data) {
                        return json_encode([
                            'msg' => 'success',
                        ]);
                    }
                } else {
                    return code::code(1008);
                }
        } else {
            return code::code(1009);
        }
    }


    //生成token值(需要传用户id)
    public function _token($uid)
    {
    	$token = md5(base64_encode(uniqid().time()));  //md5  生成为一id加密.token
        $ip = $_SERVER["REMOTE_ADDR"]; //获取ip
        $get_token_data = DB::table('admin_visit')->where('u_id', '=', $uid)->first();  //通过id查询一条数据
        if ($get_token_data) {
            DB::table('admin_visit')->where('u_id', '=', $uid)->update([
                'ip' => $ip,  //ip
                'token' => $token,
                'updated_at' => date('Y-m-d H:i:s', time())
            ]);
            return $token;
        } else {
        	if (DB::table('admin_visit')->insert(['token' => $token, 'ip' => $ip, 'u_id' => $uid, 'created_at' => date('Y-m-d H:i:s', time()), 'updated_at' => date('Y-m-d H:i:s', time())])) {
        		return $token;
        	}
        }
        return code::code(1002);
    }


   /*
    * 判断运营商的信息
    * action GET
    * param name(varchar(20)) Identification(varchar(50)) code(varchar(10))
    */
    /*public function exists_operator()
    {
    	$data = array();
    	//查看运营商的名称
    	if (isset($_REQUEST['name'])) {
    		$data['name'] =  $_REQUEST['name'];
    	} else {
    		return json_encode([
    			'msg' => '没有检测到运营商名字',
    			'data' => array(
    				'info' => '请检测你是否有填写name值'
    			),
    		]);
    	}

    	//查看运营商的标识
    	if (isset($_REQUEST['identification'])) {
    		$data['identification'] =  $_REQUEST['identification'];
    	} else {
    		return json_encode([
    			'msg' => '没有检测到运营商标识',
    			'data' => array(
    				'info' => '请检测你是否有填写Identification值'
    			),
    		]);
    	}

    	//查看运营商编码
    	if (isset($_REQUEST['code'])) {
    		$data['code'] =  $_REQUEST['code'];
    	} else {
    		return json_encode([
    			'msg' => '没有检测到运营商编码',
    			'data' => array(
    				'info' => '请检测你是否有填写Code值'
    			),
    		]);
    	}

    	$exists_operator = DB::table('admin_operator')->where('name', '=', $data['name'])->where('identification', '=', $data['identification'])->where('code', '=', $data['code'])->first();
    	if (!$exists_operator) {
    		return json_encode([
    			'msg' => '没有填写正确的信息',
    			'data' => array(
    				'info' => '您填写的信息不正确，请重复填写信息'
    			),
    		]);
    	} else {
    		return $this->_token();
    	}
    }*/
}