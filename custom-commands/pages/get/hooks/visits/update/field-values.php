<?php

/**
 * Получение детальной информации о посещении
 */

$visitDetails = $API->DB->from( "visits" )
    ->where( "id", $pageDetail[ "row_id" ] )
    ->limit( 1 )
    ->fetch();

$saleDetails =  $API->DB->from( "salesVisits" )
    ->where( "visit_id", $pageDetail[ "row_id" ] )
    ->fetch();


/**
 * Получение детальной информации о пользователе
 */

$userDetails = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();

if ( !$userDetails[ "store_id" ] ) $userDetails[ "store_id" ] = $API->DB->from( "stores" )->limit( 1 )->fetch()["id"];


/**
 * Подсчёт итоговой стоимости посещения
 */

$paymentSummary = $visitDetails[ "price" ];

if ( $visitDetails[ "discount_type" ] == "fixed" ) $paymentSummary -= $visitDetails[ "discount_value" ];
if ( $visitDetails[ "discount_type" ] == "percent" ) $paymentSummary -= ($paymentSummary / 100) * $visitDetails[ "discount_value" ];

if ( $paymentSummary < 0 ) $paymentSummary = 0;

$clients = $API->DB->from( "visits_clients" )
    ->where( "visit_id", $pageDetail[ "row_id" ] )
    ->limit( 1 )
    ->fetch();


/**
 * Заполнение полей
 */

if ( $visitDetails[ "is_payed" ] == "Y" || ( $saleDetails && $saleDetails[ "status" ] != "error" ) ) {

    $formFieldValues =
        $API->DB->from( "sales" )
            ->innerJoin( "salesVisits ON salesVisits.sale_id = sales.id" )
            ->where( "salesVisits.visit_id", $pageDetail[ "row_id" ] )
            ->fetch();

    $formFieldValues[ "summary" ] = (float) $formFieldValues[ "summary" ];
    $formFieldValues[ "cash_sum" ] = (float) $formFieldValues[ "cash_sum" ];
    $formFieldValues[ "card_sum" ] = (float) $formFieldValues[ "card_sum" ];
    $formFieldValues[ "bonus_sum" ] = (float) $formFieldValues[ "bonus_sum" ];
    $formFieldValues[ "deposit_sum" ] = (float) $formFieldValues[ "deposit_sum" ];
    $formFieldValues[ "is_combined" ] = $formFieldValues[ "is_combined" ] == "Y";
    $formFieldValues[ "online_receipt" ] = $formFieldValues[ "online_receipt" ] == "Y";

    foreach ( $API->DB->from( "salesVisits" )
                  ->where( "sale_id", $saleDetails[ "sale_id" ] ) as $saleVisit )
        $formFieldValues[ "visits_ids" ][] = $saleVisit[ "visit_id" ];

    foreach ( $API->DB->from( "salesServices" )
                  ->where( "sale_id", $saleDetails[ "sale_id" ] ) as $saleService )
        $formFieldValues[ "pay_object" ][] = $saleService[ "service_id" ];

} else {

    $formFieldValues = [
        "cash_sum" => $paymentSummary,
        "pay_method" => "cash",
        "visits_ids" => [ $pageDetail[ "row_id" ] ],
        "store_id" => (int) $userDetails[ "store_id" ],
        "client_id" => $clients[ "client_id" ]
    ];

    $formFieldValues[ "summary" ] = $paymentSummary;

    foreach ( $API->DB->from( "visits_services" )
                  ->where( "visit_id", $pageDetail[ "row_id" ] ) as $visitService )
        $formFieldValues[ "pay_object" ][] = $visitService[ "service_id" ];

} //if. $visitDetails[ "is_payed" ] == "Y" || ( $saleDetails && $saleDetails[ "status" ] != "error" )

$formFieldValues[ "pay_type" ] = "sell";