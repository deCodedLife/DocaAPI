<?php

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 7 ][ "body" ][ 1 ][ "components" ][ "buttons" ][ 0 ][ "settings" ][ "context" ] = [
    "client_id" => $pageDetail[ "row_id" ]
];


/**
 * Кнопка "Печать договора"
 */
if ( $pageDetail[ "row_detail" ][ "is_contract" ] )
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 2 ] );

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [

    [
        "property" => "clients_id",
        "value" => $pageDetail[ "row_id" ],
    ],
    [
        "property" => "start_at",
        "value" => date( 'Y-m-d', strtotime("-1 month" ) )
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d', strtotime("+1 month" ) )
    ]

];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 1 ][ "settings" ][ "filters" ] = [

    [
        "property" => "clients_id",
        "value" => $pageDetail[ "row_id" ],
    ],
    [
        "property" => "start_at",
        "value" => date( 'Y-m-d', strtotime("-1 month" ) )
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d', strtotime("+1 month" ) )
    ]

];