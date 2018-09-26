<?php
namespace B24Entity;

abstract class Route {

 private static $instance; 

 protected $params;

 public static function init(array $options) {

    if(is_null(self::$instance)) {

      self::$instance = new static();
      self::$instance->setParam($options);

    }

    return self::$instance;

 }

 private function setParam(array $params) {

    $this->params = $params;
     
 }

 protected function getParams() {

   return $this->params;

 }

 protected function getRequest() {

   return json_decode(file_get_contents("php://input",FALSE),1);

 }

 abstract protected function resolve();

 private function __construct() {}

 private function __clone() {}

}
?>