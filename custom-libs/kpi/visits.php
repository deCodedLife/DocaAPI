<?php

$start_at = $start_at ?? $requestData->start_at ?? date( "Y-m-d" );
$end_at = $end_at ?? $requestData->end_at ?? date( "Y-m-d" );
$user_id = $user_id ?? $requestData->user_id;

$visits_ids = visits\GetVisitsIDsByAuthor(
    "visits",
    $start_at . " 00:00:00",
    $end_at . " 23:59:59",
    $user_id
);

$equipment = visits\GetVisitsIDsByAuthor(
    "equipmentVisits",
    $start_at . " 00:00:00",
    $end_at . " 23:59:59",
    $user_id
);


function calculateKPI( $table, $visits_ids ): array {

    global $API;
    $fetch = mysqli_query( $API->DB_connection, "
    SELECT 
        ROUND( SUM( price ), 2 ) as summary,
        COUNT( id ) as count
    FROM 
        $table
    WHERE 
        id IN ( " . join( ",", $visits_ids ?? [] ) . " )"
    );
    if ( !$fetch ) return [ "summary" => 0, "count" => 0 ];
    return mysqli_fetch_array( $fetch );

}

$visitsInfo = calculateKPI( "visits", $visits_ids );
$equipmentInfo = calculateKPI( "equipmentVisits", $equipment );

$salesInfo = [
    "summary" =>
        $visitsInfo[ "summary" ] +
        $equipmentInfo[ "summary" ],
    "count" =>
        $visitsInfo[ "count" ] +
        $equipmentInfo[ "count" ]
];

$visits_count = count( $visits_ids ) + count( $equipment );
$sales_summary = $salesInfo[ "summary" ];
$sales_count = $salesInfo[ "count" ];


$services_count = ( mysqli_fetch_array( mysqli_query( $API->DB_connection, "
SELECT 
    COUNT( id ) as count
FROM 
	visits_services
WHERE 
	visit_id IN ( " . join( ",", $visits_ids ) . " )"
) )[ "count" ] ?? 0 ) + count( $equipment );