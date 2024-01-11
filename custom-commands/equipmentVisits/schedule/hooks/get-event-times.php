<?php

/**
 * Получение графиков работ Сотрудников
 */

$performersWorkSchedule = [];

foreach ( $performersDetail as $performerId => $performerDetail ) {

    /**
     * Обход
     */

    $storesInfo = $API->DB->from( "stores" );

    /**
     * Кастомный график работы оборудования на 30 дней
     */
    if ( $requestData->start_at ) $datetime = new DateTime( $requestData->start_at );
    else $datetime = new DateTime( date( "Y-m-d" ) );

    for ( $day = 0; $day < 30; $day++ ) {

        foreach ( $storesInfo as $store ) {

            if ( $store[ "id" ] != $requestData->store_id ) continue;

            $performerWorkSchedule[] = [
                "id" => $day,
                "event_from" => $datetime->format( "Y-m-d {$store[ "schedule_from" ]}" ),
                "event_to" => $datetime->format( "Y-m-d  {$store[ "schedule_to" ]}" ),
                "is_system" => "N",
                "equipment_id" => $performerId,
                "is_weekend" => 'N',
                "store_id" => $store[ "id" ],
                "cabinet_id" => null
            ];

        } // foreach ( $storesIDs as $store ) {

        $datetime->modify( '+1 day' );

    } // for ( $day = 0; $day < 7; $day++ )

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
