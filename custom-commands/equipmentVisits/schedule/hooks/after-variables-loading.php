<?php

/**
 * Фильтр Исполнителей
 */
$performersFilter[ "is_visible_in_schedule" ] = "Y";


/**
 * Определение филиала
 */

if ( $requestData->store_id ) {

    $storeDetail = $API->DB->from( "stores" )
        ->where( "id", $requestData->store_id )
        ->limit( 1 )
        ->fetch();

} else {

    $storeDetail = $API->DB->from( "stores" )
        ->limit( 1 )
        ->fetch();

} // if. $requestData->store_id

if ( !$storeDetail ) $API->returnResponse( "Не определен филиал", 500 );


/**
 * Определение графика работы филиала
 */

/**
 * Начало рабочего дня
 */
$workdayStart = strtotime( $storeDetail[ "schedule_from" ] );
$currentStep = $workdayStart;


/**
 * Конец рабочего дня
 */
$workdayEnd = strtotime( $storeDetail[ "schedule_to" ] );