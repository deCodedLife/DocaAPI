<?php

/**
 * Получение Записей за указанный период
 */

$sqlTimeCondition = "(
    ( start_at >= '$requestData->start_at' and start_at < '$requestData->end_at' ) OR 
    ( end_at > '$requestData->start_at' and end_at < '$requestData->end_at' ) OR 
    ( start_at < '$requestData->start_at' and end_at > '$requestData->end_at' )
)";

$existingVisits = mysqli_query(
    $API->DB_connection,
    "SELECT id, cabinet_id FROM visits WHERE $sqlTimeCondition AND is_active = 'Y'"
);



/**
 * Проверка времени филиала
 */

$storeData = $API->DB->from( "stores" )
    ->where( "id", $requestData->store_id )
    ->limit( 1 )
    ->fetch();

$isTimeCorrect = true;

if ( strtotime( $requestData->start_at ) < strtotime( $storeData->schedule_from ) ) $isTimeCorrect = false;
if ( strtotime( $requestData->end_at ) > strtotime( $storeData->schedule_to ) ) $isTimeCorrect = false;

if ( !$isTimeCorrect ) $API->returnResponse( "Время посещения выходит за рамки графика работы филиала", 400 );



/**
 * Расчет свободности Исполнителей, Клиентов и Кабинетов
 */

foreach ( $existingVisits as $existingVisit ) {

    /**
     * Проверка свободности Кабинета
     */
    if ( $existingVisit[ "cabinet_id" ] == $requestData->cabinet_id )
        $API->returnResponse( "Кабинет занят", 400 );


    /**
     * Проверка свободности Сотрудника
     */

    $visitUsers = $API->DB->from( "visits_users" )
        ->where( "visit_id", $existingVisit[ "id" ] );

    foreach ( $visitUsers as $visitUser )
        if ( in_array( $visitUser[ "user_id" ], $requestData->users_id ) ) {

            /**
             * Получение детальной информации о Сотруднике
             */
            $userDetail = $API->DB->from( "users" )
                ->where( "id", $visitUser[ "user_id" ] )
                ->limit( 1 )
                ->fetch();

            $API->returnResponse( "Сотрудник ${userDetail[ "last_name" ]} занят", 400 );

        } // if. in_array( $visitUser[ "user_id" ], $requestData->users_id


    /**
     * Проверка свободности Клиента
     */

    $visitClients = $API->DB->from( "visits_clients" )
        ->where( "visit_id", $existingVisit[ "id" ] );

    foreach ( $visitClients as $visitClient )
        if ( in_array( $visitClient[ "client_id" ], $requestData->client_id ) ) {

            /**
             * Получение детальной информации о Клиенте
             */
            $clientDetail = $API->DB->from( "clients" )
                ->where( "id", $visitClient[ "client_id" ] )
                ->limit( 1 )
                ->fetch();

            $API->returnResponse( "Клиент ${clientDetail[ "last_name" ]} занят", 400 );

        } // if. in_array( $visitClient[ "client_id" ], $requestData->client_id

} // foreach. $existingVisits