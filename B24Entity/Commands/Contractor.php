<?
namespace B24Entity\Commands;

use \B24Entity\Commands\Command,
    \B24Entity\Helpers\Logger;

class Contractor extends Command {

 private static $status_map = [
   "Активный"     => 27,
   "Пассивный"    => 28,
   "В разработке" => 29,
   "Закрыт"       => 30
 ];

 private static $errors = [];
 private static $contact_errors = [];

 public function execute($request) {

  if($request['CONFIRM']) {

     return $this->set1CExport($request['CONFIRM']);

  }

  if(!$request['CODE']) {

     return array('ERROR_REQUEST' => 'empty fields CODE');

  }

  $arCompany = $this->filter_empty(array(
     "UF_CRM_1522989078195" => trim($request['CODE']),
     "TITLE" => $request['TITLE'],
     "ASSIGNED_BY_ID"  => $request['MANAGER'], 
     "FM" => array("EMAIL" => array("n0" => array("VALUE" => $request['EMAIL'], "VALUE_TYPE" => "WORK" )),
                   "PHONE" => array("n0" => array("VALUE" => $request['PHONE'], "VALUE_TYPE" => "WORK" )),
                   "WEB"   => array("n0" => array("VALUE" => $request['WEB'], "VALUE_TYPE" => "WORK" ))),                  
     "UF_CRM_1526621027" => $request['UR_NAME'],
     "UF_CRM_1526621036" => $request['OF_NAME'],
     "UF_CRM_1526621055" => $request['MANAGER'],
     "UF_CRM_1522988414018" => $request['AVTOR'],
     "UF_CRM_1522988768425" => self::$status_map[$request['STAGE_ID']],
     "UF_CRM_1526620698"    => $request['MAIN_WORK'], 
     "UF_CRM_1522988985550" => $request['OTHER_WORK'], 
     "UF_CRM_1526620683"    => $request['PUBLIC_NAME'], 
     "UF_CRM_1526620246"    => $request['INN'],
     "UF_CRM_1526620255"    => $request['KPP'],
     "UF_CRM_1526620275"    => $request['OKPO'],
     "UF_CRM_1522995239232" => $request['ADDRESS'],
     "UF_CRM_1523208787417" => $request['UR_ADRESS'],
     "UF_CRM_1526620392"    => $request['DELIVERY_ADRESS'],
     "UF_CRM_1526620422"    => $request['DELIVERY_PERSON'],
     "UF_CRM_1526620474"    => $request['DELIVERY_PERSON_PHONE'],
     "UF_CRM_1526620487"    => $request['DOC_NUMBER'],
     "UF_CRM_1526620511"    => $request['DOC_START'],
     "UF_CRM_1526620524"    => $request['DOC_END'],
     "UF_CRM_1526620535"    => $request['DOC_CURRENCY'],
     "UF_CRM_1526620546"    => $request['DOC_TITLE'],
     "UF_CRM_1526620558"    => $request['PRICE_TYPE_DEFAULT'],
     "UF_CRM_1526620569"    => $request['DISCONT_DEFAULT'],
     "UF_CRM_1526620581"    => $request['CREDIT_TIME'],
     "UF_CRM_1526620591"    => $request['CREDIT_SUMM'],
     "UF_CRM_1526620603"    => $request['INVOICE_TITLE'],
     "UF_CRM_1526620614"    => $request['INVOICE_CURRENCY'],
     "UF_CRM_1526620624"    => $request['INVOICE_NUMBER'],
     "UF_CRM_1526620633"    => $request['BANK_TITLE'],
     "UF_CRM_1526977941"    => $request['CITY'],
     "COMMENTS"             => $request['COMMENT'],
     "UF_CRM_1522988344927" => $request['ASSORT'],
     "UF_CRM_1522988955752" => $request['ASSORT_OTHER'],
     "UF_CRM_1526622377"    => $request['STORED_PRODUCT'],
     "UF_CRM_1522988388281" => $request['HOLYDAY'],
     "UF_CRM_1522988644791" => $request['SOURCE'],
     "UF_CRM_1522988677868" => $request['BRAND'], 
     "UF_CRM_1522989013075" => $request['WORK'],
     "UF_CRM_1526620709"    => $request['IN_STOCK'],
     "UF_CRM_1522988890077" => $request['COUNT_STOCK'],
     "UF_CRM_1522988922783" => $request['TYPE_STOCK'],
     "UF_CRM_1522988400579" => $request['INFO'],
     "UF_CRM_1526622816"    => $request['BRAND_PRESENT'],
     "UF_CRM_1526622831"    => $request['REGION_SALE'],
     "UF_CRM_1526623415"    => $request['DATE_CREATE']
  ));

  $arContact = [];

  if($this->hasContact($request['CONTACT'])) {

    foreach($request['CONTACT'] as &$contact) {

      $contact['ASSIGNED_BY_ID']       = $request['MANAGER'];
      $contact['EXPORT']               = 'Y';
      $contact['UF_CRM_1522958763353'] =  $contact['TYPE_CONTACT'];
      $contact['UF_CRM_1522958816969'] =  $contact['PROPERTY'];  
      $contact['UF_CRM_1522958783273'] =  $contact['COMMUNICATION']; 
      $contact['UF_CRM_1522958860871'] =  $contact['IS_MAIN']; 
      $contact['UF_CRM_1522959041098'] =  $contact['FIO']; 
      $contact['UF_CRM_1534323400']    =  $contact['CODE_CONTACT'];

      unset($contact['TYPE_CONTACT']);
      unset($contact['PROPERTY']);
      unset($contact['COMMUNICATION']);
      unset($contact['IS_MAIN']); 
 
      $arContact[] = $contact;

    }
  }

  if($this->log_request()) {
   
     Logger::log($request);

  }

  $crm_company_id = $this->getCompany($request['CODE']);

  if(!$crm_company_id) {

    $added_company_id = $this->addCompany($arCompany);

    if($added_company_id) {

      foreach($arContact as &$contact) {

         $contact['COMPANY_ID']  = $added_company_id;

         $this->addContact($contact);

       }

       return array("RESPONSE" => "OK","CONTACT_RESULT" => self::$contact_errors ? : 'OK');

    }
  
    return array("RESPONSE_ERROR" => self::$errors);

  } else {
  
    foreach($arContact as &$contact) {

      if(!$ID = $this->getContact($contact['CODE_CONTACT'])) {

         $contact['COMPANY_ID']  = $crm_company_id; 
      
         $this->addContact($contact); 

      } else {

         $this->updateContact($ID, $contact);
          
      }
    }
   
    return $this->updateCompany($crm_company_id,$arCompany);
 
  }
 }

 private function addCompany(array $arCompany) {

   $crm_company = new \CCrmCompany(false);

   if($ID = $crm_company->Add($arCompany)) {

      return $ID;

   }
  
   self::$errors[] = $crm_company->LAST_ERROR;
  
   if($this->log_errors()) {

      Logger::log($crm_company->LAST_ERROR);

   }

   return false;

 }
 
 private function updateCompany($ID,array &$arCompany) {

  $company = new \CCrmCompany(false);
  $fields = $arCompany;

  if($this->isHasEmail($arCompany)) {

    $fields['FM']["EMAIL"][$this->getEmailID($ID, \CCrmOwnerType::CompanyName)] = array("VALUE" => $arCompany['FM']["EMAIL"]["n0"]["VALUE"], "VALUE_TYPE" => "WORK");

  }

  if($this->isHasPhone($arCompany)) {

    $fields['FM']["PHONE"][$this->getPhoneID($ID, \CCrmOwnerType::CompanyName)] = array("VALUE" => $arCompany['FM']["PHONE"]["n0"]["VALUE"], "VALUE_TYPE" => "WORK");

  }

  if($this->isHasWeb($arCompany)) {

   $fields['FM']["WEB"][$this->getWebID($ID, \CCrmOwnerType::CompanyName)]     = array("VALUE" => $arCompany['FM']["WEB"]["n0"]["VALUE"], "VALUE_TYPE" => "WORK" );

  }

  if(count($fields['FM']["EMAIL"]) > 1) {

    unset($fields['FM']["EMAIL"]["n0"]);

  }

  if(count($fields['FM']["PHONE"]) > 1) {

     unset($fields['FM']["PHONE"]["n0"]);
  }

  if(count($fields['FM']["WEB"]) > 1) {
 
    unset($fields['FM']["WEB"]["n0"]);

  }
 
  if(!$company->Update($ID,$fields)) {
  
    if($this->log_errors()) {
    
       Logger::log($company->LAST_ERROR);
  
     }

     return array("RESPONSE" => "ERROR","ERROR_CODE" => $company->LAST_ERROR);
     
  } 

  return array("RESPONSE" => "UPDATE OK","UPDATE_CONTACT" =>  self::$contact_errors);

 }

 private function addContact(array $arContact) {

   $crm_contact = new \CCrmContact(false);

   $arContact["FM"] = array(
                   "EMAIL" => array("n0" => array("VALUE" => filter_var(trim($arContact['EMAIL']),FILTER_SANITIZE_EMAIL), "VALUE_TYPE" => "WORK" )),
                   "PHONE" => array("n0" => array("VALUE" => trim($arContact['PHONE']), "VALUE_TYPE" => "WORK" ))
   );

   $arContact["TYPE_ID"]   = "CLIENT";
   $arContact["SOURCE_ID"] = "OTHER";

   [$name,$last_name,$second_name] = explode(" ",$arContact['FIO']);

   $arContact['NAME'] = $name;
   $arContact['LAST_NAME'] = $last_name;
   $arContact['SECOND_NAME'] = $second_name;

   if(!preg_match("/\d+\.\d+.\d+/is",$arContact['BIRTHDATE'])) {

      unset($arContact['BIRTHDATE']);

   }

   unset($arContact['EMAIL']);
   unset($arContact['PHONE']);
   unset($arContact['FIO']);

   if(!$crm_contact->Add($arContact)) {

      if($this->log_errors()) {

         Logger::log(array($arContact, $contact->LAST_ERROR));

      }

      self::$contact_errors[] = $crm_contact->LAST_ERROR;

      return false;
   }

   return true;
 }

 private function hasContact($entity) {
  
    return (is_array($entity) && sizeof($entity) > 0) ? true : false;

 }

 private function updateContact($ID,array $arContact) {

  $contact = new \CCrmContact(false);
  $fields = $arContact;

  if($this->isHasEmail($arContact)) {
    $fields['FM']["EMAIL"][$this->getEmailID($ID, \CCrmOwnerType::ContactName)] = array("VALUE" => filter_var(trim($arContact['EMAIL']),FILTER_SANITIZE_EMAIL), "VALUE_TYPE" => "WORK");
  }

  if($this->isHasPhone($arContact)) {
    $fields['FM']["PHONE"][$this->getPhoneID($ID, \CCrmOwnerType::ContactName)] = array("VALUE" => trim($arContact['PHONE']), "VALUE_TYPE" => "WORK");
  }

  [$name,$last_name,$second_name] = explode(" ",$arContact['FIO']);

  $fields['NAME'] = $name;
  $fields['LAST_NAME'] = $last_name;
  $fields['SECOND_NAME'] = $second_name;

  unset($arContact['EMAIL']);
  unset($arContact['PHONE']);
  unset($arContact['FIO']);

  unset($fields['ID']);

  if(count($fields['FM']["EMAIL"]) > 1) {

     unset($fields['FM']["EMAIL"]["n0"]);

  }

  if(count($fields['FM']["PHONE"]) > 1) {

     unset($fields['FM']["PHONE"]["n0"]);
  }

  if(!preg_match("/\d+\.\d+.\d+/is",$fields['BIRTHDATE'])) {

      unset($fields['BIRTHDATE']);

  }

  if(!$contact->Update($ID,$fields)) {
  
    if($this->log_errors()) {

       Logger::log($contact->LAST_ERROR);

    }

     self::$contact_errors[] = $contact->LAST_ERROR;

     return false;
     
  } 

  return true;

 }
 
 private function getCompany($code) {

   $company = \CCrmCompany::GetList(array("UF_CRM_1522989078195" => "DESC"),array("UF_CRM_1522989078195" => $code,"CHECK_PERMISSIONS" => "N"),array("ID"));

   $result = $company->Fetch();

   return $result['ID'] ? : false;

 }

 private function getContact($code) {

   $contact = \CCrmContact::GetList(array("UF_CRM_1534323400" => "DESC"),array("UF_CRM_1534323400" => $code,"CHECK_PERMISSIONS" => "N"),array("ID"));

   $result = $contact->Fetch();

   return $result['ID'] ? : false;

 }

 private function getEmailID($ID, $TYPE) {

   $rs = \CCrmFieldMulti::GetList(array(), array("ENTITY_ID"=> $TYPE,"TYPE_ID" => "EMAIL","ELEMENT_ID" => $ID)); 

   $fields = $rs->Fetch();

   return $fields['ID'] ? :  "n0";

 }

 private function getPhoneID($ID, $TYPE) {

   $rs = \CCrmFieldMulti::GetList(array(), array("ENTITY_ID"=> $TYPE ,"TYPE_ID" => "PHONE","ELEMENT_ID" => $ID)); 

   $fields = $rs->Fetch();

   return $fields['ID'] ? :  "n0";

 }

 private function getWebID($ID, $TYPE) {

   $rs = \CCrmFieldMulti::GetList(array(), array('ENTITY_ID' => $TYPE, "TYPE_ID" => "WEB","ELEMENT_ID" => $ID));
   
   $fields = $rs->Fetch();

   return $fields['ID'] ? :  "n0";

 }

 private function isHasEmail($entity) {

   if(array_key_exists('FM',$entity)) {

       return filter_var($entity['FM']["EMAIL"]["n0"]["VALUE"],FILTER_VALIDATE_EMAIL) ? true : false;
   }

   return filter_var($entity["EMAIL"],FILTER_VALIDATE_EMAIL)  ? true : false;
 }

 private function isHasPhone($entity) {

   if(array_key_exists('FM',$entity)) {

       return strlen($entity['FM']["PHONE"]["n0"]["VALUE"]) > self::$MIN_STRING_LENGTH ? true : false;
   }

   return strlen($entity["PHONE"]) > self::$MIN_STRING_LENGTH ? true : false;
 }

 private function isHasWeb($entity) {

   if(array_key_exists('FM',$entity)) {

       return strlen($entity['FM']["WEB"]["n0"]["VALUE"]) > self::$MIN_STRING_LENGTH ? true : false;
   }

   return strlen($entity["PHONE"]) > self::$MIN_STRING_LENGTH ? true : false;
 }

 private function set1CExport($id) {

  $company = new \CCrmCompany(false);

  $field = array("UF_CRM_1527066240" => 1);

  if($company->Update($id,$field)) {

      return array("RESPONSE" => "OK");
   
  }

  if($this->log_errors()) {
    
     Logger::log($company->LAST_ERROR);

  }

  return array("RESPONSE" => "ERROR","ERROR_CODE" => $company->LAST_ERROR);

 }

 private function log_errors() {
    return defined("CONTRACTOR_ERROR_LOG") && \CONTRACTOR_ERROR_LOG == 'Y' ? true : false;
 }

 private function log_request() {
    return defined("CONTRACTOR_REQUEST_LOG") && \CONTRACTOR_REQUEST_LOG == 'Y' ? true : false;
 }
}
?>