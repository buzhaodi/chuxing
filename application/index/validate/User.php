<?php
/**
 * Created by PhpStorm.
 * User: buzha
 * Date: 2017-06-20
 * Time: 14:05
 */
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'start'  => 'require',
        'age'   => 'number|between:1,120',
        'email' => 'email',
    ];

    protected $message  =   [
        'start.require' => '出发城市必填',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',
    ];
}