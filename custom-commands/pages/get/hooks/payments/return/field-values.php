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
    $formFieldValues[ "products" ][ "value" ][] = $saleService[ "product_id" ];

foreach ( $API->DB->from( "saleVisits" )
              ->where( "sale_id", $saleID ) as $saleVisit )
    $formFieldValues[ "visits_ids" ][] = $saleVisit[ "visit_id" ];