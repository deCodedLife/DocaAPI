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
 * Обработанная дата
 */
$currentScheduleDate = $scheduleFrom;

/**
 * Текущий график
 */

$currentSchedule = [];

$currentScheduleEvents = $API->DB->from( $API->request->object )
    ->where( [
        "event_from >= ?" => $requestData->start_from,
        "event_from <= ?" => $requestData->start_to . " 23:59:59"
    ] );

foreach ( $currentScheduleEvents as $currentScheduleEvent )
    $currentSchedule[
        date( "Y-m-d", strtotime( $currentScheduleEvent[ "event_from" ] ) )
    ][] = [
        "from" => date( "H:i:s", strtotime( $currentScheduleEvent[ "event_from" ] ) ),
        "to" => date( "H:i:s", strtotime( $currentScheduleEvent[ "event_to" ] ) ),
    ];


/**
 * Обход дат расписания
 */

while ( $currentScheduleDate <= $scheduleTo ) {

    /**
     * Получение текущей даты
     */

    $scheduleDate = date( "Y-m-d", $currentScheduleDate );

    $isContinue = false;


    /**
     * Проверка дня недели
     */

    if ( $requestData->work_days ) {

        /**
         * Получение дня недели текущей даты
         */
        $currentWeekDay = date( "l", $currentScheduleDate );

        if ( !in_array( $currentWeekDay, $requestData->work_days ) ) $isContinue = true;

    } // if. !$requestData->work_days


    /**
     * Обновление текущей даты
     */
    $currentScheduleDate = strtotime( "+1 day", $currentScheduleDate );
    if ( $isContinue ) continue;


    /**
     * Проверка на свободность графика
     */
    if ( $currentSchedule[ $scheduleDate ] ) {

        foreach ( $currentSchedule[ $scheduleDate ] as $dayWorkSchedule )
            if (
                (
                    ( $requestData->event_from >= $dayWorkSchedule[ "from" ] ) &&
                    ( $requestData->event_from <= $dayWorkSchedule[ "to" ] )
                ) ||
                (
                    ( $requestData->event_to >= $dayWorkSchedule[ "from" ] ) &&
                    ( $requestData->event_to <= $dayWorkSchedule[ "to" ] )
                )
            ) $isContinue = true;

        if ( $isContinue ) continue;

    } // if. $currentSchedule[ $scheduleDate ]


    /**
     * Добавление дня в график
     */
    $API->DB->insertInto( $API->request->object )
        ->values( [
            "event_from" => "$scheduleDate $requestData->event_from",
            "event_to" => "$scheduleDate $requestData->event_to",
            "user_id" => $requestData->id,
            "is_weekend" => $requestData->is_weekend,
            "store_id" => $requestData->store_id,
            "cabinet_id" => $requestData->cabinet_id
        ] )
        ->execute();

} // while. $currentScheduleDate <= $scheduleTo
