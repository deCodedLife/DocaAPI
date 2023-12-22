<?php

/**
 * Сформированный график
 */
$generatedGraph = [];


/**
 * Создание событий для правил.
 * Создаёт массив объектов - событий
 *{
 * "id": ...,
 * "event_from": "2023-12-04 08:00:00",
 * "event_to": "2023-12-04 15:00:00",
 * "cabinet_id": 75,
 * "is_weekend": "N",
 * "user_id": 132
 * }
 *
 * @param array $rule
 * @return array
 */
function generateRuleEvents( array $rule ): array {

    global $API, $requestData;

    /**
     * Списки событий
     */
    $weekdays = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
    $generatedEvents = [];
    $eventWorkdays = $requestData->work_days ?? [];


    /**
     * Подтягиваем дни из связанной таблицы, если правило существует
     */
    if ( ( $requestData->id ?? 0 ) != 0 ) {

        /**
         * Получение дней графика
         */
        $eventWeekdays = $API->DB->from( "workDaysWeekdays" )
            ->where( "rule_id", $rule[ "id" ] );

        foreach ( $eventWeekdays as $weekday )
            $eventWorkdays[] = $weekday[ "workday" ];

    } // if ( !$requestData->work_days )

    if ( empty( $eventWorkdays ) ) $eventWorkdays = $weekdays;


    /**
     * Итерация графика по дням
     */
    $eventEnd = DateTime::createFromFormat( "Y-m-d H:i:s", $rule[ "event_to" ] );

    for (
        $iterator = DateTime::createFromFormat( "Y-m-d H:i:s", $rule[ "event_from" ] );
        $iterator < $eventEnd;
        $iterator->modify( "+1 day" )
    ) {

        $date = $iterator->format( "Y-m-d" );
        $weekday = date( "l", strtotime( $date ) );
        if ( !in_array( $weekday, $eventWorkdays ) ) continue;

        /**
         * Генерируем событие
         */
        $generatedEvents[] = [
            "id" => $rule[ "id" ] ?? 0,
            "event_from" => $iterator->format( "Y-m-d H:i:s" ),
            "event_to" => $eventEnd->format( "$date H:i:s" ),
            "cabinet_id" => $rule[ "cabinet_id" ],
            "is_weekend" => $rule[ "is_weekend" ] ?? 'N',
            "user_id" => $rule[ "user_id" ]
        ];

    } // for days iterator


    return $generatedEvents;

} // function generateRuleEvents( int $eventID ): array


foreach ( $response[ "data" ] as $eventDate => $events ) {

    foreach ( $events as $event ) {

        /**
         * Получение типа события (рабочий день / выходной)
         */

        $eventDetail = $API->DB->from( $API->request->object )
            ->where( "id", $event[ "id" ] )
            ->limit( 1 )
            ->fetch();

        /**
         * Получаем события для правила
         */
        $ruleEvents = generateRuleEvents( $eventDetail );


        /**
         * Подготавливаем события к выдаче
         */
        foreach ( $ruleEvents as $ruleEvent ) {

            $ruleEventDate = date( "Y-m-d", strtotime( $ruleEvent[ "event_from" ] ) );
            $newEvent = $event;
            $newEvent[ "is_rule" ] = $eventDetail[ "is_rule" ];
            $newEvent[ "is_weekend" ] = $ruleEvent[ "is_weekend" ];

            if ( $eventDetail[ "is_rule" ] === 'N' ) {

                $newEvent[ "background" ] = "primary";
                $generatedGraph[ $ruleEventDate ][ "hasIndividualRule" ] = true;

            }

            $cabinetDetail = $API->DB->from( "cabinets" )
                ->where( "id", $eventDetail[ "cabinet_id" ] )
                ->limit( 1 )
                ->fetch();

            if ( $cabinetDetail )  $newEvent[ "title" ] = " [Каб. " . $cabinetDetail[ "title" ] . "]";
            if ( $eventDetail[ "is_rule" ] === 'Y' ) $newEvent[ "background" ] = "success";

            if ( $ruleEvent[ "is_weekend" ] == "Y" ) {

                $newEvent[ "title" ] = "Отмена приема";
                $newEvent[ "background" ] = "danger";

            }

            $generatedGraph[ $ruleEventDate ][ "events" ][] = $newEvent;

        }

    } // foreach. $events

} // foreach. $response[ "data" ]


/**
 * Фильтруем события. Если в дате имеется отмена дня
 * тогда все события удаляются
 */
foreach ( $generatedGraph as $eventDate => $ruleObject ) {

    $eventList = [];
    $hasIndividualRule = $ruleObject[ "hasIndividualRule" ] ?? false;

    foreach ( $ruleObject[ "events" ] as $event ) {

        if ( $event[ "is_weekend" ] === 'Y'  ) {
            $eventList = [ $event ];
            break;
        }

        if ( $event[ "is_rule" ] === 'Y' && $hasIndividualRule ) continue;
        $eventList[] = $event;

    }

    $generatedGraph[ $eventDate ] = $eventList;

}


$response[ "data" ] = $generatedGraph;
