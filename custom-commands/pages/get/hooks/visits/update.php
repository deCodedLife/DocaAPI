<?php


/**
 * Отключение кнопок "Удалить посещение", "Сохранить" и "Оплатить"
 */
if ( $pageDetail[ "row_detail" ][ "is_payed" ] == true ) {

    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 5 ] );
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 0 ] );

}

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
    $listedInSales = $API->DB->from( "salesList" )
        ->innerJoin( "saleVisits ON saleVisits.sale_id = salesList.id" )
        ->where( "saleVisits.visit_id", $pageDetail[ "row_detail" ][ "id" ] )
        ->limit( 1 )
        ->fetch();

    if ( !$listedInSales ) return false;

    if ( $listedInSales[ "status" ] == "done" ) return true;
    if ( $listedInSales[ "status" ] == "waiting" ) return true;

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
 * Кнопка "Печать договора"
 */
if ( $clientDetail[ "is_contract" ] == "Y" )
    unset( $pageScheme[ "structure" ][ 1 ][ "settings" ][ 2 ][ "body" ][ 0 ][ "components" ][ "buttons" ][ 0 ] );


/**
 * Подготовка
 */
foreach ( $pageDetail[ "row_detail" ][ "services_id" ] as $service ) {

    /**
     * Детальная информация об услуге
     */
    $serviceDetail = $API->DB->from( "services" )
        ->where( "id", $service->value )
        ->limit( 1 )
        ->fetch();

    $second_users = $API->DB->from( "services_second_users" )
        ->where( "service_id", $service->value );

    if ( count( $second_users ) != 0 ) $formFieldValues[ "assist_id" ][ "is_visible" ] = true;
    else $formFieldValues[ "assist_id" ][ "is_visible" ] = false;

    if ( $serviceDetail[ "preparation" ] )
        $response[ "detail" ][ "modal_info" ] = $serviceDetail[ "preparation" ];

} // foreach. $pageDetail[ "row_detail" ][ "services_id" ]