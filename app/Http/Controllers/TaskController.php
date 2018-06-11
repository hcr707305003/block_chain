<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\CodeController as code;
use App\Http\Controllers\TokenController as token;
use App\Http\Controllers\ATOBController as a_to_b;
class TaskController extends TokenController
{

    //引入父类方法
    public function __construct(Request $request)
    {
        \DB::connection()->enableQueryLog(); // 开启查询日志 
        $this->msg = parent::exists_token($request);
    }

    //开启数据库调试模式查询最后插入的语句
    public function getLastSql($sql)
    {
        $queries = \DB::getQueryLog(); // 获取查询日志  
        return $queries[0]['query']; // 即可查看执行的sql 
    }


	/*
	 * 新增任务
	 * param@ name(varchar<50)任务名  describe(varchar<255)描述  state(tinyint)状态 img_src(varchar255)项目图标
	 * 
	 */
    public function add_task(Request $request)
    {
    	$data = $request->all();
    	$arr = array();
    	if (!$data['name']) {//任务名称
    		code::code('1023');
    	}

    	if ($data['describe']) {//任务描述
    		$arr['describe'] = $data['describe'];
    	}

    	if ($data['state']) {//如果不存在默认为1
    		$arr['state'] = $data['state'];
    	}

    	if (!$data['img_src']) {//任务图标
    		code::code('1025');
    	}

    	if ($data['type']) {//任务触发类型，不存在默认为1,1为click
    		$arr['type'] = $data['type'];
    	}

    	if (!$data['power']) {//设置该任务奖励多少元力值
    		code::code('1018');
    	}

    	if ($data['check_code']) {//检测行为状态码
    		$arr['check_code'] = $data['check_code'];
    	}

    	if (!$data['url']) {//跳转的地址url
    		$arr['url'] = $data['url'];
    	}

    	$get_insert_id = DB::table('admin_app')->insertGetId($arr);

    	if ($get_insert_id) {
            return json_encode([
                'msg' => 'success'
            ]);	
    	}
    }


    /*
     * 签到
     * 
     */
    function sing_in(Request $request) {

        $data = $request->all();
        if (json_decode($this->msg)->msg != 'success') {
            return code::code(1004);
        }
        $user_data = parent::get_header_token_uid();
        $get_newest_data = $this->get_task($user_data['uid']);

        // var_dump($get_newest_data);die;
        //判断元石
        switch ($get_newest_data['admin_stone']['appid']) {
            case 1:
                //判断今天是否签到
                $user_date_time = strtotime($get_newest_data['admin_stone']['created_at']);
                $Tomorrow = strtotime(date('Y-m-d', time()+60*60*24));
                $Yesterday = strtotime(date('Y-m-d', time()));
                // echo date('Y-m-d H:i;s', $Yesterday);
                if ($user_date_time > $Yesterday && $user_date_time < $Tomorrow) {
                    return code::code('1027');
                }
                if ($get_newest_data['admin_stone']) {
                    //获取用户最新的元石数量
                    $user_stone = $get_newest_data['admin_stone']['stone_balance'];
                    //获取用户最新的元力数量
                    $user_power = $get_newest_data['admin_power']['power_balance'];
                    //获取到增加元力的数量
                    if (isset($get_newest_data['admin_power']['power_stone'])) {
                        $power = $get_newest_data['admin_power']['power_stone'];
                        //增加到元石记录表
                        $total_power = intval($user_power)+intval($power);
                    } else {
                        $total_power = $user_power;
                    }
                    //获取到增加元石的数量
                    if (isset($get_newest_data['admin_stone']['power_stone'])) {
                        $stone = $get_newest_data['admin_stone']['power_stone'];
                        //增加到元石记录表
                        $total_stone = intval($user_stone)+intval($stone);
                    } else {
                        $total_stone = $user_stone;
                    }
                    $arr = array(
                        'uid' => $user_data['uid'],
                        'appid' => $get_newest_data['admin_stone']['appid'],
                        'resid' => $get_newest_data['admin_stone']['resid'],
                        'actid' => $get_newest_data['admin_stone']['actid'],
                        'stone' => $stone,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time()),
                        'stone_balance' => $total_stone,
                    );
                    if (DB::table('admin_stone')->insert($arr)){
                        $arr = array(
                            'total_stone' => intval($total_stone),
                            'total_power' => intval($total_power),
                            'created_at' => date('Y-m-d H:i:s', time()),
                            'updated_at' => date('Y-m-d H:i:s', time()),
                        );
                        $get_user = DB::table('admin_main_property')->where('uid', '=', $user_data['uid'])->first();
                        if ($get_user) {
                            if (DB::table('admin_main_property')->where('uid', '=', $user_data['uid'])->update($arr)) {
                                return json_encode([
                                    'msg' => 'success',
                                    'content' => '签到成功！',
                                ]);
                            } else {
                                return code::code('1002');
                            }
                        } else {
                            $arr['uid'] = $user_data['uid'];
                            if (DB::table('admin_main_property')->insert($arr)) {
                                return json_encode([
                                    'msg' => 'success',
                                    'content' => '签到成功！',
                                ]);
                            } else {
                                return code::code('1002');
                            }
                        }
                    } else {
                        return code::code('1002');
                    }
                }
            break;
            
            case 2:
                // echo 2222;
            break;

            case 3:
                // echo 3333;
            break;
        }

    }


    /*
     * 根据uid获取到这个用户的元石以及行为
     * 查询出这个用户的最新的数据
     * 
     */
    static function get_task($uid = "", $limit=1) {
        $arr = array();
        //查询改用户最新的一条元石记录
        $admin_stone = DB::table('admin_stone')->where('uid', '=', $uid)->orderby('created_at', 'desc')->first();
        $a_to_b = new a_to_b();

        //判断用户元石的获得是触发了什么行为
        if ($admin_stone) {
            $admin_stone = $a_to_b->object_to_array($admin_stone);

            if ($admin_stone['appid'] > 0) {
                if ($limit == 1) {
                    $admin_app = DB::table('admin_app')->where('app_id', '=', $admin_stone['appid'])->where('state', '=', 1)->where('exists_power_stone', '=', '2')->first(['name', 'describe', 'img_src', 'type', 'power_stone', 'check_code', 'url']);
                } else {
                    $admin_app = DB::table('admin_app')->where('app_id', '=', $admin_stone['appid'])->where('state', '=', 1)->where('exists_power_stone', '=', '2')->limit($limit)->get(['name', 'describe', 'img_src', 'type', 'power_stone', 'check_code', 'url']);
                }
                $admin_app = $a_to_b->object_to_array($admin_app);
            }
        }
        //融合数组
        $array = array_merge($admin_stone, $admin_app);
        $arr['admin_stone'] = $array;
        //查询出该用户的最新的数据
        $admin_power = DB::table('admin_power')->where('uid', '=', $uid)->orderby('created_at', 'desc')->first();

        //判断用户元力的获得是触发了什么行为
        if ($admin_power) {
            $admin_power = $a_to_b->object_to_array($admin_power);

            if ($admin_power['appid'] > 0) {
                if ($limit == 1) {
                    $admin_app = DB::table('admin_app')->where('app_id', '=', $admin_power['appid'])->where('state', '=', 1)->where('exists_power_stone', '=', 1)->first();
                } else {
                    $admin_app = DB::table('admin_app')->where('app_id', '=', $admin_power['appid'])->where('state', '=', 1)->where('exists_power_stone', '=', 1)->limit($limit)->get();
                }
                $admin_app = $a_to_b->object_to_array($admin_app);
            }
        }
        $array = array_merge($admin_power, $admin_app);
        $arr['admin_power'] = $array;
        return $arr;
    }
}   
