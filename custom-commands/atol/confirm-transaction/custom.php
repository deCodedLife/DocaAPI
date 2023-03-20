<?php



/**
 * Изменение статуса оплаты
 */

$API->DB->update( "sales" )
    ->set( "status", "done" )
    ->where( "id", $requestData->sale_id )
    ->execute();



/**
 * Изменение статуса посещений
 */

$sale_visits = $API->DB->from( "salesVisits" )
    ->where( "sale_id", $requestData->sale_id );

foreach ( $sale_visits as $sale_visit ) {

    $API->DB->update( "visits" )
        ->set( [
            "is_payed" => 'Y'
        ] )
        ->where( "id", $sale_visit[ "visit_id" ] )
        ->execute();

} // foreach.  $sale_visits as $sale_visit



/**
 * Списание бонусов и депозита
 */

$saleDetails = $API->DB->from( "sales" )
    ->where( "id", $requestData->sale_id )
    ->fetch();

$clientDetails = $API->DB->from( "clients" )
    ->where( "id", $saleDetails[ "client_id" ] )
    ->fetch();

$API->DB->update( "clients" )
    ->set( [
        "bonuses" => $clientDetails[ "bonuses" ] - $saleDetails[ "bonus_sum" ] ?? 0,
        "deposit" => $clientDetails[ "deposit" ] - $saleDetails[ "deposit_sum" ] ?? 0,
    ] )
    ->where( "id", $saleDetails[ "client_id" ] )
    ->execute();