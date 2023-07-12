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


/**
 * Увеличение диапазона графика для специальностей
 */

if ( $requestData->profession_id || $requestData->users_id ) {

    $requestData->end_at = date(
        "Y-m-d", strtotime( "+30 days", strtotime( $requestData->start_at ) )
    );

} // if. $requestData->profession_id || $requestData->users_id



/**
 * Отключение фильтрации по тем кто не хочет раньше
 */
if ( $requestData->is_earlier == "N" ) unset( $requestData->is_earlier );
