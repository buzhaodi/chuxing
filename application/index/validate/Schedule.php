<?php
/**
 * Created by PhpStorm.
 * User: buzha
 * Date: 2017-06-20
 * Time: 14:05
 */
namespace app\index\validate;

use think\Validate;

class Schedule extends Validate
{
    protected $rule =   [
        'startcity'=>'require',
        'startcounty'=>'require',
        'tocity'=>'require',
        'tocounty'=>'require',
        'placeofdeparture'=>'require',
        'destination'=>'require',
        'time'=>'require|number',
        'road'=>'require|min:20',
        'money'=>'require|number',
        'car'=>'require',
        'seat'=>'require',
        'preson'=>'require'


    ];

    protected $message  =   [
        'time.require' => '请选择出发时间',
        'time.number' => '请选择出发时间',
        'road.require'=>'请详细填写路线 至少20个字呦',
        'road.min'=>'请详细填写路线 至少20个字呦',
        'money.require'=>'请填写价格 注意,请不要带元 栗子:40',
        'money.number'=>'请填写价格 注意,请不要带元 栗子:40',
        'car.require'=>'填写车型呦 栗子:比亚迪速锐',
        'seat.require'=>'选择作为呦',
        'startcity.require'=>'您输入的位置尚未开通服务或没有选择,请点击出 发地按 钮选择',
        'startcounty.require'=>'您输入的位置尚未开通服务或没有选择,请点击 出发地 按钮选择',
        'tocity.require'=>'您输入的位置尚未开通服务或没有选择,请点击 目的地 按钮选择',
        'tocounty.require'=>'您输入的位置尚未开通服务或没有选择,请点击 目的地 按钮选择',
        'placeofdeparture.require'=>'出发地 必填',
        'destination.require'=>'目的地 必填',
        'preson.require'=>'您还未绑定手机号码 请先绑定手机号码在操作'
    ];
}