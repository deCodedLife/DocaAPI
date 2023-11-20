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


if ( $requestData->start_at ) $requestData->end_at = date( "Y-m-d 23:59:59", strtotime($requestData->start_at ) );
else $requestData->end_at =  date( "Y-m-d 23:59:59", strtotime( $requestData->start_at) );

/**
 * Увеличение диапазона графика для специальностей
 */
if ( $requestData->profession_id || $requestData->user_id ) {

    $requestData->end_at = date(
        "Y-m-d", strtotime( "+30 days", strtotime( $requestData->start_at ) )
    );

} // if. $requestData->profession_id || $requestData->users_id

if ( !$requestData->user_id ) {

    $users = mysqli_query(
        $API->DB_connection,
        "SELECT user_id FROM workDays
           WHERE event_from > '$requestData->start_at'
           AND event_to < '$requestData->end_at'
           AND store_id = $requestData->store_id
           GROUP BY user_id"
    );

    foreach ( $users as $user )
        $requestData->user_id[] = $user[ "user_id" ];

} else {

    $requestData->user_id = [ $requestData->user_id ];

} // if ( !$requestData->user_id )



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
 * Отключение фильтрации по тем кто не хочет раньше
 */
if ( $requestData->is_earlier == "N" ) unset( $requestData->is_earlier );

//$API->returnResponse( $requestData );