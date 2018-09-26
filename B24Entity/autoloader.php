<?php
require_once $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php";

Bitrix\Main\Loader::registerAutoLoadClasses(null, array(
  '\B24Entity\Commands\Orders'     => '/B24Entity/Commands/Orders.php',
  '\B24Entity\Commands\Contractor' => '/B24Entity/Commands/Contractor.php',
  '\B24Entity\Queries\Contractor'  => '/B24Entity/Queries/Contractor.php'
));