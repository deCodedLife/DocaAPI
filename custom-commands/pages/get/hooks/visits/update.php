<?php


/**
 * Получение продажи, связанной с посещением
 */

$saleDetails = $API->DB->from( "salesVisits" )
    ->where( "visit_id", $pageDetail[ "row_detail" ][ "id" ] )
    ->limit( 1 )
    ->fetch();

/**
 * Отключение возможности оплатить посещения, после оплаты
 */

if ( $pageDetail[ "row_detail" ][ "status" ]->value === "ended" || $saleDetails )
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 2 ]);
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 3 ]);

if ( $pageDetail[ "row_detail" ][ "is_payed" ] || $saleDetails )
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 0 ]);
