<?php

/**
 * Получение информации об услугах
 */

$servicesInfo = [];

$visitServices = $API->DB->from( "visits_services" )
    ->where( "visit_id", $pageDetail[ "row_detail" ][ "id" ] );

foreach ( $visitServices as $service ) {

    $serviceDetails = $API->DB->from( "services" )
        ->where( "id", $service[ "service_id" ] )
        ->limit( 1 )
        ->fetch();

    $servicesInfo[] = $serviceDetails[ "id" ];

} // foreach. $visitServices

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

$generatedTab[ "settings" ][ "areas" ][ 1 ][ "blocks" ][ 0 ][ "fields" ][ 1 ][ "description" ] = "Ваш баланс: " . $client["deposit"] . " бонусов";
$generatedTab[ "settings" ][ "areas" ][ 1 ][ "blocks" ][ 0 ][ "fields" ][ 2 ][ "description" ] = "Ваш баланс: " . $client["bonuses"] . " ₽";

