<?php
namespace B24Entity\Commands;

use \B24Entity\Commands\Command,
    \B24Entity\Helpers\Logger;

class Orders extends Command  {

  use \B24Entity\Helpers\Contractor;

  private static $STAGES_NEW_CLIENT = [
   "Заявка"            => "PREPAYMENT_INVOICE",
   "Готов к отгрузке"  => 4,
   "Отгружено"         => 3,
   "Оплачено"          => 2
  ];

  private static $STAGES_OLD_CLIENT = [ 
   "Заявка"            => "C1:NEW",
   "Готов к отгрузке"  => 'C1:PREPARATION',
   "Отгружено"         => 'C1:EXECUTING',
   "Оплачено"          => 'C1:PREPAYMENT_INVOICE'
  ];

  private static $COMPANY_STAGES_OLD_CLIENT = [
   "Активный",    
   "Пассивный",    
   "Закрыт"      
  ];

  private static $COMPANY_STAGES_NEW_CLIENT = [
   "В разработке"   
  ];

  private static $ERRORS = [];

  const CATEGORY_DEAL_REGULAR_CLIENT = 1;

  public function execute($request) {

  $order_id = $request['TICKET_ID'];
 
  if(!$order_id) {

     return array('ERROR_REQUEST' => 'empty fields TICKET_ID');

  }

  if($this->log_request()) {

    Logger::log($request);
   
  } 
  
  $order           = $this->prepareOrder($request);

  $exists_order_id = $this->getOrderID($order_id);

  if($exists_order_id) {

    if(!$this->updateOrder($exists_order_id, $order)) {

       return array("RESPONSE_UPDATE_ERROR" => self::$ERRORS);

    }
   
    return array("RESPONSE_UPDATE" => "OK");

  } else {

    $order_id = $this->addOrder($order);

    if($order_id) {

      if(!$this->addProducts($order_id, $request['goods'])) {

         return array("RESPONSE_PRODUCT_ERROR" => \CCrmProductRow::GetLastError());

      }

      return array("RESPONSE" => "OK");

    }

    return array("RESPONSE_ERROR" => self::$ERRORS);

  }
 }

 private function prepareOrder(array $request) {

  $arCompany = array(
    "TITLE" => $request['COMPANY'],
    "UF_CRM_1522989078195" => $request['COMPANY_CODE'],
    "UF_CRM_1522988768425" => self::$COMPANY_STATUS_MAP[$request['COMPANY_STAGE']]
  );

  $arOrder = $this->filter_empty(array(
    "TITLE"                => trim($request['TICKET_ID']),
    "TYPE_ID"              => "SALE",
    "OPPORTUNITY"          => $request['ORDER_PRICE'],
    "UF_CRM_1523465881937" => $request['DOC_ID'],
    "UF_CRM_1523465906865" => $request['PROJECT'],
    "UF_CRM_1526379613"    => $request['DATE'],
    "UF_CRM_1526379650"    => $request['DOC_ID'],
    "UF_CRM_1534777837195" => $request['PREPAYMENT'],
    "ASSIGNED_BY_ID"       => $request['MANAGER'],
    "STAGE_ID"             => $this->getStatusID($request['COMPANY_STAGE'], $request['STAGE_ID']),
    "CATEGORY_ID"          => $this->getCategoryID($request['COMPANY_STAGE']),
    "COMPANY_ID"           => $this->getCompany($arCompany)
  ));

  return $arOrder;

 }

 private function getStatusID($company_status, $status) {

  if(!$status) {

     return false;

  }

  if(in_array($company_status, self::$COMPANY_STAGES_OLD_CLIENT) || $company_status == '') {

     return self::$STAGES_OLD_CLIENT[$status];

  } 

  return self::$STAGES_NEW_CLIENT[$status];

 }

 private function getCategoryID($company_status) {

  if(in_array($company_status, self::$COMPANY_STAGES_OLD_CLIENT) || $company_status == '') {

      return self::CATEGORY_DEAL_REGULAR_CLIENT;

  } 

  return false;

 }
 
 private function getCompany(array $arCompany) {

   return $this->getCompanyID($arCompany['COMPANY_CODE']) ? : $this->addCompany($arCompany);
  
 }

 private function addOrder(array $order) {

   $deal = new \CCrmDeal(false);

   if(!$ID = $deal->Add($order)) {

     self::$ERRORS[] = $deal->LAST_ERROR;

     if($this->log_errors()) {

        Logger::log(array($order, $deal->LAST_ERROR));
   
     } 

     return false;

   }

   return $ID;

 }

 private function getOrderID($title) {

   $deal = \CCrmDeal::GetList(array("TITLE" => "DESC"),array("TITLE" => trim($title), "CHECK_PERMISSIONS" => "N"),array("ID"));

   $result = $deal->Fetch();

   return $result['ID'] ? : false;

 }

 private function updateOrder($ID,$fields) {

   $deal = new \CCrmDeal(false); 

   unset($fields['COMPANY_ID']);

   if(!$deal->Update($ID,$fields)) {

     self::$ERRORS[] = $deal->LAST_ERROR;

     if($this->log_errors()) {

        Logger::log(array($fields, $deal->LAST_ERROR));

     }
  
     return false;
  }

  return true;

 }
  
 private function addProducts($deal_id, array $products) {

   $result = [];

   foreach($products as $item) {

     $result[] = array(
         "PRODUCT_NAME" => $item['NUMBER'],
         "PRICE"        => $item['PRICE'],
         "QUANTITY"     => (int)$item['QANTITY']
     );
     
   }

   if(!\CCrmProductRow::SaveRows('D', $deal_id, $result)) {

     if($this->log_errors()) {

        Logger::log(array($products, \CCrmProductRow::GetLastError()));

     }
       
     return false;

   }
 
   return true;

 }

 private function log_errors() {
   return defined("ORDER_ERROR_LOG") && \ORDER_ERROR_LOG == 'Y' ? true : false;
 }

 private function log_request() {
   return defined("ORDER_REQUEST_LOG") && \ORDER_REQUEST_LOG == 'Y' ? true : false;
 }

}

?>