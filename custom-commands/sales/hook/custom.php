<?php

/**
 * Определение значений
 */

$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];

$saleVisits = $requestData->visits_ids;
$saleServices = [];
$allServices = [];

$cash_sum = $requestData->cash_sum;
$card_sum = $requestData->card_sum;

$is_return = $API->DB->from( "visits" )
    ->where( "id", $saleVisits[ 0 ] )
    ->limit( 1 )
    ->fetch()[ "is_payed" ] == 'Y';



/**
 * Получение списка посещений (в том числе совмещённых)
 */

require ( $publicAppPath . '/custom-commands/sales/hook/' . 'get-visits.php' );



/**
 * Формирование списка услуг со всех посещений
 */

require ( $publicAppPath . '/custom-commands/sales/hook/' . 'get-services.php' );



/**
 * Подсчёт итоговых сумм для оплаты
 */

require ( $publicAppPath . '/custom-commands/sales/hook/' . 'calculate-price.php' );



/**
 * Учёт акций
 */

require ( $publicAppPath . '/custom-commands/sales/hook/' . 'calculate-promotions.php' );



/**
 * Заполнение полей
 */

require ( $publicAppPath . '/custom-commands/sales/hook/' . 'fields-update.php' );


$API->returnResponse( $formFieldsUpdate );