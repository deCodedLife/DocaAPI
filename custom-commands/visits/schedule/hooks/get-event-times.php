<?php

/**
 * Получение графиков работ Сотрудников
 */

$performersWorkSchedule = [];

foreach ( $performersDetail as $performerId => $performerDetail ) {

    /**
     * Обход графика работы Сотрудника
     */

    $performerWorkSchedule = $API->DB->from( "workDays" )
        ->where( [
            "event_from >= ?" => "$requestData->start_at 00:00:00",
            "event_from <= ?" => "$requestData->end_at 23:59:59",
            "user_id" => $performerId
        ] );

    foreach ( $performerWorkSchedule as $scheduleEvent ) {

        /**
         * Игнорирование выходных
         */
        if ( $scheduleEvent[ "is_weekend" ] == "Y" ) continue;


        /**
         * Получение даты графика работы
         */

        $scheduleEventDate = date( "Y-m-d", strtotime( $scheduleEvent[ "event_from" ] ) );

        $performersWorkSchedule[ $performerId ][ $scheduleEventDate ][] = [
            "from" => date( "H:i", strtotime( $scheduleEvent[ "event_from" ] ) ),
            "to" => date( "H:i", strtotime( $scheduleEvent[ "event_to" ] ) ),
            "cabinet_id" => $scheduleEvent[ "cabinet_id" ]
        ];


        /**
         * Добавление события в список шагов
         */

        $eventTimes[] = date(
            "H:i",
            strtotime( $scheduleEvent[ "event_from" ] )
        );

        $eventTimes[] = date(
            "H:i",
            strtotime( $scheduleEvent[ "event_to" ] )
        );

    } // foreach. $performerWorkSchedule

} // foreach. $performersDetail


/**
 * Очистка дублей
 */
$eventTimes = array_unique( $eventTimes );

/**
 * Сортировка временных отрезков
 */
sort( $eventTimes );
