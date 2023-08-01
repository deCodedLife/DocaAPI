<?php

/**
 * Получение филиалов, привязанных к сотруднику
 */

$users_stores = $API->DB->from( "users_stores" )
    ->where( "user_id", $requestData->context->row_id );

$stores = [];

foreach ( $users_stores as $user_store ) {

    $storeDetail = $API->DB->from( "stores" )
        ->where( "id", $user_store[ "store_id" ] )
        ->limit( 1 )
        ->fetch();


    $stores[] = [
        "title" => $storeDetail[ "title" ],
        "value" => $storeDetail[ "id" ]
    ];

} // foreach. $users_stores


$response[ "data" ][ 0 ][ "settings" ][ "areas" ][ 1 ][ "blocks" ][ 0 ][ "fields" ][ 1 ][ "list" ] = $stores;