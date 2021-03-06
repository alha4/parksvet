<?php
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
header("Content-type: application/json; charset=utf-8");
header('Cache-Control: no-cache, must-revalidate');

require_once $_SERVER['DOCUMENT_ROOT']."/B24Entity/autoloader.php";

use \B24Entity\Route,
    \B24Entity\JsonResponse;

/**
  логировать запросы и ошибки заказов
*/
const ORDER_REQUEST_LOG = 'Y';
const ORDER_ERROR_LOG = 'Y';
/**
  логировать запросы и ошибки контрагентов
*/
const CONTRACTOR_REQUEST_LOG = 'Y';
const CONTRACTOR_ERROR_LOG = 'Y';

/**
  логировать запросы товары
*/
const GOODS_REQUEST_LOG = 'Y';

/**
 максимальный размер лога в мегабайтах
*/
const MAX_LOG_SIZE = 1; 
/**
  свой путь к логу
  const LOG_PATH = '/your_path'; 
*/

try {

  $route = [
    'query' => [
        'orders'  => '\B24Entity\Commands\Orders',
        'company' => '\B24Entity\Commands\Contractor',
        'goods'   => '\B24Entity\Commands\Goods'
    ],
    'data' => [
        'contractor' => '\B24Entity\Queries\Contractor'
    ]
  ];

  $command  = Route::init($route);
  echo $command->response(new JsonResponse());

} catch(Error $err) {
 
  echo json_encode(array("Line" => $err->getLine(),'file' => $err->getFile(),'message' => $err->getMessage(),"trace" => $err->getTraceAsString()));

} catch(Exception $err) {

  echo json_encode(array("Line" => $err->getLine(),'file' => $err->getFile(),'message' => $err->getMessage(),"trace" => $err->getTraceAsString()));
}