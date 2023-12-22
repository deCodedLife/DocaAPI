<?php

/**
 * Получение периода
 */
$begin = DateTime::createFromFormat( "Y-m-d H:i:s", "$requestData->start_from $requestData->event_from" );
$end = DateTime::createFromFormat( "Y-m-d H:i", "$requestData->start_to  $requestData->event_to" );

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
$requestData->is_rule = 'N';
$requestData->is_weekend = $requestData->is_weekend ?? null;


/**
 * Валидация времени
 */
if ( $begin > $end ) $API->returnResponse( "Период указан некорректно", 402 );

$storeDetails = $API->DB->from( "stores" )
    ->where( "id", $requestData->store_id )
    ->fetch();

if ( strtotime( $storeDetails[ "schedule_from" ] ) > strtotime( $begin->format( "H:i:s" ) ) )
    $API->returnResponse( "Расписание выходит за рамки графика филиала ${$storeDetails[ "title" ]}", 402 );

if ( strtotime( $storeDetails[ "schedule_to" ] ) < strtotime( $end->format( "H:i:s" ) ) )
    $API->returnResponse( "Расписание выходит за рамки графика филиала ${$storeDetails[ "title" ]}", 402 );



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
    is_rule = 'N' ";

/**
 * Поиск корреляций
 */
$scheduleRules = mysqli_query( $API->DB_connection, $searchQuery );
$newEvent = (array) $requestData;

foreach ( $scheduleRules as $rule ) {

    /**
     * Не проверяем правила на отмену посещений
     */
    if ( $requestData->is_weekend === 'Y' ) break;
    if ( $rule[ "is_weekend" ] === 'Y' ) continue;

    /**
     * Проверяем занят ли кабинет
     */
    if ( $rule[ "cabinet_id" ] == $newEvent[ "cabinet_id" ] ) {

        /**
         * Получаем информацию по сотруднику в событии коррелирующего правила
         */
        $employeeDetails = $API->DB->from( "users" )
            ->where( "id", $rule[ "user_id" ] )
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
    if ( $rule[ "user_id" ] == $newEvent[ "user_id" ] ) {

        $eventDate = date( "d-m", strtotime( $rule[ "event_from" ] ) );

        $eventTimeFrom = date( "H:i", strtotime( $rule[ "event_from" ] ) );
        $eventTimeTo = date( "H:i", strtotime( $rule[ "event_to" ] ) );

        $API->returnResponse( "У сотрудника уже есть расписание на $eventDate с $eventTimeFrom по $eventTimeTo", 500 );

    } //  if ( $ruleEvent[ "user_id" ] == $newEvent[ "user_id" ] ) {

} // foreach ( $scheduleRules as $rule )


/**
 * Создание записи в расписании
 */
$API->DB->insertInto( "workDays" )
    ->values( $newEvent )
    ->execute();