<?php

$saleID = $pageDetail[ "row_id" ];

$formFieldValues = $API->DB->from( "sales" )
    ->where( "id", $saleID )
    ->limit( 1 )
    ->fetch();

$formFieldValues[ "summary" ] = (float) $formFieldValues[ "summary" ];
$formFieldValues[ "cash_sum" ] = (float) $formFieldValues[ "cash_sum" ];
$formFieldValues[ "card_sum" ] = (float) $formFieldValues[ "card_sum" ];
$formFieldValues[ "bonus_sum" ] = (float) $formFieldValues[ "bonus_sum" ];
$formFieldValues[ "deposit_sum" ] = (float) $formFieldValues[ "deposit_sum" ];
$formFieldValues[ "is_combined" ] = $formFieldValues[ "is_combined" ] == "Y";
$formFieldValues[ "online_receipt" ] = $formFieldValues[ "online_receipt" ] == "Y";
$formFieldValues[ "pay_type" ] = "sellReturn";



foreach ( $API->DB->from( "salesServices" )
              ->where( "sale_id", $saleID ) as $saleService )
    $formFieldValues[ "pay_object" ][] = $saleService[ "service_id" ];

foreach ( $API->DB->from( "salesVisits" )
              ->where( "sale_id", $saleID ) as $saleVisit )
    $formFieldValues[ "visits_ids" ][] = $saleVisit[ "visit_id" ];