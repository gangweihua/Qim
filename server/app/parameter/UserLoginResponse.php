<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2019/12/10
 * Time: 19:02
 */

namespace api;


class UserLoginResponse extends Response {
  /**
   * @var int
   */
  public $result = 2;//0未找到该用户 1密码错误 2登录成功
  /**
   * @var string
   */
  public $token;
}