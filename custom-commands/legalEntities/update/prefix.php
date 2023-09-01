<?php

/**
 * Проверка клиентов на принадлежность к другим организациям
 * @return void
 */
function checkClients() {

    global $API, $requestData;

    if ( !$requestData->clients ) return;

    /**
     * Формирование sql запроса
     */
    $clientsList = join( ',', $requestData->clients );
    $additional = "";

    if ( $requestData->id ) $additional = "AND NOT id = $requestData->id";

    /**
     * Получение списка клиентов
     */
    $existingClients = mysqli_query(
        $API->DB_connection,
        "SELECT * FROM legal_entity_clients WHERE client_id IN ($clientsList) $additional"
    );
    if ( !$existingClients ) return;

    /**
     * Выбираем первого из списка
     */
    $existingClient = mysqli_fetch_array( $existingClients );

    /**
     * Получение детальной информации
     */
    $entityDetails = $API->DB->from( "legal_entities" )
        ->where( "id", $existingClient[ "legal_entity_id" ] )
        ->fetch();

    $clientDetails = $API->DB->from( "clients" )
        ->where( "id", $existingClient[ "client_id" ] )
        ->fetch();

    $API->returnResponse( "Клиент {$clientDetails[ "last_name" ]} уже числится в {$entityDetails[ "title" ]}", 500 );

}

checkClients();