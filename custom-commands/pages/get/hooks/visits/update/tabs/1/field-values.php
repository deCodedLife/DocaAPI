<?php

/**
 * Получение информации об услугах
 */

$servicesInfo = [];
$visitServices = [];
$isPayed = false;
$visitsList = [];


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

$generatedTab[ "settings" ][ "areas" ][ 1 ][ "blocks" ][ 0 ][ "fields" ][ 2 ][ "description" ] = "Ваш баланс: " . number_format( $client[ "deposit" ] , 0, '.', ' ' ) . " бонусов";
$generatedTab[ "settings" ][ "areas" ][ 1 ][ "blocks" ][ 0 ][ "fields" ][ 1 ][ "description" ] = "Ваш баланс: " . number_format( $client[ "bonuses" ], 0, '.', ' ' ) . " ₽";


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

} // if ( $clientEntity )


/**
 * Получение детальной информации о сотруднике
 */
$userDetails = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();
