<?php
namespace B24Entity\Commands;

use Bitrix\Main\Loader;

Loader::IncludeModule("crm");

abstract class Command {

 protected static $MIN_STRING_LENGTH = 3;

 protected function filter_empty(array $array) {

   return array_filter($array, function($value) {

    if(($value && strlen($value) > 0 && $value != ' ') || is_array($value)) {
  
      return true;

    }

    return false;

   });

 }
 
 abstract function execute($request);
}

