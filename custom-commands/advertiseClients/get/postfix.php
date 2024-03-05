<?php

/**
 * @file
 * Список "Рекламные источники
 */

$filter = [];
if ( $requestData->start_at ) $filter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $filter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";
$filter[ "advertise_id != ?" ] = "null";

$clients = $API->DB->from( "clients" )
    ->select( null )
    ->select( [ "id", "advertise_id" ] )
    ->where( $filter );

$API->returnResponse($clients->fetchAll( "id" ));

$advertiseReturn = [];

foreach ( $response[ "data" ] as $advertise ) {

    $advertiseReturn[] = [

        "id" => $advertise[ "id" ],
        "title" => $advertise[ "title" ],
        "start_at" => $advertise[ "start_at" ],
        "end_at" => $advertise[ "end_at" ],
        "store_id" => $advertise[ "store_id" ],
        "advertise_id" => $advertise[ "advertise_id" ],
        "clientsCount" => 1,
        "recordedCount" => 1,
        "extantCount" => 1,
        "underdoneCount" => 1,
        "visitsCount" => 1,
        "price" => 1

    ];

}

$response[ "data" ] = $advertiseReturn;