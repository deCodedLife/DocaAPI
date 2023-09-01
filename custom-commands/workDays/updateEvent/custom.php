<?php

/**
 * Дата начала графика
 */
$scheduleFrom = strtotime( $requestData->start_from );

/**
 * Дата окончания графика
 */
$scheduleTo = strtotime( $requestData->start_to );

/**
 * Время начала графика
 */
$scheduleTimeFrom = $requestData->event_from;
if ( !$scheduleTimeFrom ) $scheduleTimeFrom = "00:00:00";

/**
 * Время окончания графика
 */
$scheduleTimeTo = $requestData->event_to;
if ( !$scheduleTimeTo ) $scheduleTimeTo = "23:59:59";

if ( $requestData->id ) $currentScheduleDetail = $API->DB->from( "workDays" )
    ->where( "id", $requestData->id )
    ->limit( 1 )
    ->fetch();

$currentScheduleDetail[ "start_at" ] = explode( " ", $currentScheduleDetail[ "event_from" ] );
$currentScheduleDetail[ "end_at" ] = explode( " ", $currentScheduleDetail[ "event_to" ] );

if ( !$scheduleFrom ) $scheduleFrom = strtotime( $currentScheduleDetail[ "start_at" ][ 0 ] );
if ( !$scheduleTo ) $scheduleTo = strtotime( $currentScheduleDetail[ "end_at" ][ 0 ] );
if ( !$requestData->event_from ) $scheduleTimeFrom = $currentScheduleDetail[ "start_at" ][ 1 ];

if ( !$requestData->event_to ) $scheduleTimeTo = $currentScheduleDetail[ "end_at" ][ 1 ];

if ( $requestData->is_weekend === null ) $eventIsWeekend = $currentScheduleDetail[ "is_weekend" ];
elseif ( !$requestData->is_weekend ) $eventIsWeekend = "N";
else $eventIsWeekend = "Y";

if ( !$requestData->store_id ) $eventStoreId = $currentScheduleDetail[ "store_id" ];
else $eventStoreId = $requestData->store_id;

if ( !$requestData->cabinet_id ) $eventCabinetId = $currentScheduleDetail[ "cabinet_id" ];
else $eventCabinetId = $requestData->cabinet_id;


/**
 * Обработанная дата
 */
$currentScheduleDate = $scheduleFrom;

/**
 * Текущий график
 */

$currentSchedule = [];

/**
 * Получение данных о филиале пользователя
 */
$storeDetails = $API->DB->from( "stores" )
    ->where( "id", $eventStoreId )
    ->fetch();

if ( $requestData->event_from ) {

    if ( strtotime( $requestData->event_from ) < strtotime( $storeDetails[ "schedule_from" ] ) ) $API->returnResponse( "Расписание выходит за рамки графика филиала ${$storeDetails[ "title" ]}", 500 );

}
if ( $requestData->event_to ) {

    if ( strtotime( $requestData->event_to )   > strtotime( $storeDetails[ "schedule_to" ] )   ) $API->returnResponse( "Расписание выходит за рамки графика филиала ${$storeDetails[ "title" ]}", 500 );

}

$currentScheduleEvents = $API->DB->from( $API->request->object )
    ->where( [
        "event_from >= ?" => $currentScheduleDetail[ "start_at" ][ 0 ],
        "event_from <= ?" => $currentScheduleDetail[ "end_at" ][ 0 ] . " 23:59:59",
        "user_id" => $requestData->id
    ] );

foreach ( $currentScheduleEvents as $currentScheduleEvent )
    $currentSchedule[
        date( "Y-m-d", strtotime( $currentScheduleEvent[ "event_from" ] ) )
    ][] = $currentScheduleEvent[ "id" ];


/**
 * Обход дат расписания
 */

while ( $currentScheduleDate <= $scheduleTo ) {

    /**
     * Получение текущей даты
     */
    $scheduleDate = date( "Y-m-d", $currentScheduleDate );


    /**
     * Обновление текущей даты
     */
    $currentScheduleDate = strtotime( "+1 day", $currentScheduleDate );

    /**
     * Очистка дня
     */
    if ( ( $requestData->is_weekend == "Y" ) && $currentSchedule[ $scheduleDate ] ) {

        foreach ( $currentSchedule[ $scheduleDate ] as $currentDayId )
            $API->DB->deleteFrom( $API->request->object )
                ->where( [
                    "id" => $currentDayId
                ] )
                ->execute();

    } // if. $currentSchedule[ $scheduleDate ]
    /**
     * Добавление дня в график
     */
    $API->DB->update( $API->request->object )
        ->set( [
            "event_from" => "$scheduleDate $scheduleTimeFrom",
            "event_to" => "$scheduleDate $scheduleTimeTo",
            "user_id" => $currentScheduleDetail[ "user_id" ],
            "is_weekend" => $eventIsWeekend,
            "store_id" => $eventStoreId,
            "cabinet_id" => $eventCabinetId
        ] )
        ->where( "id", $requestData->id )
        ->execute();

} // while. $currentScheduleDate <= $scheduleTo
