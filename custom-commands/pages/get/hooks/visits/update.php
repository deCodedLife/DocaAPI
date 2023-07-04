<?php

$clientsInfo = [];

$clients = $API->DB->from( "visits_clients" )
    ->where( "visit_id", $pageDetail[ "row_id" ] );

foreach ($clients as $client) {

    $clientDetail = $API->DB->from( "clients" )
        ->where("id", $client [ "client_id" ])
        ->limit(1)
        ->fetch();

    $phoneFormat = "+" . sprintf("%s (%s) %s-%s-%s",
            substr($clientDetail [ "phone" ], 0, 1),
            substr($clientDetail [ "phone" ], 1, 3),
            substr($clientDetail [ "phone" ], 4, 3),
            substr($clientDetail [ "phone" ], 7, 2),
            substr($clientDetail [ "phone" ], 9)
        );

    $clientsInfo[] = $clientDetail[ "last_name" ] . " " . $clientDetail[ "first_name" ] . " " . $clientDetail[ "patronymic" ] . ", " . $phoneFormat;

}

$formFieldValues[ "clients_info" ] = [ "is_visible" => true, "value" => $clientsInfo ];



/**
 * Определение того, стоит ли скрывать кнопку оплаты
 */
function shouldHideButton(): bool {

    global $API, $pageDetail;
    $isPayed = $pageDetail[ "row_detail" ][ "is_payed" ];

    if ( $isPayed ) return true;

    /**
     * Получение информации из таблицы продаж
     */
    $listedInSales = $API->DB->from( "saleVisits" )
        ->where( "visit_id", $pageDetail[ "row_detail" ][ "id" ] )
        ->limit( 1 )
        ->fetch();

    if ( !$listedInSales ) return false;

    $saleDetails = $API->DB->from( "sales" )
        ->where( "id", $listedInSales )
        ->fetch();

    if ( $saleDetails[ "status" ] == "done" ) return true;
    if ( $saleDetails[ "status" ] == "waiting" ) return true;

    // if sale status is error
    return false;

} // function shouldHideButton(): bool



/**
 * Отключение возможности оплатить посещения, в процессе и после оплаты
 */

if ( shouldHideButton() )
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 0 ] );




/**
 * Отключение кнопки "Акт вып работ"
 */
if ( $pageDetail[ "row_detail" ][ "status" ]->value === "planning" ) {

    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 3 ] );

}

/**
 * Отключение кнопки "Талон"
 */
if ( $pageDetail[ "row_detail" ][ "status" ]->value === "ended" ) {

    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 1 ] );

}

/**
 * Отключение кнопок "Удалить посещение", "Сохранить" и "Оплатить"
 */
if ( $pageDetail[ "row_detail" ][ "status" ]->value === "paidfor" ) {

    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 4 ] );
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 5 ] );
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 0 ] );

}

