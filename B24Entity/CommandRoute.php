<?php
namespace B24Entity;

use B24Entity\Route;

final class CommandRoute extends Route {

  protected function resolve() {

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
}
?>