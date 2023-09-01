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

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ][ 0 ][ "components" ][ "filters" ][ 3 ][ "settings" ][ "is_multi" ] = true;

$user = $API->DB->from( "users" )
    ->where( "id", $pageDetail[ "row_detail" ][ "id" ] )
    ->fetch();

if ( $user[ "salary_type" ] != "rate_kpi" ) {

    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ][ 1 ] );
    $pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ] = array_values( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ] );

}