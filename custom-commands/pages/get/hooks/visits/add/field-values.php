<?php

/**
 * Автоподстановка филиала
 */

$userDetails = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();

if ( !$userDetails[ "store_id" ] ) $userDetails[ "store_id" ] = 1;

$formFieldValues = [
    "store_id" => (int) $userDetails[ "store_id" ]
];