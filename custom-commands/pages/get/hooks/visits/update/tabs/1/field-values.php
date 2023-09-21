<?php

/**
 * Получение информации об услугах
 */

$visitDetails = $API->DB->from( "visits" )
    ->where( "id", $pageDetail[ "row_id" ] )
    ->limit( 1 )
    ->fetch();

$saleDetails =  $API->DB->from( "saleVisits" )
    ->where( "visit_id", $pageDetail[ "row_id" ] )
    ->fetch() ?? null;

$servicesInfo = [];
$visitServices = [];
$isPayed = false;
$visitsList = [];

if ( $visitDetails[ "is_payed" ] == "Y" || ( $saleDetails && $saleDetails[ "status" ] != "error" ) ) {

    $visitServices = $API->DB->from( "salesProductsList" )
        ->innerJoin( "salesList ON salesList.id = salesProductsList.sale_id" )
        ->where( [
            "salesProductsList.type = ?" => "service",
            "salesList.id = ?" => $saleDetails[ "sale_id" ]
        ] );
    $isPayed = true;

    foreach ( $API->DB->from( "saleVisits" )
                  ->where( "sale_id", $saleDetails[ "sale_id" ] ) as $saleVisit )
        $visitsList[] = $saleVisit[ "visit_id" ];

} else {

    $visitServices = $API->DB->from( "visits_services" )
        ->where( "visit_id", $pageDetail[ "row_detail" ][ "id" ] );

    $visitsList[] = $pageDetail[ "row_id" ];

} // if ( $visitDetails[ "is_payed" ] == "Y" || ( $saleDetails && $saleDetails[ "status" ] != "error" ) )



foreach ( $visitServices as $service ) {

    $serviceDetails = $API->DB->from( "services" )
        ->where( "id", $isPayed ? $service[ "product_id" ] : $service[ "service_id" ] )
        ->limit( 1 )
        ->fetch();

    $servicesInfo[] = $serviceDetails[ "title" ];

} // foreach. $visitServices



$generatedTab[ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 0 ][ "value" ] = $visitsList;
$generatedTab[ "settings" ][ "areas" ][ 0 ][ "blocks" ][ 0 ][ "fields" ][ 1 ][ "value" ] = $servicesInfo;




/**
 * Заполнение описаний полей "Списать бонусов" и "Списать с депозита"
 */


$visit = $API->DB->from( "visits_clients" )
    ->where( "visit_id", $pageDetail[ "row_detail" ][ "id" ] )
    ->limit( 1 )
    ->fetch();

$client = $API->DB->from( "clients" )
    ->where( "id", $visit["client_id"] )
    ->limit( 1 )
    ->fetch();

$generatedTab[ "settings" ][ "areas" ][ 1 ][ "blocks" ][ 0 ][ "fields" ][ 1 ][ "description" ] = "Ваш баланс: " . number_format( $client[ "deposit" ] , 0, '.', ' ' ) . " бонусов";
$generatedTab[ "settings" ][ "areas" ][ 1 ][ "blocks" ][ 0 ][ "fields" ][ 2 ][ "description" ] = "Ваш баланс: " . number_format( $client[ "bonuses" ], 0, '.', ' ' ) . " ₽";


/**
 * Получение информации о юр лице клиента
 */
$clientEntity = $API->DB->from( "legal_entity_clients" )
    ->where( "client_id", $client[ "id" ] )
    ->fetch();

if ( $clientEntity ) {

    $legalEntity = $API->DB->from( "legal_entities" )
        ->where( "id", $clientEntity[ "legal_entity_id" ] )
        ->fetch();

    $generatedTab[ "settings" ][ "areas" ][ 1 ][ "blocks" ][ 0 ][ "fields" ][ 3 ][ "description" ] = "{$legalEntity[ "title" ]}: " . number_format( $legalEntity[ "balance" ], 0, '.', ' ' ) . " ₽";
    $generatedTab[ "settings" ][ "areas" ][ 1 ][ "blocks" ][ 0 ][ "fields" ][ 3 ][ "is_visible" ] = true;

} // if ( $clientEntity )


/**
 * Получение детальной информации о сотруднике
 */
$userDetails = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();
