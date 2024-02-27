<?php

$visits_ids = utils\GetVisitsIDsByAuthor(
    "visits",
    $requestData->start_at,
    $requestData->end_at,
    $requestData->user_id
);

$API->returnResponse( $visits_ids );

$sales_ids = utils\getSalesByVisits( $visits_ids );


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

$API->returnResponse( [ $salesInfo, $sales_ids ] );

$visits_count = count( $visits_ids );
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