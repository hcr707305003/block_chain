<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CodeController extends Controller
{
    //定义code错误码
    public static function code($code)
    {
    	switch ($code) {
    		case '1001':
    			return json_encode([
                    'msg' => 'error',
                    'code' => '1001',
                    'data' => [
                        'info' => '没有获取到token值'
                    ]
                ]);
    		break;

    		case '1002':
    			return json_encode([
		            'msg' => 'error',
		            'code' => '1002',
		            'data' => [
		                'info' => '请重新请求页面，刷新数据，重新提交数据'
		            ]
		        ]);
    		break;
    		
    		case '1003':
    			return json_encode([
	                'msg' => 'error',
	                'code' => '1003',
	                'data' => [
	                    'info' => '没有获取到用户id'
	                ]
	            ]);
    		break;

    		case '1004':
    			return json_encode([
                    'msg' => 'error',
                    'code' => '1004',
                    'data' => [
                        'info' => '不是有效的token,请重新获取'
                    ]
                ]);
			break;

            case '1005':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1005',
                    'data' => [
                        'info' => '没有获取到用户手机号'
                    ]
                ]);
            break;

            case '1006':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1006',
                    'data' => [
                        'info' => '获取不到signature'
                    ]
                ]);
            break;

            case '1007':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1007',
                    'data' => [
                        'info' => '获取不到code验证码'
                    ]
                ]);
            break;

            case '1008':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1008',
                    'data' => [
                        'info' => '验证码不正确或者验证码已过期'
                    ]
                ]);
            break;

            case '1009':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1009',
                    'data' => [
                        'info' => '请注册手机号码'
                    ]
                ]);
            break;

            case '1010':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1010',
                    'data' => [
                        'info' => '请输入您的密码'
                    ]
                ]);
            break;

            case '1011':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1011',
                    'data' => [
                        'info' => '不合法的手机号码，请输入正确的手机号码'
                    ]
                ]);
            break;

             case '1012':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1012',
                    'data' => [
                        'info' => '用户不存在,请注册登录'
                    ]
                ]);
            break;

            case '1013':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1013',
                    'data' => [
                        'info' => '您已经实名认证过了'
                    ]
                ]);
            break;

            case '1014':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1014',
                    'data' => [
                        'info' => '真实姓名不能为空'
                    ]
                ]);
            break;

            case '1015':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1015',
                    'data' => [
                        'info' => '身份证号码不能为空'
                    ]
                ]);
            break;

            case '1016':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1016',
                    'data' => [
                        'info' => '请输入正确的身份证号码'
                    ]
                ]);
            break;

            case '1017':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1017',
                    'data' => [
                        'info' => '非法登入'
                    ]
                ]);
            break;

            case '1018':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1018',
                    'data' => [
                        'info' => '添加的元石不能为空'
                    ]
                ]);
            break;

            case '1019':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1019',
                    'data' => [
                        'info' => '元力添加失败'
                    ]
                ]);
            break;

            case '1020':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1020',
                    'data' => [
                        'info' => '获取不到url地址'
                    ]
                ]);
            break;

            case '1021':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1021',
                    'data' => [
                        'info' => '获取不到invitation邀请码'
                    ]
                ]);
            break;

            case '1022':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1022',
                    'data' => [
                        'info' => '邀请已达上限'
                    ]
                ]);
            break;

            case '1023':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1023',
                    'data' => [
                        'info' => 'task_name不存在'
                    ]
                ]);
            break;

            case '1024':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1024',
                    'data' => [
                        'info' => 'task_name不存在'
                    ]
                ]);
            break;

            case '1025':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1025',
                    'data' => [
                        'info' => '无法获取任务图标'
                    ]
                ]);
            break;

            case '1026':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1026',
                    'data' => [
                        'info' => '参数不全'
                    ]
                ]);
            break;

            case '1027':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1027',
                    'data' => [
                        'info' => '你今天已经签到过了'
                    ]
                ]);
            break;

             case '1028':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1028',
                    'data' => [
                        'info' => '非法请求'
                    ]
                ]);
            break;

            case '1029':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1029',
                    'data' => [
                        'info' => '该元石已过期'
                    ]
                ]);
            break;

            case '1030':
                return json_encode([
                    'msg' => 'error',
                    'code' => '1030',
                    'data' => [
                        'info' => '实名认证失败'
                    ]
                ]);
            break;
    	}
    }
}
