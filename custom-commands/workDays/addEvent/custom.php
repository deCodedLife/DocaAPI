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
    ->where( "id", $requestData->store_id )
    ->fetch();


if ( strtotime( $requestData->event_from ) < strtotime( $storeDetails[ "schedule_from" ] ) ) $API->returnResponse( "Расписание выходит за рамки графика филиала", 500 );
if ( strtotime( $requestData->event_to )   > strtotime( $storeDetails[ "schedule_to" ] )  )  $API->returnResponse( "Расписание выходит за рамки графика филиала", 500 );


$currentScheduleEvents = $API->DB->from( $API->request->object )
    ->where( [
        "event_from >= ?" => $requestData->start_from,
        "event_from <= ?" => $requestData->start_to . " 23:59:59",
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
    $API->DB->insertInto( $API->request->object )
        ->values( [
            "event_from" => "$scheduleDate $scheduleTimeFrom",
            "event_to" => "$scheduleDate $scheduleTimeTo",
            "user_id" => $requestData->id,
            "is_weekend" => $requestData->is_weekend,
            "store_id" => $requestData->store_id,
            "cabinet_id" => $requestData->cabinet_id
        ] )
        ->execute();

} // while. $currentScheduleDate <= $scheduleTo
