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
    $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ] = [];

if ( $pageDetail[ "row_detail" ][ "is_payed" ] || $saleDetails )
    $pageScheme[ "structure" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 0 ][ "components" ][ "buttons" ] = [];