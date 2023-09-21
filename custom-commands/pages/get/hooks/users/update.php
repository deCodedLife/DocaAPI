<?php

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date( 'Y-m-d', strtotime("-1 months") ) . " 00:00:00",
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ],
    [
        "property" => "status",
        "value" => "ended",
    ],
    [
        "property" => "user_id",
        "value" => ":id",
    ]

];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ][ 1 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date( 'Y-m-d', strtotime("-1 months") ) . " 00:00:00",
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ],
    [
        "property" => "status",
        "value" => "ended",
    ],
    [
        "property" => "user_id",
        "value" => ":id",
    ]

];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ][ 2 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date( 'Y-m-d', strtotime("-1 months") ) . " 00:00:00",
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ],
    [

        "property" => "status",
        "value" => "ended",

    ],
    [
        "property" => "user_id",
        "value" => ":id",
    ]

];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ][ 0 ][ "components" ][ "filters" ][ 3 ][ "settings" ][ "is_multi" ] = true;

$user = $API->DB->from( "users" )
    ->where( "id", $pageDetail[ "row_detail" ][ "id" ] )
    ->fetch();

$userStores = $API->DB->from( "users_stores" )
    ->where( "user_id", $pageDetail[ "row_detail" ][ "id" ] )
    ->limit( 1 )
    ->fetch();


if ( $userStores ) {

    $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [

        [
            "property" => "store_id",
            "value" => (int)$userStores[ "store_id" ]
        ],
        [
            "property" => "user_id",
            "value" => ":id"
        ]

    ];

}

if ( $user[ "salary_type" ] != "rate_kpi" ) {

    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ][ 1 ] );
    $pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ] = array_values( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ] );

}
