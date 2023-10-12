<?php

$defaultStore = $API->DB->from( "stores" )
    ->limit(1)
    ->fetch();

$userStoreID = $API->DB->from( "users_stores" )
    ->where( "user_id", $API::$userDetail->id )
    ->limit(1)
    ->fetch();

$storeDetails = $API->DB->from( "stores" )
    ->where( "id", $userStoreID[ "store_id" ] ?? 0 )
    ->fetch();

$formFieldValues[ "client_id" ][ "is_visible" ] = true;
$formFieldValues[ "action" ][ "value" ] = "sell";
$formFieldValues[ "online_receipt" ][ "is_visible" ] = true;
$formFieldValues[ "online_receipt" ][ "value" ] = true;
$formFieldValues[ "store_id" ] = [
    "is_visible" => true,
    "value" => $storeDetails[ "id" ] ?? $defaultStore[ "id" ]
];

$formFieldValues[ "discount_type" ][ "value" ] = "fixed";
$formFieldValues[ "discount_value" ][ "value" ] = 0;