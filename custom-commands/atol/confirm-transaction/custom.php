<?php



/**
 * Изменение статуса оплаты
 */

$API->DB->update( "salesList" )
    ->set( "status", "done" )
    ->where( "id", $requestData->sale_id )
    ->execute();


/**
 * Изменение статуса посещений
 */

$sale_visits = $API->DB->from( "saleVisits" )
    ->where( "sale_id", $requestData->sale_id );

$API->DB->update( "visits" )
    ->innerJoin( "saleVisits ON saleVisits.visit_id = visits.id" )
    ->set( "visits.is_payed", 'Y' )
    ->where( "saleVisits.sale_id", $requestData->sale_id )
    ->execute();



/**
 * Списание бонусов и депозита
 */

$saleDetails = $API->DB->from( "salesList" )
    ->where( "id", $requestData->sale_id )
    ->fetch();

$clientDetails = $API->DB->from( "clients" )
    ->where( "id", $saleDetails[ "client_id" ] )
    ->fetch();

if ( $saleDetails[ "action" ] == "sell" ) {

    $API->DB->update( "clients" )
        ->set( [
            "bonuses" => $clientDetails[ "bonuses" ] - $saleDetails[ "sum_bonus" ] ?? 0,
            "deposit" => $clientDetails[ "deposit" ] - $saleDetails[ "sum_deposit" ] ?? 0,
        ] )
        ->where( "id", $saleDetails[ "client_id" ] )
        ->execute();

    $API->DB->insertInto( "bonusHistory" )
        ->values( [
            "user_id" => $API::$userDetail->id ?? 1,
            "client_id" => $saleDetails[ "client_id" ],
            "action" => "Пополнение",
            "replenished" => $saleDetails[ "sum_bonus" ]
        ] )
        ->execute();

} // if ( $saleDetails[ "action" ] == "sell" )



if ( $saleDetails[ "action" ] == "sellReturn" ) {

    $API->DB->update( "clients" )
        ->set( [
            "bonuses" => $clientDetails[ "bonuses" ] + $saleDetails[ "sum_bonus" ] ?? 0,
            "deposit" => $clientDetails[ "deposit" ] + $saleDetails[ "sum_deposit" ] ?? 0
        ] )
        ->where( "id", $saleDetails[ "client_id" ] )
        ->execute();

    $API->DB->insertInto( "bonusHistory" )
        ->values( [
            "user_id" => $API::$userDetail->id ?? 1,
            "client_id" => $saleDetails[ "client_id" ],
            "action" => "Списание",
            "replenished" => -$saleDetails[ "sum_bonus" ]
        ] )
        ->execute();

} // if ( $saleDetails[ "action" ] == "sellReturn" )



$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );
$API->addEvent( "salesList" );