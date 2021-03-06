<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2019/9/23
 * Time: 10:48
 */

namespace tiny;


class Response {
  function __construct() {
    $this->httpStatus = HttpStatus::SUC;
  }

  public $data;
  public $httpStatus;
  public $httpStatusMsg;
  public $httpHeaders = [
    'Access-Control-Allow-Origin' => '*',
    'Access-Control-Allow-Headers' => 'Referer,Origin, Content-Type, Cookie, Accept,User-Agent',
    'Access-Control-Allow-Methods' => 'POST',
    'Access-Control-Allow-Credentials' => 'true'
  ];
}