<?php

/**
 * Формирование списка отмененных посещений
 */

$advertise = $API->DB->from( "advertise" );

$visits = $API->DB->from( "visits" );

/**
 * Сформированный список
 */
$returnVisits = [];

foreach ( $advertise as $advertise ) {

    $visitsCount = 0;
    $visitsPrice = 0;

    $filterClients = [];
    if ( $requestData->start_at ) $filterClients[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $filterClients[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";
    $filterClients[ "advertise_id" ] =  $advertise[ "id" ];

    $clients = $API->DB->from( "clients" )
        ->where( $filterClients );

    $filterVisits = [];
    $filterVisits[ "is_active" ] =  "Y";
    if ( $requestData->start_at ) $filterVisits[ "end_at >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $filterVisits[ "end_at <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $filterVisits[ "store_id" ] = $requestData->store_id;

    $visits = $API->DB->from( "visits" )
        ->where( $filterVisits );

    foreach ( $visits as $visit ) {

        $visits_clients = $API->DB->from( "visits_clients" )
            ->where( "visit_id", $visit[ "id" ] )
            ->limit( 1 )
            ->fetch();

        foreach ( $visits_clients as $visits_client ) {

            $client = $API->DB->from( "clients" )
                ->where( "id", $visits_client[ "client_id" ] )
                ->limit( 1 )
                ->fetch();

            if ( $client[ "advertise_id" ] == $advertise[ "id" ] ) {

                $visitsCount++;
                $visitsPrice += $visit[ "price" ];

            }

        }

    }

    $returnVisits[] = [

        "id" => $advertise["id"],
        "title" => $advertise["title"],
        "clientsCount" => count( $clients ),
        "visitsCount" => $visitsCount,
        "price" => $visitsPrice

    ];

}

$response[ "data" ] = $returnVisits;
