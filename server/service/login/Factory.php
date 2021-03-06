<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2019/10/17
 * Time: 17:22
 */

namespace service\login;

/**
 * Class Factory
 * @package service\wechat
 * @method static WeChatThirdPartyLogin weChatLogin()
 * @method static SinaThirdPartyLogin sinaLogin()
 * @method static QqThirdPartyLogin qqLogin()
 */
class Factory {

  private static function make($name, $arguments) {
    $namespace = ucfirst($name);
    $app = "\\service\\login\\{$namespace}";
    return new $app;
  }

  public static function __callStatic($name, $arguments) {
    return self::make($name, $arguments);
  }
}