<?php

/**
 * Время действия статуса "Повторное" у Записей
 */
$repeatStatusFrom = date(
    "Y-m-d", strtotime( "-30 days", strtotime( date( "Y-m-d" ) ) )
);


if ( $requestData->phone || $requestData->last_name || $requestData->first_name || $requestData->patronymic ) {

    $clientDetail = $API->DB->from( "clients" )
        ->where( "phone", $requestData->phone )
        ->limit( 1 )
        ->fetch();

   $requestData->clients_id = [$clientDetail[ "id" ]];

}

/**
 * Статус "Повторное" у Посещения и Клиентов
 */

foreach ( $requestData->clients_id as $clientId ) {

    /**
     * Статус "Повторный" у Клиента
     */
    $isClientHasRepeatStatus = false;


    /**
     * Получение посещений Клиента
     */
    $clientVisits = $API->DB->from( "visits" )
        ->leftJoin( "visits_clients ON visits_clients.visit_id = visits.id" )
        ->select( null )->select( [ "visits.id", "visits.start_at", "visits_clients.client_id" ] )
        ->where( [
            "visits_clients.client_id" => $clientId
        ] )
        ->limit( 0 );


    /**
     * Обработка посещений Клиента
     */

    foreach ( $clientVisits as $clientVisit ) {

        /**
         * Проверка статуса "Повторный" у Клиента
         */
        if ( $clientVisit[ "id" ] != $insertId )
            $isClientHasRepeatStatus = true;


        /**
         * Проверка срока действия статуса "Повторный"
         */
        $visitStartAt = substr( $clientVisit[ "start_at" ], 0, 10 );
        if ( $repeatStatusFrom > $visitStartAt ) continue;


        /**
         * Обработка статуса "Повторное" у Записи
         */

        $visits_user = $API->DB->from( "visits" )
            ->where( "id", $clientVisit[ "id" ] )
            ->limit( 1 )
            ->fetch();

        if ( isset( $visits_user[ "user_id" ] ) && ( $visits_user[ "user_id" ] == $requestData->user_id ) )
            $API->DB->update( "visits" )
                ->set( [ "is_repeat" => "Y" ] )
                ->where( [
                    "id" => $insertId
                ] )
                ->execute();

    } // foreach. $clientVisits


    /**
     * Указание статуса "Повторное" у Клиента
     */
    if ( $isClientHasRepeatStatus ) $API->DB->update( "clients" )
        ->set( "is_repeat", "Y" )
        ->where( [
            "id" => $clientId
        ] )
        ->execute();

} // foreach. $requestData->clients_id


/**
 * Отправка события об обновлении расписания
 */
$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );

/**
 * Отправка уведомления
 */
$API->addNotification(
    "system_alerts",
    "Создана запись ",
    "на " . date( "H:i:s d.m.Y", strtotime( $requestData->start_at ) ),
    "info",
    $requestData->user_id
);
