<?php

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 6 ][ "body" ][ 1 ][ "components" ][ "buttons" ][ 0 ][ "settings" ][ "context" ] = [
    "client_id" => $pageDetail[ "row_id" ]
];


/**
 * Кнопка "Печать договора"
 */
if ( $pageDetail[ "row_detail" ][ "is_contract" ] )
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 0 ] );

