<?
namespace B24Entity\Queries;

class Contractor  {

 private $uf_manager;

 private static $status_map = [
   27 => "Активный", 
   28 => "Пассивный", 
   29 => "в Разработке",
   30 => "Закрыт"    
 ];

 public function __construct() {

   $this->uf_manager = new \CUserTypeManager();

 } 
 /**

 params UF_CRM_1527066240 boolean : не выгружен в 1с

 */
 
 public function execute($request) {

  $contractors = [];

  $sort   = array('DATE_CREATE'=>'DESC');

  $filter = array("CHECK_PERMISSIONS" => "N", "UF_CRM_1527066240" => 0);

  $fields = array("TITLE","COMMENTS");

  $companys = \CCrmCompany::GetList($sort, $filter, $fields, 50);

  while($arCompany = $companys->Fetch()) {

    $ID = $arCompany['ID'];

    $arCompany['CODE']         = $this->getCode($ID);

    $arCompany['STATUS']       = $this->getStatus($ID);

    $arCompany['DATE_CREATE']  = $this->getDateCreate($ID);

    $arCompany['PHONE']        = $this->getCompanyPhone($ID);

    $arCompany['EMAIL']        = $this->getCompanyEmail($ID);

    $arCompany['WEB']          = $this->getWebSite($ID);

    $arCompany['ADDRESS']      = $this->getAddress($ID);

    $arCompany['UR_NAME']      = $this->getURTitle($ID);

    $arCompany['OF_NAME']      = $this->getOFTitle($ID);

    $arCompany['MANAGER']      = $this->getManager($ID);

    $arCompany['AVTOR']        = $this->getAvtor($ID);

    $arCompany['INN']          = $this->getINN($ID);

    $arCompany['KPP']          = $this->getKPP($ID);

    $arCompany['OKPO']         = $this->getOKPO($ID);

    $arCompany['UR_ADDRESS']   = $this->getURAddress($ID);

    $arCompany['DELIVERY_ADRESS']   = $this->getDeliveryAddress($ID);

    $arCompany['DELIVERY_PERSON']   = $this->getDeliveryPerson($ID);

    $arCompany['DELIVERY_PERSON_PHONE'] = $this->getDeliveryPersonPhone($ID);

    $arCompany['DOC_NUMBER'] = $this->getDocNumber($ID);

    $arCompany['DOC_START'] = $this->getDocStart($ID);

    $arCompany['DOC_END'] = $this->getDocEnd($ID);

    $arCompany['DOC_CURRENCY'] = $this->getDocCurrency($ID);

    $arCompany['DOC_TITLE'] = $this->getDocTitle($ID);

    $arCompany['PRICE_TYPE_DEFAULT'] = $this->getPriceTypeDefault($ID);

    $arCompany['DISCONT_DEFAULT'] = $this->getDiscontDefault($ID);

    $arCompany['CREDIT_TIME'] = $this->getCreditTime($ID);

    $arCompany['CREDIT_SUMM'] = $this->getCreditSumm($ID);

    $arCompany['INVOICE_TITLE'] = $this->getInvoiceTitle($ID);

    $arCompany['INVOICE_CURRENCY'] = $this->getInvoiceCurrency($ID);

    $arCompany['INVOICE_NUMBER'] = $this->getInvoiceNumber($ID);

    $arCompany['BANK_TITLE'] = $this->getBankTitle($ID);

    $arCompany['CITY']   = $this->getCity($ID);

    $arCompany['COMMENT'] = $arCompany['COMMENTS'];
 
    $arCompany['ASSORT'] = $this->getAssort($ID);
    
    $arCompany['ASSORT_OTHER'] = $this->getAssortOther($ID);

    $arCompany['STORED_PRODUCT'] = $this->getStoredProduct($ID);

    $arCompany['HOLYDAY'] = $this->getHoliday($ID);

    $arCompany['SOURCE'] = $this->getSource($ID);

    $arCompany['BRAND'] = $this->getBrand($ID);

    $arCompany['WORK'] = $this->getWork($ID);

    $arCompany['IN_STOCK'] = $this->getInStock($ID);

    $arCompany['COUNT_STOCK'] = $this->getCountStock($ID);

    $arCompany['TYPE_STOCK']  = $this->getTypeStock($ID);

    $arCompany['INFO']  = $this->getInfo($ID);

    $arCompany['BRAND_PRESENT']  = $this->getBrandPresent($ID);

    $arCompany['REGION_SALE']    = $this->getRegionSale($ID);

    $arCompany['CLIENTS']           = $this->getClients($ID);

    unset($arCompany['COMMENTS']);
    array_push($contractors,$arCompany);

  }

  return $contractors;
	
 }

 private function getClients($company_id) {
	
  $clients = [];

  $sort   = array('DATE_CREATE'=>'DESC');

  $filter = array("CHECK_PERMISSIONS" => "N", "COMPANY_ID" => $company_id);

  $fields = array("ID","NAME","POST","LAST_NAME","SECOND_NAME","DATE_CREATE","COMPANY_ID");

  $contacts = \CCrmContact::GetList($sort,$filter,$fields);
 
  while($arContacts = $contacts->Fetch()) {
      
    $ID = $arContacts['ID'];

    $arContacts['DATE_CREATE']  = $this->parseDate($arContacts['DATE_CREATE']);

    $arContacts['PHONE']  = $this->getContactPhone($ID);

    $arContacts['MOBILE'] = $this->getContactMobilePhone($ID);

    $arContacts['EMAIL']  = $this->getContactEmail($ID);

    if(!$arContacts['POST']) {
        
        $arContacts['POST'] = '';

    }

    if(!$arContacts['SECOND_NAME']) {
        
        $arContacts['SECOND_NAME'] = '';

    }

    unset($arContacts['COMPANY_TITLE']);
    
    $clients[] = $arContacts;

  }

  return $clients;

 }

 private function getCode($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522989078195',$ID)  ? : '';

 }

 private function getStatus($ID) {

  return self::$status_map[$this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988768425',$ID)]  ? : '';

 }

 private function getWebSite($ID) {

  $rs = \CCrmFieldMulti::GetList(
   array(),
   array(
      'ENTITY_ID' => 'COMPANY', 
      "ELEMENT_ID" => $ID, 
      "TYPE_ID" => "WEB"
      )
   );
   
  return $rs->Fetch()['VALUE'] ? : '';

 }

 private function getCity($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526977941',$ID)  ? : '';

 }

 private function getURTitle($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526621027',$ID)  ? : '';

 }
 
 private function getOFTitle($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526621036',$ID)  ? : '';

 }

 private function getManager($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526621055',$ID)  ? : '';

 }

 private function getAvtor($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988414018',$ID)  ? : '';

 }

 private function getINN($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620246',$ID)  ? : '';

 }

 private function getKPP($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620255',$ID)  ? : '';

 }

 private function getOKPO($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620275',$ID)  ? : '';

 }

 private function getDeliveryAddress($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620392',$ID)  ? : '';

 } 

 private function getDeliveryPerson($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620422',$ID)  ? : '';

 } 

 private function getDeliveryPersonPhone($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620474',$ID)  ? : '';

 } 

 private function getDocNumber($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620487',$ID)  ? : '';

 } 

 private function getDocStart($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620511',$ID)  ? : '';

 } 

 private function getDocEnd($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620524',$ID)  ? : '';

 } 

 private function getDocCurrency($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620535',$ID)  ? : '';

 } 

 private function getDocTitle($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620546',$ID)  ? : '';

 } 

 private function getPriceTypeDefault($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620558',$ID)  ? : '';

 }

 private function getDiscontDefault($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620569',$ID)  ? : '';

 }

 private function getCreditTime($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620581',$ID)  ? : '';

 }

 private function getCreditSumm($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620591',$ID)  ? : '';

 }

 private function getInvoiceTitle($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620603',$ID)  ? : '';

 }

 private function getInvoiceCurrency($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620614',$ID)  ? : '';

 }

 private function getInvoiceNumber($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620624',$ID)  ? : '';

 }

 private function getBankTitle($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620633',$ID)  ? : '';

 }

 private function getAssort($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988344927',$ID)  ? : '';

 }

 private function getAssortOther($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988955752',$ID)  ? : '';

 }

 private function getStoredProduct($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526622377',$ID)  ? : '';

 }

 private function getHoliday($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988388281',$ID)  ? : '';

 }

 private function getSource($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988644791',$ID)  ? : '';

 }

 private function getBrand($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988677868',$ID)  ? : '';

 }

 private function getWork($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522989013075',$ID)  ? : '';

 }

 private function getInStock($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526620709',$ID)  ? : '';

 }
  
 private function getCountStock($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988890077',$ID)  ? : '';

 }

 private function getTypeStock($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988922783',$ID)  ? : '';

 }

 private function getInfo($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522988400579',$ID)  ? : '';

 }
 
 private function getBrandPresent($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526622816',$ID)  ? : '';

 }

 private function getRegionSale($ID) {

  return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526622831',$ID)  ? : '';

 }

 private function getDateCreate($ID) {

  return $this->parseDate($this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1526623415',$ID))  ? : '';

 }

 private function getCompanyTitle($ID) {
   
   return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_59CE47C771201',$ID)  ? : $this->getContactName($ID);
    
 }
 
 private function getCompanyPhone($ID) {

   $rs = \CCrmFieldMulti::GetList(array(), array("ENTITY_ID"=>"COMPANY","TYPE_ID" => "PHONE","ELEMENT_ID" => $ID)); 

   $fields = $rs->Fetch();

   return $fields['VALUE'] ? : '';
   
 }

 private function getCompanyEmail($ID) {

   $rs = \CCrmFieldMulti::GetList(array(), array("ENTITY_ID"=>"COMPANY","TYPE_ID" => "EMAIL","ELEMENT_ID" => $ID)); 

   $fields = $rs->Fetch();

   return $fields['VALUE'] ? : '';
   
 }

 private function getAddress($ID) {

   return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1522995239232',$ID) ? : '';

 }

 private function getURAddress($ID) {

   return $this->uf_manager->GetUserFieldValue('CRM_COMPANY','UF_CRM_1523208787417',$ID) ? : '';

 }

 private function getContactName($company_id) {

  $sort   = array('DATE_CREATE'=>'DESC');

  $filter = array("CHECK_PERMISSIONS" => "N", "COMPANY_ID" => $company_id);

  $fields = array("ID","NAME","LAST_NAME","SECOND_NAME");

  $contacts = \CCrmContact::GetList($sort,$filter,$fields);

  $arResult = $contacts->Fetch();

  if(!$arResult) {
  
     return '';

  }

  return rtrim($arResult['NAME'].' '.$arResult['LAST_NAME'].' '.$arResult['SECOND_NAME']);

 }

 private function getContactPhone($ID) {

  $rs = \CCrmFieldMulti::GetList(array(), array("ENTITY_ID"=>"CONTACT","TYPE_ID" => "PHONE","ELEMENT_ID" => $ID)); 

  $fields = $rs->Fetch();

  return $fields['VALUE'] ? : '';

 }

 private function getContactMobilePhone($ID) {

  $rs = \CCrmFieldMulti::GetList(array(), array("ENTITY_ID"=>"CONTACT","VALUE_TYPE"=>"MOBILE","TYPE_ID" => "PHONE","ELEMENT_ID" => $ID)); 

  $fields = $rs->Fetch();

  return $fields['VALUE'] ? : '';

 }

 private function getContactEmail($ID) {

  $rs = \CCrmFieldMulti::GetList(array(), array("ENTITY_ID"=>"CONTACT","TYPE_ID" => "EMAIL","ELEMENT_ID" => $ID)); 

  $fields = $rs->Fetch();

  return $fields['VALUE'] ? : ''; 
   
 }

 private function parseDate($date) {

   return strftime("%Y-%m-%dT%H:%M:%S" ,strtotime($date));

 }

 private function __clone(){}
}

?>