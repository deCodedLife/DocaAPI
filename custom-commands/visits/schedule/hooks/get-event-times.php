<?php // X

/**
 * Получение графиков работ Сотрудников
 */

$performersWorkSchedule = [];

foreach ( $performersDetail as $performerId => $performerDetail ) {

    if ( !in_array( $performerId, $requestData->user_id ) ) continue;

    /**
     * Обход графика работы Сотрудника
     */

    /**
     * Обход графика работы Сотрудника
     */
    $filters = [
        "event_from >= ?" => "$requestData->start_at 00:00:00",
        "event_to <= ?" => "$requestData->end_at 23:59:59",
        "user_id" => $performerId,
        "is_weekend" => 'Y'
    ];

    $is_weekend = $API->DB->from( "scheduleEvents" )
        ->where( $filters )
        ->fetch();

    if ( $is_weekend ) continue;
    unset( $filters[ "is_weekend" ] );
    $filters[ "is_rule" ] = 'N';

    $hasEvents = $API->DB->from( "scheduleEvents" )
        ->where( $filters )
        ->fetch();

    if ( !$hasEvents ) unset( $filters[ "is_rule" ] );

    $performerWorkSchedule = $API->DB->from( "scheduleEvents" )
        ->where( $filters )
        ->orderBy( "event_from ASC" );

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