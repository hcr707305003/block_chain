<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('phoneregister', 'User\LoginController@phoneregister');  //手机获取验证码
Route::get('phoneexists', 'User\LoginController@phoneexists');  //手机注册
Route::get('phonelogin', 'User\LoginController@phonelogin');  //手机登录
Route::get('phonelogout', 'User\LoginController@phonelogout');  //手机退出登录
Route::get('retrieve_password', 'User\LoginController@retrieve_password');  //找回密码
Route::get('share_link', 'SharingController@share_link');  //分享链接
Route::get('scerweima', 'SharingController@scerweima');  //生成二维码图片
Route::get('get_invitation', 'SharingController@get_invitation');  //添加邀请次数
Route::get('wxsharing', 'SharingController@wxsharing');//微信分享接口
Route::get('invitation_friend', 'SharingController@invitation_friend');//邀请好友

Route::get('exists_token', 'User\LoginController@exists_token');//验证token是否存在
Route::get('sing_in', 'TaskController@sing_in');//签到
Route::get('login_record', 'HistoryController@login_record');//签到历史记录


Route::group([
    'namespace' => 'User',
],function (){
    //实名验证
    Route::get('realcheck', 'UserController@Realcheck');
    //         //我的钱包->元力总量
    // Route::get('orepower', 'StoneController@orePower');
    //我的钱包->元石总量
    Route::get('orenum', 'StoneController@oreNum');
    //元力排行
    Route::get('machineranking', 'StoneController@oreMachineRanking');
    //元石排行
    Route::get('oreranking', 'StoneController@oreRanking');
    //元石增加
    Route::get('incstone', 'StoneController@incStone');
    //元力增加
    Route::get('incpower', 'StoneController@incPower');
    //元石总数以及每日产出
    Route::get('stonetotal', 'StoneController@stoneTotal');
    //元石生成规则
    Route::get('generule', 'StoneController@geneRule');
     //元石总量和昨日矿石
    Route::get('yesterdaystone', 'StoneController@yesterdayStone');
        //任务列表展示
    Route::get('rasklist', 'UserController@raskList');
        //用户信息展示
    Route::get('userinfo', 'UserController@UserInfo');

});

Route::get('aa', 'StoneEncController@aa');//签到
