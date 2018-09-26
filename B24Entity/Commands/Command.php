<?php
namespace B24Entity\Commands;

use Bitrix\Main\Loader;

Loader::IncludeModule("crm");

abstract class Command {

 const MAX_LOG_SIZE = 3;

 const DEFAULT_LOG_FILE  = '/log.txt';

 protected static $MIN_STRING_LENGTH = 3;

 abstract function execute($request);

 protected function filter_empty(array $array) {

   return array_filter($array, function($value) {

    if(($value && strlen($value) > 0 && $value != ' ') || is_array($value)) {
  
      return true;

    }

    return false;

   });

 }

 protected function log($mess) { 

   $path = constant("LOG_PATH")  ? $_SERVER['DOCUMENT_ROOT'].constant("LOG_PATH") : $_SERVER['DOCUMENT_ROOT'].self::DEFAULT_LOG_FILE;

   $file_size = (int)(filesize($path) / 1024 / 1024);

   $max_log_size = constant("MAX_LOG_SIZE") ? : self::MAX_LOG_SIZE;
   
   if($file_size > $max_log_size) {
    
     file_put_contents($path, '');

   }

   file_put_contents($path, print_r($mess,1).date("d.m.Y H:i:s"),FILE_APPEND);
 
 }
}
?>

