<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2019/10/4
 * Time: 21:15
 */

namespace tiny;

class Main {
  //目标路由
  private $redirect_url;

  private $path_info;

  public function run() {
    ini_set('date.timezone', 'Asia/Shanghai');
    ini_set('display_errors','Off');
    error_reporting(E_ALL);

    $softWare = $_SERVER['SERVER_SOFTWARE'];

    //兼容Apache以及Nginx
    $_SERVER['API_URI'] = strstr($softWare, 'Apache')
      ? $_SERVER['PATH_INFO']
      : $_SERVER['REQUEST_URI'];

    if (!isset($_SERVER['API_URI'])) {
      http_response_code(HttpStatus::NOT_FOUND);
      Logger::getInstance()->error('404 API NOT FOUND');
      return;
    }
    $this->path_info = $_SERVER['API_URI'];
    $this->redirect_url = str_replace('/', '\\', $this->path_info);
    try {
      $reflection = new \ReflectionClass($this->redirect_url);
    } catch (\ReflectionException $e) {
      http_response_code(HttpStatus::NOT_FOUND);
      Logger::getInstance()->error('404 API NOT FOUND');
      return;
    }

    //判断请求类型
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
      http_response_code(HttpStatus::FAILED);
      Logger::getInstance()->error('目前仅支持POST,这是一个错误的请求方式');
      return;
    }

    set_error_handler(function ($errno, $errStr, $errFile, $errLine) {
      /**
       * 错误控制运算符：@ 标注的忽略错误不再输出
       */
      if (error_reporting() === 0) {
        return false;
      }
      throw new \ErrorException($errStr, 0, $errno, $errFile, $errLine);
    });


    try {
      Logger::getInstance()->info('start');

      $request = new Request();


      if (json_decode(file_get_contents("php://input"))) {
        $data = toArray(json_decode(file_get_contents("php://input")));
      } else {
        $data = array_merge($_POST, $_FILES);
      }

      $request->data = new \stdClass();

      if ($data) {
        foreach ($data as $k => $v) {
          $request->data->{$k} = $v;
        }
      }

      if (isset($request->data->token)) {
        $request->token = $request->data->token;
        unset($request->data->token);
      }


      $response = new Response();
      $reflection->newInstanceArgs()->process($request, $response);

      // ------ response -------
      Logger::getInstance()->info("end");

      //返回客户端信息
      http_response_code($response->httpStatus);
      if ($response->httpStatus == HttpStatus::SUC) {
        foreach ($response->httpHeaders as $header => $value) {
          header($header . ': ' . $value);
        }
        echo json_encode($response->data);
      }
    } catch (\Exception $e) {
      $errorCode = $e->getCode() == HttpStatus::DEFAULT ? HttpStatus::FAILED : $e->getCode();
      http_response_code($errorCode);
      Logger::getInstance()->fatal($e);
    }
  }
}
