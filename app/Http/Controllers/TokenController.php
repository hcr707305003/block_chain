<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CodeController as code;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\User\LoginController as login;

class TokenController extends Controller
{
    /*
     * 判断token是否失效
     * param@ uid用户id  token用户token
     * action get
     */
    static function failure_time($data)
    {
        $token_time = DB::table('admin_visit')->where('u_id', '=', $data['uid'])->where('token', '=', $data['token'])->first();
        if (!$token_time) {
            $ip = $_SERVER["REMOTE_ADDR"];
            $array = [
                'u_id' => $data['uid'],
                'token' => login::_token($data['uid']),
                'ip' => $ip,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ];
            if (DB::table('admin_visit')->insert($array)) {
                return 'success';
            };
        } else {
            $array = [
                'ip' => $_SERVER["REMOTE_ADDR"],
                'updated_at' => date('Y-m-d H:i:s', time()),
            ];

            $update = DB::table('admin_visit')->where('token', '=', $data['token'])->update($array);
            sleep(1);
            if ($update) {
                $failure_time = strtotime($token_time->updated_at)+24*60*60;
                if ($failure_time < time()) {
                    return 'fail';
                } else {
                    return 'success';
                }
            }
        }
    }

    /*
     * 给token一个失效时间
     * param@ uid用户id  token用户token
     * action get
     */
    function set_failure_time($data)
    {
        $date = date('Y-m-d H:i:s', time())-24*30*60;
        $token_time = DB::table('admin_visit')->where('u_id', '=', $data['uid'])->where('token', '=', $data['token'])->update([
            'updated_at' => $date
        ]);
        if ($token_time) {
            return 'success';
        } else {
            return 'fail';
        }
    }



    /*
     * 判断token是否存在
     * param@ uid用户id  判断token是否在header头
     * action get
     */
    //
    function exists_token(Request $request)
    {
        $data = $request->all();
        $headers = $this->em_getallheaders();
        if (isset($headers['Authorization'])) {
            //获取到header头的token
            $get_token = trim(ltrim($headers['Authorization'], 'Bearer'));
            if ($get_token) {
                //cookie不存在的时候
                $find = DB::table('admin_visit')->where('token', '=', $get_token)->first();
                if (!$find) {
                    return code::code('1004');
                }
                $uid = $find->u_id;

                //判断token是否过期
                $arr = array(
                    'uid' => $uid,
                    'token' => $get_token
                );
                if ($this->failure_time($arr)  == "success") {
                    return json_encode([
                        'msg' => 'success',
                    ]);
                } else {
                    return code::code(1004);
                }
            } else {
                return code::code(1001);
            }
        } else {
            return code::code(1001);
        }
    }


    //获取header头信息
    public static function em_getallheaders()
    {
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    //获取到header的token值
    public function get_header_token_uid()
    {
        $headers = $this::em_getallheaders();
        $get_token = trim(ltrim($headers['Authorization'], 'Bearer'));
        $arr = array();
        if ($get_token) {
            $arr['token'] = $get_token;
            $find = DB::table('admin_visit')->where('token', '=', $get_token)->first();
            if ($find) {
                $arr['uid'] = $find->u_id;
            } else {
                $arr['uid'] = null;
            }
        } else {
            $arr['token'] = '';
        }
        return $arr;
    }
}
