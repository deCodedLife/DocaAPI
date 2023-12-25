<?php

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
function generateRuleEvents( array $rule ): array
{

    global $API, $requestData;

    /**
     * Списки событий
     */
    $weekdays = [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
    $generatedEvents = [];
    $eventWorkdays = $requestData->work_days ?? [];


    /**
     * Подтягиваем дни из связанной таблицы, если правило существует
     */
    if (( $requestData->id ?? 0) != 0 ) {

        /**
         * Получение дней графика
         */
        $eventWeekdays = $API->DB->from( "workDaysWeekdays" )
            ->where( "rule_id" , $rule[ "id" ] );

        foreach ( $eventWeekdays as $weekday)
            $eventWorkdays[] = $weekday[ "workday" ];

    } // if ( !$requestData->work_days )

    if (empty( $eventWorkdays)) $eventWorkdays = $weekdays;


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
            "event_to" => $eventEnd->format( "$date H:i:s"),
            "cabinet_id" => $rule[ "cabinet_id" ],
            "store_id" => $rule[ "store_id" ],
            "is_weekend" => $rule[ "is_weekend" ] ?? 'N',
            "user_id" => $rule[ "user_id" ]
        ];

    } // for days iterator


    return $generatedEvents;

} // function generateRuleEvents( int $eventID ): array