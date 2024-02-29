<?php

namespace visits;

function sql_constructor( string $table, array $request ): array
{
    global $API;

    $visitsList = mysqli_query( $API->DB_connection, "
    SELECT $table.id as id
    FROM $table
    WHERE \n" . join( " AND\n\t", $request ) );

    foreach ( $visitsList as $visit ) $visits_ids[] = intval( $visit[ "id" ] );
    return $visits_ids ?? [];
}

function GetVisitsIDsByUser( $table, $start_at, $end_at, $user_id ): array
{
    global $API;
    return sql_constructor( $table, [
        "start_at >= '$start_at'",
        "end_at <= '$end_at'",
        "is_active = 'Y'",
        "status = 'ended'",
        "is_payed = 'Y'",
        "( user_id = $user_id OR assist_id = $user_id )"
    ]);
}

function GetVisitsIDsByAuthor( $table, $start_at, $end_at, $operator_id ): array
{
    global $API;
    return sql_constructor( $table, [
        "start_at >= '$start_at'",
        "end_at <= '$end_at'",
        "status = 'ended'",
        "is_active = 'Y'",
        "is_payed = 'Y'",
        "author_id = $operator_id"
    ]);
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

function getServicesIds( $category ): array {

    global $API;

    $sqlFilter = "SELECT id FROM services WHERE category_id = $category";
    $servicesList = mysqli_query( $API->DB_connection, $sqlFilter );
    $services_ids = [];

    foreach ( $servicesList as $service ) $services_ids[] = intval( $service[ "id" ] );
    return $services_ids;

}

function getFullService( $id, $user_id = null ) {

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