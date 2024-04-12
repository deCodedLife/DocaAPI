<?php

namespace Sales\visits;

use visits;

// VISITS EQUIPMENT
$visits = [];

function GetVisitsLists( string $table, array $visits_ids ): array
{
    global $API;
    if ( empty( $visits_ids ) ) return [];
    return $API->DB->from( $table )
        ->where( "id", $visits_ids )
        ->fetchAll( "id" ) ?? [];
}

function CombinedHandler( string $is_combined, &$visits )
{
    global $API, $requestData;
    if ( $is_combined == 'N' ) return;

    $start_at = $API->request->data->start_at ?? date( "Y-m-d" );
    $end_at = $API->request->data->end_at ?? date( "Y-m-d" );

    $start_at = date( "Y-m-d", strtotime( $start_at ) ) . " 00:00:00";
    $end_at = date( "Y-m-d", strtotime( $end_at ) ) . " 23:59:59";
    $client_id = $requestData->client_id ?? 0;

    $visits[ "visits" ] = visits\Base( "visits", $start_at, $end_at )
        ->innerJoin( "visits_clients ON visits_clients.visit_id = visits.id" )
        ->where( [
            "visits.store_id" => (int) $requestData->store_id,
            "visits_clients.client_id" => $client_id,
            "visits.is_payed" => "N"
        ] )
        ->fetchAll( "id" );

    $visits[ "equipmentVisits" ] = visits\Base( "equipmentVisits", $start_at, $end_at )
        ->where( [
            "equipmentVisits.store_id" => (int) $requestData->store_id,
            "equipmentVisits.client_id" => $client_id,
            "equipmentVisits.is_payed" => "N"
        ] )
        ->fetchAll( "id" );
}


$visits = (array) $requestData->visits_ids;
//$API->returnResponse( [ $visits, $requestData->visits_ids ] );
if ( ( $requestData->is_combined ?? 'N' ) == 'N' ) $visits = [
    "visits" => GetVisitsLists( "visits", $visits[ "visits" ] ?? [0] ),
    "equipmentVisits" => GetVisitsLists( "equipmentVisits", $visits[ "equipmentVisits" ] ?? [0] )
];
CombinedHandler( $requestData->is_combined ?? 'N', $visits );



/**
 * Получение итоговой суммы продажи
 */
function calculateSummary( array $visit )
{
    global $saleSummary;
    if ( !$visit[ "price" ] ) return;
    $saleSummary += $visit[ "price" ];
}

array_map( 'Sales\visits\calculateSummary', $visits[ "visits" ] );
array_map( 'Sales\visits\calculateSummary', $visits[ "equipmentVisits" ] );