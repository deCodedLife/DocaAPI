<?php

/**
 * Определение значений
 */

$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
require_once ( $publicAppPath . '/custom-libs/sales/business_logic.php' );

//$saleVisits = $requestData->visits_ids;
//$saleServices = [];
//$allServices= [];
$formFieldsUpdate[ "products" ][ "value" ][] = 2;
//$cash_sum = $requestData->cash_sum;
//$card_sum = $requestData->card_sum;

//$is_return = $API->DB->from( "visits" )
//    ->where( "id", $saleVisits[ 0 ] )
//    ->limit( 1 )
//    ->fetch()[ "is_payed" ] == 'Y';


//$API->returnResponse( "test error", 500 );
/**
 * Получение списка посещений (в том числе совмещённых)
 */

//require ( $publicAppPath . '/custom-commands/sales/hook/' . 'get-visits.php' );



/**
 * Формирование списка услуг со всех посещений
 */

//require ( $publicAppPath . '/custom-commands/sales/hook/' . 'get-services.php' );



/**
 * Подсчёт итоговых сумм для оплаты
 */

//require ( $publicAppPath . '/custom-commands/sales/hook/' . 'calculate-price.php' );



/**
 * Заполнение полей
 */

//require ( $publicAppPath . '/custom-commands/sales/hook/' . 'fields-update.php' );


$API->returnResponse( $formFieldsUpdate );
