<?php

/**
 * Получение периода
 */
$begin = new DateTime( "Y-m-d H:i", "$requestData->start_from $requestData->event_from" );
$end = new DateTime( "Y-m-d H:i", "$requestData->start_to  $requestData->event_to" );

$strEventFrom = $begin->format( "Y-m-d H:i:s" );
$strEventTo = $end->format( "Y-m-d H:i:s" );

$timeEventFrom = strtotime( $begin->format( "Y-m-d H:i:s" ) );
$timeEventTo = strtotime( $end->format( "Y-m-d H:i:s" ) );


/**
 * Формирование поискового запроса для выявления
 * корреляций по кабинету и времени в расписании
 */
$searchQuery = "SELECT * FROM workDays WHERE
    ( start_at >= '$strEventFrom' and start_at < '$strEventTo' ) OR
    ( end_at > '$strEventFrom' and end_at < '$strEventTo' ) OR
    ( start_at < '$strEventFrom' and end_at > '$strEventTo' ) AND
    user_id = $requestData->user_id AND
    store_id = $requestData->store_id AND
    cabinet_id = $requestData->cabinet_id AND
    is_weekend is NULL AND 
    is_rule = 'Y'
";

if ( $end > $begin ) $API->returnResponse( "Период указан некорректно" );


/**
 * Создание событий для правил
 * @param int $eventID
 * @return array
 */
function generateSchedule( array $event ): array {

    global $API, $requestData;

    $weekdays = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday' ];

    $generatedEvents = [];
    $eventWorkdays = [];


    /**
     * Получение дней графика
     */
    if ( !$requestData->work_days ) {

        $eventWeekdays = $API->DB->from( "workDaysWeekdays" )
            ->where( "rule_id", $event[ "id" ] );

        foreach ( $eventWeekdays as $weekday )
            $eventWeekdays[] = $weekday[ "workday" ];

    } else {

        $eventWeekdays = $requestData->work_days;

    } // if ( !$requestData->work_days )


    if ( empty( $eventWeekdays ) ) $eventWeekdays = $weekdays;


    /**
     * Итерация графика по дням
     */
    $eventStart = new DateTime( $event[ "event_from" ] );
    $eventEnd = new DateTime( $event[ "event_to" ] );

    for (
        $iterator = new DateTime( $event[ "event_from" ] );
        $iterator < $eventEnd;
        $iterator->modify( "+1 day" )
    ) {

        $date = $iterator->format( "Y-m-d" );
        $weekday = date( "l", $date );
        if ( !in_array( $weekday, $eventWeekdays ) ) continue;

        $generatedEvents[] = [
            "id" => $event[ "id" ],
            "event_from" => $iterator->format( "Y-m-d H:i:s" ),
            "event_to" => $eventEnd->format( "$date H:i:s" ),
            "cabinet_id" => $event[ "cabinet_id" ],
            "is_weekend" => $event[ "is_weekend" ],
            "user_id" => $event[ "user_id" ]
        ];

    }

    $API->returnResponse( $generatedEvents );


    return $generatedEvents;

} // function generateSchedule( int $eventID ): array


/**
 * Поиск корреляций
 */
$scheduleEvents = $API->DB->from( "workDays" )
    ->where( $searchFilter);

foreach ( $scheduleEvents as $event ) {

    /**
     * Валидация событий
     */
    if ( $is_rule ) require "validateRule.php";
    else require "validateEvent.php";

    $eventStartFrom = strtotime( $event[ "event_from" ] );
    $eventEndsAt = strtotime( $event[ "event_to" ] );

} // foreach ( $scheduleEvents as $event )

if ( $scheduleEvents )
    $API->returnResponse( "Правило конфликтует с другим 
        [{$scheduleEvents[ "id" ]}] 
        ({$scheduleEvents[ "event_from" ]} {$scheduleEvents[ "event_to" ]})" );