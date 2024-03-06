<?php

/**
 * @file
 * Список "Рекламные источники
 */

$filter = [];
if ( $requestData->start_at ) $filter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $filter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";

$advertiseReturn = [];

foreach ( $response[ "data" ] as $advertise ) {

    $filter[ "advertise_id" ] = $advertise[ "id" ];

    $clientsIds = $API->DB->from( "clients" )
        ->select( null )
        ->select( "id" )
        ->where( $filter );

    $clientsIds = array_keys( $clientsIds->fetchAll( "id" ) );

    if ( !empty( $clientsIds ) ) {

        $visits = $API->DB->from( "visits" )
            ->select( null )
            ->select( [ "COUNT( id ) as count", "ROUND( SUM( price ), 2 ) as summary" ] )
            ->where( [ "client_id" => $clientsIds ] );

        $cancelVisits = $API->DB->from( "visits" )
            ->select( null )
            ->select( "COUNT( id ) as count" )
            ->where( [ "client_id" => $clientsIds, "status" => "canceled" ] );

        $endedVisits = $API->DB->from( "visits" )
            ->select( null )
            ->select( [ "COUNT( id ) as count" ] )
            ->where( [ "client_id" => $clientsIds, "status" => "ended" ] );

        $visits = $visits->fetch();
        $cancelVisits = $cancelVisits->fetch();
        $endedVisits = $endedVisits->fetch();

    } else {

        $cancelVisits[ "count" ] = 0;
        $endedVisits[ "count" ] = 0;
        $visits[ "count" ] = 0;
        $visits[ "summary" ] = 0;

    }

    $advertiseReturn[] = [

        "id" => $advertise[ "id" ],
        "title" => $advertise[ "title" ],
        "start_at" => $advertise[ "start_at" ],
        "end_at" => $advertise[ "end_at" ],
        "store_id" => $advertise[ "store_id" ],
        "advertise_id" => $advertise[ "advertise_id" ],
        "clientsCount" => count($clientsIds),
        "recordedCount" => $visits[ "count" ] - $endedVisits[ "count" ] - $cancelVisits[ "count" ],
        "extantCount" => $endedVisits[ "count" ],
        "underdoneCount" => $cancelVisits[ "count" ],
        "visitsCount" => $visits[ "count" ],
        "price" => $visits[ "summary" ]

    ];

}

$response[ "data" ] = $advertiseReturn;