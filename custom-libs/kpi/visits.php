<?php

$visits_ids = utils\GetVisitsIDsByAuthor(
    "visits",
    $requestData->start_at . " 00:00:00",
    $requestData->end_at . " 23:59:59",
    $requestData->user_id
);

$equipment = utils\GetVisitsIDsByAuthor(
    "equipmentVisits",
    $requestData->start_at . " 00:00:00",
    $requestData->end_at . " 23:59:59",
    $requestData->user_id
);

$sales_ids = array_merge(
    utils\getSalesByVisits( "saleVisits", $visits_ids ),
    utils\getSalesByVisits( "salesEquipmentVisits", $equipment ),
);



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