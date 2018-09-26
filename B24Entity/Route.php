<?php
namespace B24Entity;

class Route {

 private static $instance; 

 private $params;

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

 private function getParams() {

   return $this->params;

 }

 private function getRequest() {

   return json_decode(file_get_contents("php://input",FALSE),1);

 }

 private function resolve() {

   $query = $_REQUEST;

   $query_key = current(array_keys($query));

   $class_map = $this->getParams();
   
   foreach($class_map as $key=>$val) {
     
     if($key == $query_key) {

       $request = $query[$key];

       $command = $class_map[$key][$request];

       $reflection = new \ReflectionClass($command);
  
       return $reflection->newInstance();

       break;

     }

   }

   return new class {

    public function execute() {
    
      return array('ERROR'=>array('NOT_VALID_REQUEST'));

    }
   };
 }

 public function response(IResponse $r) {

    $entity = $this->resolve()->execute($this->getRequest());

    if($entity) {

       return $r->parse($entity); 

    }

    return false;  
 }

 private function __construct() {}

 private function __clone() {}
  
}
