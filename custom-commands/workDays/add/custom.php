<?php

/**
 * Получение периода
 */
$begin = DateTime::createFromFormat( "Y-m-d H:i:s", "$requestData->start_from $requestData->event_from" );
$end = DateTime::createFromFormat( "Y-m-d H:i:s", "$requestData->start_to  $requestData->event_to" );

/**
 * Перезаписываем объект requestData, чтобы затем использовать для создания записи
 * $API->DB->insert( "..." )->values( (array) $requestData )
 */
$requestData->event_from = $begin->format( "Y-m-d H:i:s" );
$requestData->event_to = $end->format( "Y-m-d H:i:s" );
unset( $requestData->start_from );
unset( $requestData->start_to );
unset( $requestData->id );


/**
 * Инициализация значений
 */
$requestData->is_rule = 'Y';
$requestData->work_days = $requestData->work_days ?? [];
$requestData->is_weekend = ( $requestData->is_weekend ? 'Y' : 'N' ) ?? null;


/**
 * Валидация времени
 */
if ( $begin > $end ) $API->returnResponse( "Период указан некорректно", 402 );

$storeDetails = $API->DB->from( "stores" )
    ->where( "id", $requestData->store_id )
    ->fetch();

if ( $requestData->is_weekend !== 'Y' ) {

    if ( strtotime( $storeDetails[ "schedule_from" ] ) > strtotime( $begin->format( "H:i:s" ) ) )
        $API->returnResponse( "Расписание выходит за рамки графика филиала ${$storeDetails[ "title" ]}", 402 );

    if ( strtotime( $storeDetails[ "schedule_to" ] ) < strtotime( $end->format( "H:i:s" ) ) )
        $API->returnResponse( "Расписание выходит за рамки графика филиала ${$storeDetails[ "title" ]}", 402 );

}



/**
 * Формирование поискового запроса для выявления
 * корреляций по кабинету и времени в расписании
 *
 * В списке также присутствуют графики, которые
 * частично пересекаются с новым
 */
$searchQuery = "SELECT * FROM workDays WHERE 
    (
        ( event_from >= '$requestData->event_from' and event_from < '$requestData->event_to' ) OR
        ( event_to > '$requestData->event_from' and event_to < '$requestData->event_to' ) OR
        ( event_from < '$requestData->event_from' and event_to > '$requestData->event_to' ) 
    ) AND 
    store_id = $requestData->store_id AND
    ( is_weekend is NULL OR is_weekend = 'N' ) AND
    is_rule = 'Y' ";


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


/**
 * Поиск корреляций
 */
$scheduleRules = mysqli_query( $API->DB_connection, $searchQuery );
$newSchedule = generateRuleEvents( (array) $requestData );


foreach ( $scheduleRules as $rule ) {

    /**
     * Не проверяем правила на отмену посещений
     */
    if ( $requestData->is_weekend === 'Y' ) break;
    if ( $rule[ "is_weekend" ] === 'Y' ) continue;

    /**
     * Получаем список событий коррелирующего правила
     */
    $ruleEvents = generateRuleEvents( $rule );

    foreach ( $ruleEvents as $ruleEvent ) {

        /**
         * Получаем время события коррелирующего правила
         */
        $eventStartFrom =  strtotime( $ruleEvent[ "event_from" ] );
        $eventEndsAt = strtotime( $ruleEvent[ "event_to" ] );

        /**
         * Проходимся по событиям нового правила
         */
        foreach ( $newSchedule as $newEvent ) {

            /**
             * Получаем время события нового правила
             */
            $newEventStartFrom = strtotime( $newEvent[ "event_from" ] );
            $newEventEndsAt = strtotime( $newEvent[ "event_to" ] );

            /**
             * Находим корреляцию по времени
             */
            if (
                ( $eventStartFrom >= $newEventStartFrom AND $eventStartFrom < $newEventEndsAt ) OR
                ( $eventEndsAt > $newEventStartFrom AND $newEventEndsAt < $newEventStartFrom ) OR
                ( $eventStartFrom < $newEventStartFrom AND $eventEndsAt > $newEventEndsAt )
            ) {

                /**
                 * Проверяем занят ли кабинет
                 */
                if ( $ruleEvent[ "cabinet_id" ] == $newEvent[ "cabinet_id" ] ) {

                    /**
                     * Получаем информацию по сотруднику в событии коррелирующего правила
                     */
                    $employeeDetails = $API->DB->from( "users" )
                        ->where( "id", $ruleEvent[ "user_id" ] )
                        ->fetch();

                    $employeeFio = "{$employeeDetails[ "last_name" ]} ";
                    $employeeFio .= mb_substr( $employeeDetails[ "first_name" ], 0, 1 ) . ". ";
                    $employeeFio .= mb_substr( $employeeDetails[ "patronymic" ], 0, 1 ) . ". ";

                    $API->returnResponse( "Кабинет занимает врач $employeeFio", 500 );

                } // if ( $ruleEvent[ "cabinet_id" ] == $newEvent[ "cabinet_id" ] ) {


                /**
                 * Если кабинет не занят, то возможной причиной корреляции стало уже
                 * существующее правило для сотрудника
                 */
                if ( $ruleEvent[ "user_id" ] == $newEvent[ "user_id" ] ) {

                    $eventDate = date( "d-m", strtotime( $ruleEvent[ "event_from" ] ) );

                    $eventTimeFrom = date( "H:i", strtotime( $ruleEvent[ "event_from" ] ) );
                    $eventTimeTo = date( "H:i", strtotime( $ruleEvent[ "event_to" ] ) );

                    $API->returnResponse( "У сотрудника уже есть расписание на $eventDate с $eventTimeFrom по $eventTimeTo", 500 );

                } //  if ( $ruleEvent[ "user_id" ] == $newEvent[ "user_id" ] ) {

            } // if ( correlation )

        } // foreach ( $newSchedule as $newEvent ) {

    } // foreach ( $ruleEvents as $ruleEvent ) {

} // foreach ( $scheduleEvents as $event )


/**
 * Если мы находимся тут, то никаких накладок не выявлено
 */

/**
 * Вытаскиваем список дней для последующего добавления
 * в связанную таблицу workDaysWeekdays
 */
$workDays = (array) $requestData->work_days;
unset( $requestData->work_days );


/**
 * Добавляем правило из запроса, попутно сохраняя ID
 * создаваемого объекта
 */
$ruleID = $API->DB->insertInto( "workDays" )
    ->values( (array) $requestData )
    ->execute();


/**
 * Запись данных в связанную таблицу
 */
foreach ( $workDays as $workDay ) {

    $API->DB->insertInto( "workDaysWeekdays" )
        ->values( [
            "rule_id" => $ruleID,
            "workday" => $workDay
        ] )
        ->execute();

}

/**
 * Блокируем создание записей
 */
$API->returnResponse();