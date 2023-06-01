<?php

/**
 * Время действия статуса "Повторное" у Записей
 */
$repeatStatusFrom = date(
    "Y-m-d", strtotime( "-30 days", strtotime( date( "Y-m-d" ) ) )
);


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

        $visits_users = $API->DB->from( "visits_users" )
            ->where( "visit_id", $clientVisit[ "id" ] );

        foreach ( $visits_users as $visit_user )
            if ( in_array( $visit_user[ "user_id" ], $requestData->users_id ) )
                $API->DB->update( "visits" )
                    ->set( "is_repeat", "Y" )
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