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
 * Обход дат расписания
 */

while ( $currentScheduleDate <= $scheduleTo ) {

    /**
     * Получение текущей даты
     */
    $scheduleDate = date( "Y-m-d", $currentScheduleDate );


    /**
     * Проверка дня недели
     */

    if ( !$requestData->work_days ) {

        /**
         * Добавление дня в график
         */
        $API->DB->insertInto( $API->request->object )
            ->values( [
                "event_from" => "$scheduleDate $requestData->event_from",
                "event_to" => "$scheduleDate $requestData->event_to",
                "user_id" => $requestData->id
            ] )
            ->execute();

    } else {

        /**
         * Получение дня недели текущей даты
         */
        $currentWeekDay = date( "l", $currentScheduleDate );

        /**
         * Добавление дня в график
         */
        if ( in_array( $currentWeekDay, $requestData->work_days ) ) $API->DB->insertInto( $API->request->object )
            ->values( [
                "event_from" => "$scheduleDate $requestData->event_from",
                "event_to" => "$scheduleDate $requestData->event_to",
                "user_id" => $requestData->id
            ] )
            ->execute();

    } // if. !$requestData->work_days


    /**
     * Обновление текущей даты
     */
    $currentScheduleDate = strtotime( "+1 day", $currentScheduleDate );

} // while. $currentScheduleDate <= $scheduleTo