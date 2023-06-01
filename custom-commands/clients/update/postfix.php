<?php
$client = $API->DB->from( "clients" )
    ->where( "id", $requestData->id )
    ->limit( 1 )
    ->fetch();

$user = $API::$userDetail;

/**
 * Бонусы - пополнение
 */

if ($requestData->bonuses_replenishment) {

    $API->DB->update( "clients" )
    ->set( [
        "bonuses" => $client['bonuses'] + $requestData->bonuses_replenishment
    ] )
    ->where( "id", $requestData->id )
    ->execute();


    $API->DB->insertInto( "bonusHistory" )
    ->values( [
        "user_id" => $user->id,
        "client_id" => $requestData->id,
        "replenished" => $requestData->bonuses_replenishment
    ] )
    ->execute();
    $API->returnResponse($API::$userDetail);
}

/**
 * Депозит
 */

if ($requestData->deposit_replenishment) {

    $API->DB->update( "clients" )
        ->set( [
            "deposit" => $client['deposit'] + $requestData->deposit_replenishment
        ] )
        ->where( "id", $requestData->id )
        ->execute();


    $API->DB->insertInto( "depositHistory" )
        ->values( [
            "user_id" => $user->id,
            "client_id" => $requestData->id,
            "replenished" => $requestData->deposit_replenishment
        ] )
        ->execute();
}