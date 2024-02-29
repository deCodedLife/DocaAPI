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

$sales_ids = array_merge(
    visits\getSalesByVisits( "saleVisits", $visits_ids ?? [ 0 ] ),
    visits\getSalesByVisits( "salesEquipmentVisits", $equipment ?? [ 0 ] ),
);

if ( count( $sales_ids ) == 0 ) $sales_ids = [ 0 ];


$salesInfo = mysqli_fetch_array( mysqli_query( $API->DB_connection, "
SELECT 
    ROUND( SUM( summary ) - SUM( sum_bonus ), 2 ) as summary,
    COUNT( id ) as count
FROM 
	salesList
WHERE 
    id IN ( " . join( ",", $sales_ids ) . " ) AND
	action = 'sell' AND
	status = 'done'"
) ) ?? [];


$visits_count = count( $visits_ids ) + count( $equipment );
$sales_summary = $salesInfo[ "summary" ] ?? 0;
$sales_count = $salesInfo[ "count" ] ?? 0;
$services_count = mysqli_fetch_array( mysqli_query( $API->DB_connection, "
SELECT 
    COUNT( salesProductsList.id ) as count
FROM 
	salesList
INNER JOIN 
    salesProductsList ON salesProductsList.sale_id = salesList.id
WHERE 
	salesList.id IN ( " . join( ",", $sales_ids ) . " ) AND
	salesList.action = 'sell'"
) )[ "count" ] ?? 0;