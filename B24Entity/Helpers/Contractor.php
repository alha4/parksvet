<?php
 namespace B24Entity\Helpers;

 trait Contractor {

 private static $COMPANY_ERRORS = [];

 private static $COMPANY_STATUS_MAP = [
   "Активный"     => 27,
   "Пассивный"    => 28,
   "В разработке" => 29,
   "Закрыт"       => 30
 ];

 private function addCompany(array $arCompany) {

    $crm_company = new \CCrmCompany(false);
 
    if($ID = $crm_company->Add($arCompany)) {
 
       return $ID;
  
    } 
    
    self::$COMPANY_ERROR[] = $crm_company->LAST_ERROR;
 
    return false;
 
  }

  private function getCompanyID($code) {

    if(!$code) return false;

    $filter = ["CHECK_PERMISSIONS" => "N", "UF_CRM_1522989078195" => $code];
    
    $company = \CCrmCompany::GetList(array("UF_CRM_1522989078195" => "DESC"), $filter, array("ID"));
 
    $result = $company->Fetch();
 
    return $result['ID'] ? : false;
   
  }

 }


