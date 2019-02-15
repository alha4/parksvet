<?php
namespace B24Entity\Commands;

use \B24Entity\Commands\Command,
    \B24Entity\Helpers\Logger,
    \Bitrix\Catalog\ProductTable;

\Bitrix\Main\Loader::includeModule('catalog');

class Goods extends Command  {

  const CATALOG_SECTION = 51;

  public function execute($request) {

    if($this->log_request()) {

      Logger::log($request);
     
    }

    $errors = [];

    $iblock_id = (int)\COption::GetOptionString('crm', 'default_product_catalog_id');
    
    foreach($request as $item) {

      $arGoods = [

        'ACTIVE' => 'Y',
        'IBLOCK_ID' =>  $iblock_id,
        'NAME'   => $item['PRODUCT_NAME'],
        'IBLOCK_SECTION_ID' => self::CATALOG_SECTION,
        'PROPERTY_VALUES' => [

          'ID_1C' => $item['ID'],
          'CODE'  => $item['CODE'],
          'PRODUCT_PROPERY'  => $item['PRODUCT_PROPERY'],
          'UNITS' => $item['UNITS'],
          'VOLUME' => $item['VOLUME'],
          'CONTRY' => $item['CONTRY'],
          'COMMENT' => $item['COMMENT'],
          'PRODUCT_SING' => $item['PRODUCT_SING'],
          'BAR_CODE'     => $item['BAR_CODE'],
          'STATUS'       => $item['STATUS'],
          'STORE_PLACE'  => $item['STORE_PLACE'],
          'STORE3'       => $item['STORE3'],
          'STORE4'       => $item['STORE4'],
          'STORE2'       => $item['STORE2'],
          'STORE1'       => $item['STORE1'],
          'STORE0'       => $item['STORE0'],
          'PRICE_DILLER'  => $item['PRICE_DILLER'],
          'PRICE_BIG_OPT' => $item['PRICE_BIG_OPT'],
          'PRICE_OPT'     => $item['PRICE_OPT'],
            
        ]
      ];

      $item['PURCHASING_PRICE'] = $item['PRICE'];
      $item['TYPE'] = ProductTable::TYPE_PRODUCT;

      if($ID = $this->exists($item['ID'])) {

        $ib = new \CIBlockElement(false);

        if($ib->Update($ID, $arGoods)) {

          $item['ID'] = $ID;

          if(!ProductTable::update($ID, $item)) {

             $errors[] = 'ошибка обновления товара';

             Logger::log(['ошибка обновления товара',$item]);

          }

        } else {

          $errors[] = 'ошибка обновления элемента';

          Logger::log(['ошибка обновления элемента', $ib->LAST_ERROR]);

        }

      } else {

       $ib = new \CIBlockElement(false);

       if($ID = $ib->Add($arGoods)) {

         $item['ID'] = $ID;

         if(!ProductTable::Add($item)) {

            $errors[] = 'ошибка добавления товара';

            Logger::log(['ошибка добавления товара',$item]);

         }

      } else {

           $errors[] = 'ошибка добавления элемента';

           Logger::log(['ошибка добавления элемента',$ib->LAST_ERROR]);

      }
     }
    }

    return array('STATUS' => 200, 'LAST_ID' => $ID, 'errors' => $errors);

  }

  private function exists($id) {

    $ib = \CIBlockElement::GetList(['PROPERTY_ID_1C' => 'DESC'], ['PROPERTY_ID_1C' => $id], FALSE, FALSE, ['ID']);

    return $ib->Fetch()['ID'] ? : false;

  }

  private function log_request() {
    return defined("GOODS_REQUEST_LOG") && \GOODS_REQUEST_LOG == 'Y' ? true : false;
 }

}