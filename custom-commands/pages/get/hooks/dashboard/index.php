<?php

$userDetails = $API->DB->from( "users_stores" )
    ->innerJoin( "users on users.id = users_stores.user_id" )
    ->where( "users.id", $API::$userDetail->id )
    ->limit( 1 )
    ->fetch();

$serviceGroup = $API->DB->from( "serviceGroups" )
    ->where( "is_active", "Y" )
    ->limit( 1 )
    ->fetch();

$workDay = $API->DB->from( "workDays" )
    ->orderBy( "event_from desc" )
    ->limit( 1 )
    ->fetch();

$pageScheme[ "structure" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date( 'Y-m-d' ) . " 00:00:00",
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ],
    [
        "property" => "store_id",
        "value" => $userDetails[ "store_id" ] ?? $API->DB->from( "stores" )
                ->limit(1)->fetch()[ "id" ]
    ]
];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ "filters" ] = [
    [
        "property" => "month",
        "value" => date( 'Y-m-d' ),
    ],
    [
        "property" => "id",
        "value" => $serviceGroup[ "id" ]
    ]
];

$pageScheme[ "structure" ][ 2 ][ "settings" ][ "filters" ] = [
    [
        "property" => "month",
        "value" => date( 'Y-m-d' ),
    ],
    [
        "property" => "id",
        "value" => $workDay[ "user_id" ]
    ]
];

$pageScheme[ "structure" ][ 4 ][ "settings" ][ "filters" ] = [
    "performers_article" => "user_id",
    "performers_table" => "users",
    "performers_title" => "first_name",
    "store_id" => $userDetails[ "store_id" ] ?? $API->DB->from( "stores" )
            ->limit(1)->fetch()[ "id" ],
    "start_at" =>  date( 'Y-m-d' )
];