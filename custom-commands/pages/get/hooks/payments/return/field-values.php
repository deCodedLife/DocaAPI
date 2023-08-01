<?php

$saleID = $pageDetail[ "row_id" ];

$formFieldValues = $API->DB->from( "salesList" )
    ->where( "id", $saleID )
    ->limit( 1 )
    ->fetch();

$formFieldValues[ "summary" ] = (float) $formFieldValues[ "summary" ];
$formFieldValues[ "sum_cash" ] = (float) $formFieldValues[ "sum_cash" ];
$formFieldValues[ "sum_card" ] = (float) $formFieldValues[ "sum_card" ];
$formFieldValues[ "sum_bonus" ] = (float) $formFieldValues[ "sum_bonus" ];
$formFieldValues[ "sum_deposit" ] = (float) $formFieldValues[ "sum_deposit" ];
$formFieldValues[ "is_combined" ] = $formFieldValues[ "is_combined" ] == "Y";
$formFieldValues[ "online_receipt" ] = $formFieldValues[ "online_receipt" ] == "Y";
$formFieldValues[ "action" ] = "sellReturn";


foreach ( $API->DB->from( "salesProductsList" )
              ->where( "sale_id", $saleID ) as $saleService )
    $formFieldValues[ "return_services" ][ "value" ][] = $saleService[ "product_id" ];

foreach ( $API->DB->from( "saleVisits" )
              ->where( "sale_id", $saleID ) as $saleVisit )
    $formFieldValues[ "visits_ids" ][] = $saleVisit[ "visit_id" ];

//$API->returnResponse( json_encode( $formFieldValues ), 500 );
//{"id":"11","status":"done","client_id":"1","store_id":"1","employee_id":"1","action":"sellReturn","pay_method":"card","sum_bonus":0,"sum_deposit":0,"sum_card":2,"sum_cash":0,"terminal_code":null,"created_at":"2023-07-07 13:06:58","online_receipt":true,"summary":2,"error":null,"is_system":"N","is_combined":false,"return_services":{"value":["28"]},"visits_ids":["336"]}