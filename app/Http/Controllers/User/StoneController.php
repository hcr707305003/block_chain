<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\CodeController as code;
use App\Http\Controllers\StoneEncController;
use App\Http\Controllers\TokenController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StoneController extends TokenController
{
    protected $key = 'Stopr';   //加密K值

    public function __construct(Request $request)
    {
        $this->msg = parent::exists_token($request);
    }

    /**
     * 个人元石总量
     */
    public function oreNum(Request $request)
    {
        if ($this->msg !== '{"msg":"success"}') {
            return code::code(1004);
        }
        $data = $request->all();
        if (empty($data['token'])) {
            return code::code(1017);
        }
        $uid = DB::table('admin_visit')->where('token', '=', $data['token'])->first();  //通过token拿到UID
        $stoneNum = DB::table('admin_main_property')->where('uid', '=', $uid->u_id)->value('total_stone'); //查询该用户当前的元石

        if ($stoneNum == null) {
            return json_encode([
                'msg' => 'success',
                'data' => [
                    'num' => 0
                ]
            ]);
        } else {
            return response()->json([
                'msg' => 'success',
                'data' => [
                    'num' => $stoneNum
                ]
            ]);
        }
    }
    //个人元力总量
    public function orePower(Request $request)
    {
        if ($this->msg !== '{"msg":"success"}') {
            return code::code(1004);
        }
        $data = $request->all();
        if (empty($data['token'])) {
            return code::code(1017);
        }
        $uid = DB::table('admin_visit')->where('token', '=', $data['token'])->first();  //通过token拿到UID
        $powerNum = DB::table('admin_main_property')->where('uid', '=', $uid->u_id)->value('total_power'); //查询该用户当前的元石

        if ($powerNum == null) {
            return json_encode([
                'msg' => 'success',
                'data' => [
                    'power' => 0
                ]
            ]);
        } else {
            return response()->json([
                'msg' => 'success',
                'data' => [
                    'power' => $powerNum
                ]
            ]);
        }
    }

    //元力的排行榜(用户的元力名次)
    public function oreMachineRanking(Request $request)
    {
        if ($this->msg !== '{"msg":"success"}') {
            return code::code(1004);
        }
        $data = DB::table('admin_reception_users as u')
            ->leftJoin('admin_main_property as m', 'u.id', '=', 'm.uid')
            ->select('u.id', 'u.username', 'm.total_power', 'm.total_stone')
            ->orderBy('total_power', 'desc')
            ->limit(10)
            ->get();
        if ($data) {
            return response()->json([
                "msg" => "success",
                "data" => $data
            ]);
        }
    }

    //元石的排行榜
    public function oreRanking()
    {
        $data = DB::table('admin_reception_users as u')
            ->leftJoin('admin_main_property as m', 'u.id', '=', 'm.uid')
            ->select('u.id', 'u.username', 'm.total_stone', 'm.total_power')
            ->orderBy('total_stone', 'desc')
            ->limit(10)
            ->get();
        if ($data) {
            return response()->json([
                "msg" => "success",
                "data" => $data
            ]);
        }
    }

    /**
     *元石增加
     */
    public function incStone(Request $request)
    {
        if ($this->msg !== '{"msg":"success"}') {
            return code::code(1004);
        }
        $data = $request->all();
        if (empty($data['token'])) {
            return code::code(1017);
        }
        if (empty($data['stone'])) {
            return code::code(1018);
        }
        dump($data['stone']);
        ############解密##########################################################
        $dec = (new StoneEncController())->StoneDec($data['stone']);  //解密     #
        $arr = explode('^', $dec);  //炸开成数组                       #
        $str = str_replace(',', '', $arr[0]); //把逗号替换成空    #
        $str_md5 = $arr[1];                                                     #
        $base_md = md5($str . $this->key);  //MD5加密                       #
        #########################################################################

        $uid = DB::table('admin_visit')->where('token', '=', $data['token'])->first();  //通过token拿到UID
        $stoneKU = DB::table('admin_stone_total')->orderBy('id', 'desc')->first(); //拿到总元石库表数据


        #################当日该领取######剩余未领取###############
        if ($stoneKU->Reserved == null) {
            $day_stone = $stoneKU->day_stone;   //当前派发的元石量
        } else {
            $day_stone = $stoneKU->Reserved;   //当前拍完剩余的元石量
        }

        $stoneNum = DB::table('admin_main_property')->where('uid', '=', $uid->u_id)->value('total_stone');  //资产主表
        if ($base_md == $str_md5) {
            $str_dec = $arr[0];  //获取base 解密 数据

            $stone_data = explode(',', $str_dec);  //逗号炸开
            if ((time() - $stone_data[1]) > 5 * 60) {  //对比时间 ..是否失效 5分钟
                return code::code(1029);
            } else {  //没有失效入库
                $admin_stone = DB::table('admin_stone')->where('uid', '=', $uid->u_id)->orderBy('created_at', 'desc')->first();  //查询最新一条日志数据
                $data_arr['uid'] = $admin_stone->uid;
                $data_arr['appid'] = $admin_stone->appid;
                $data_arr['resid'] = $admin_stone->resid;
                $data_arr['actid'] = $admin_stone->actid;
                $data_arr['stone'] = $stone_data[0];  //新增的元石量
                $data_arr['stone_balance'] = $admin_stone->stone_balance + $stone_data[0];   //元石总量
                $data_arr['created_at'] = date('Y-m-d H:i:s', time());  //创建时间
                $data_arr['updated_at'] = date('Y-m-d H:i:s', time());  //修改时间
                $data = DB::table('admin_stone')->insert($data_arr);  //插入数据库

                ####入住资产表之前.处理元石##########################################
                $Reserved = $day_stone - $stone_data[0]; //每增加一次减一定额度的元石
                $stone_Reserved = DB::table('admin_stone_total')->where('id', '=', $stoneKU->id)->update(['Reserved' => $Reserved]);  //修改里面的参数
                #####################################################################################################################
                if ($data == true && $stone_Reserved == true) {//如果日志表加入成功并且总矿石表修改成功,主资产表同时加入
                    $stone_stone['total_stone'] = $stoneNum + $stone_data[0];
                    $stone_stone['updated_at'] = date('Y-m-d H:i:s', time());
                    $incStone = DB::table('admin_main_property')->where('uid', '=', $uid->u_id)->update($stone_stone);  //资产主表
                    if ($incStone) {
                        return response()->json([
                            "msg" => "success",
                            "data" => $stoneNum
                        ]);
                    }
                }
            }
        } else {
            return code::code(1017);  //非法登录
        }
    }

//    /**
//     * 元力增加
//     */
//    public function incPower(Request $request)
//    {
//        if ($this->msg !== '{"msg":"success"}') {
//            return code::code(1004);
//        }
//        $data = $request->all();
//        if (empty($data['token'])) {
//            return code::code(1017);
//        }
//        if (empty($data['total_power'])) {
//            return code::code(1026);
//        }
//        $uid = DB::table('admin_visit')->where('token', '=', $data['token'])->first();  //通过token拿到UID
//
//        $powerNum = DB::table('admin_main_property')->where('uid', '=', $uid->u_id)->value('total_power');
//
//        $admin_power = DB::table('admin_power')->where('uid', '=', $uid->u_id)->orderBy('created_at', 'desc')->first();
//        if ($data['token'] == $uid->token) {
//            $data_arr['uid'] = $admin_power->uid;
//            $data_arr['appid'] = $admin_power->appid;
//            $data_arr['resid'] = $admin_power->resid;
//            $data_arr['actid'] = $admin_power->actid;
//            $data_arr['power'] = $data['total_power'];
//            $data_arr['created_at'] = date('Y-m-d H:i:s', time());
//            $data_arr['updated_at'] = date('Y-m-d H:i:s', time());
//            $data_arr['power_balance'] = $admin_power->power_balance + $data['total_power'];
//            $result = DB::table('admin_power')->insert($data_arr);
//            if ($result) {
//                $data_power['total_power'] = $powerNum + $data['total_power'];
//                $data_power['updated_at'] = date('Y-m-d H:i:s', time());
//                $incPower = DB::table('admin_main_property')->where('uid', '=', $uid->u_id)->update($data_power);  //资产主表
//                if ($incPower) {
//                    return response()->json([
//                        "msg" => "success"
//                    ]);
//                }
//            } else {
//                return code::code(1030);
//            }
//        }
//    }

    /*
     * 元石总数,以及每日产出
     */
    public function stoneTotal(Request $request)
    {
        if ($this->msg !== '{"msg":"success"}') {
            return code::code(1004);
        }
        $day_stone = 1000000; //元石每天产量
        $stone_total = DB::table('admin_stone_total')->first();  //查询

        //如果数据库没数据.创建数据.元石
        if ($stone_total == null) {
            $arr = [];
            $arr['total_stone'] = 5000000000; //50Y 元石
            $arr['day_stone'] = $day_stone; //50Y 元石
            $arr['remain_stone'] = $arr['total_stone'] - $day_stone; //50Y 元石
            $arr['created_at'] = date('Y-m-d H:i:s', time());
            $arr['updated_at'] = date('Y-m-d H:i:s', time());
            $data = DB::table('admin_stone_total')->insert($arr);
            if ($data) {
                unset($arr['updated_at'], $arr['created_at']);
                $arr['num'] = 5000000000;
                return response()->json([
                    "msg" => "success",
                    "data" => $arr
                ]);
            }
        }
        //查询库存数据.降序查询
        $stone_total = DB::table('admin_stone_total')->orderBy('id', 'desc')->limit(1)->first();

        ###############################################################################################################
        //拿到最初创建的时间
        $stone_time = DB::table('admin_stone_total')->where('total_stone', '=', 5000000000)->select('created_at')->first();
        $time = strtotime("$stone_time->created_at+2year");  //一年后时间,发矿石简易版变50W
        //当前时间是否大于入库时间.
        if ($time < time()) {     //二年后
            $day_stone = $day_stone / 2;   //元石减半
        }
        ###############################################################################################################
        //拿出昨天没有分配完的元石
        if ($stone_total->Reserved == null) {
            $Reserved = 0;  //空等于 0
        } else {
            $Reserved = $stone_total->Reserved;  //不等于0的情况下
        }

        //剩余元石减去当日元石 ==等于下次分配的元石,,,先把昨天没分配完的元石+入到总量中,之后再减去 日产元石
        $remain_stone = ($stone_total->remain_stone + $Reserved) - $day_stone;


        if ($stone_total->remain_stone <= $day_stone) {
            $remain_stone = $stone_total->remain_stone - $stone_total->remain_stone;
            $data_stone['total_stone'] = $stone_total->remain_stone;
            $data_stone['day_stone'] = $stone_total->remain_stone;
            $data_stone['remain_stone'] = $remain_stone;
            $data_stone['created_at'] = date('Y-m-d H:i:s', time());
            $data_stone['updated_at'] = date('Y-m-d H:i:s', time());

            $result = DB::table('admin_stone_total')->insert($data_stone);
            if ($result) {
                unset($data_stone['updated_at'], $data_stone['created_at']);
                $data_stone['num'] = 5000000000;
                return response()->json([
                    "msg" => "success",
                    "data" => $data_stone
                ]);
            }
        }

        $data_stone['total_stone'] = $stone_total->remain_stone;
        $data_stone['day_stone'] = $day_stone;
        $data_stone['remain_stone'] = $remain_stone;
        $data_stone['created_at'] = date('Y-m-d H:i:s', time());
        $data_stone['updated_at'] = date('Y-m-d H:i:s', time());

        //当剩余元石少于 给定元石的时候
        $user_date_time = strtotime($stone_total->created_at);
        $Tomorrow = strtotime(date('Y-m-d', time() + 60 * 60 * 24));
        $Yesterday = strtotime(date('Y-m-d', time()));

        if ($user_date_time > $Yesterday && $user_date_time < $Tomorrow) {  //当前时间减去创建时间  大于1天的时候.插入.
            $result = DB::table('admin_stone_total')->insert($data_stone);
            if ($result) {
                unset($data_stone['updated_at'], $data_stone['created_at']);
                $data_stone['num'] = 5000000000;
                return response()->json([
                    "msg" => "success",
                    "data" => $data_stone
                ]);
            }
        }
    }

    /*
     * 元石生成规则
     * 假设每日发放元石总数C，用户每日领取到的元石=C*该用户当前元力值/所有用户元力值之和
     */
    public function geneRule(Request $request)
    {
        if ($this->msg !== '{"msg":"success"}') {
            return code::code(1004);
        }
        if (!$request->token) {
            return code::code(1004);
        }
        $uid = DB::table('admin_visit')->where('token', '=', $request->token)->first();  //通过token拿到UID
        $dataUser = DB::table('admin_main_property')->where('uid', '=', $uid->u_id)->first();   //查询该用户的资产
        $day_stone = DB::table('admin_stone_total')->orderBy('id', 'desc')->limit(1)->first(); //获取日产矿石

        ###############################################当日生产矿石
        if ($day_stone->Reserved == null) {
            $day_stone = $day_stone->day_stone;  //如果没有.当日剩余元石  //那么直接使用当日配发元石
        } else {
            $day_stone = $day_stone->Reserved;  //如果有,剩余元石,那么直接使用它
        }
        ###############################################


        if ($day_stone !== null) {
            $data = DB::table('admin_main_property')->sum('total_power');  //所有用户总元力和

            if ($data) {
                $result = $day_stone * $dataUser->total_power / $data;  //元石生成规则
                //分成10份规则
                $div = 10;
                $total = $result;
                $a = range(0, $div - 1);  //数组
                $base = $total / $div;

                for ($i = 0; $i < count($a); $i++) {
                    $a[$i] = $data = sprintf("%.5f", $base);
                }
                shuffle($a); //打乱排序

//                $div = 10;
//                $total = $result;
//                $a = range(0, $div - 1);
//                $base = ($total - array_sum($a)) / $div;
//
//                for ($len = count($a), $i = 0; $i < $len; $i++) {
//                    $aa = explode('.', $base);
//                    $a[$i] += $aa[0] . "." . substr($aa[1], 0, 5); //取小数点后五位;
//                }
//                shuffle($a); //打乱排序

                $data = [];
                $data['power'] = $dataUser->total_power;
                $data['stone'] = $dataUser->total_stone;
                $data['total'] = $result;
                $data_base = [];
                foreach ($a as $key => $value) {
                    $enc = (new StoneEncController())->StoneEnc($value, $this->key);
                    $data_base[] = [
                        'enc' => $enc,
                        'value' => $value,
                    ];
                    /*$data_base['data'.$key] = $value;
                    $data_enc['enc'.$key] = $enc;*/
                }
                return response()->json([
                    "msg" => "success",
                    "data" => [
                        'data' => $data,
                        'number' => $data_base,
                    ]
                ]);
            } else {
                return code::code(1028);
            }

        } else {
            return code::code(1028);
        }
    }

    /**
     * 元石总产量 和 昨日发放元石
     */
    public function yesterdayStone()
    {
        $data = DB::table('admin_stone_total')->orderBy('created_at', 'desc')->limit(2)->get();

        if (count($data) == 2) {
            return response()->json([
                "msg" => "success",
                "data" => $data[1]
            ]);
        } else {
            return response()->json([
                "msg" => "success",
                "data" => $data
            ]);
        }
    }
}
