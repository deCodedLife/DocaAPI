<?php

namespace visits;

function Base(
    string $table,
    string $start_at,
    string $end_at
): \Envms\FluentPDO\Queries\Common
{
    global $API;
    return $API->DB->from( $table )
        ->where( [
            "$table.start_at > ?" => $start_at,
            "$table.start_at < ?" => $end_at,
            "$table.is_active" => "Y",
        ] );
}


function GetVisitsIDsByUser( $table, $start_at, $end_at, $user_id ): array
{
    global $API;
    $request = Base( $table, $start_at . " 00:00:00", $end_at . " 23:59:59"  )
        ->where( "( user_id = $user_id OR assist_id = $user_id )" )
        ->where([
            "is_payed" => 'Y',
            "status" => "ended",
            "author_id" => $user_id
        ])
        ->fetchAll( "id" ) ?? [];
    $request = array_keys( $request );
    if ( empty( $request ) ) $request = [0];
    return $request;
}


function GetVisitsIDsByAuthor( $table, $start_at, $end_at, $operator_id ): array
{
    global $API;
    $request = $API->DB->from( $table )
        ->where([
            "start_at > ?" => $start_at . " 00:00:00",
            "end_at < ?" => $end_at . " 23:59:59",
            "is_active" => 'Y',
            "is_payed" => 'Y',
            "author_id" => $operator_id
        ])->fetchAll( 'id' ) ?? [];
    $request = array_keys( $request );
    if ( empty( $request ) ) $request = [0];
    return $request;
}


function VisitServices( array $visits_ids ) : array
{
    global $API;
    $servicesList = $API->DB->from( "services" )
        ->rightJoin( "visits_services ON visits_services.service_id = services.id" )
        ->where( "visits_services.visit_id", $visits_ids );
    return $servicesList->fetchAll() ?? [];
}


function EquipmentServices( array $visits_ids ) : array
{
    global $API;
    $servicesList = $API->DB->from( "services" )
        ->rightJoin( "equipmentVisits ON equipmentVisits.service_id = services.id" )
        ->where( "equipmentVisits.id", $visits_ids );
    return $servicesList->fetchAll() ?? [];
}


function getSalesByVisits( string $table, array $visits_ids ): array
{
    global $API;
    if ( count( $visits_ids ) == 0 ) return [];
    $sales = $API->DB->from( "salesList" )
        ->innerJoin( "$table ON $table.sale_id = salesList.id" )
        ->where( [
            "$table.visit_id" => $visits_ids,
            "salesList.action" => "sell",
            "salesList.status" => "done"
        ] );
    foreach ( $sales as $sale ) $sales_ids[] = $sale[ "id" ];
    return array_unique( $sales_ids ?? [] );
}


function getServicesIds( $category ): array
{
    global $API;

    $sqlFilter = "SELECT id FROM services WHERE category_id = $category";
    $servicesList = mysqli_query( $API->DB_connection, $sqlFilter );
    $services_ids = [];

    foreach ( $servicesList as $service ) $services_ids[] = intval( $service[ "id" ] );
    return $services_ids;

}


function getFullService( $id, $user_id = null )
{
    global $API;

    $innerPropertyRows = $API->sendRequest( "services", "get", [
        "id" => $id
    ] );

    $service = (array) $innerPropertyRows[ 0 ];
    $service[ "with_discount" ] = $service[ "price" ];

    if ( $service[ "article" ] == "null" ) $service[ "article" ] = "-";
    if ( !$user_id ) return $service;

    foreach ( ( $service[ "workingTime" ] ?? [] ) as $user_time ) {

        if ( $user_time->user != $user_id ) continue;
        $service[ "price" ] = $user_time->price;

    }

    $service[ "with_discount" ] = $service[ "price" ];

    return $service;

}