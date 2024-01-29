<?php
//ini_set( "display_errors", true );

$amountOfPhysicalPayments = $requestData->summary ?? 0;
$saleSummary = 0;
$sum_card = $requestData->sum_card ?? 0;
$sum_cash = $requestData->sum_cash ?? 0;


/**
 * Обновление полей
 */
$formFieldsUpdate = [];

/**
 * Определение значений
 */
$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];

if ( $requestData->action !== "deposit" ) {

    require_once( $publicAppPath . '/custom-libs/sales/include.php' );
    require_once( $publicAppPath . '/custom-libs/sales/projects/doca/business_logic.php' );

} else {

    $isReturn = false;
    $sum_card = $requestData->sum_card ?? 0;
    $sum_cash = $requestData->sum_cash ?? 0;
    $saleSummary = $sum_cash + $sum_card;

    $formFieldsUpdate[ "products" ][ "value" ][] = [
        "title" => "Пополнение депозита",
        "type" => "product",
        "cost" => $requestData->summary,
        "amount" => 1,
        "product_id" => 0
    ];

} // if ( $requestData->action !== "deposit" )

$clientEntity = $API->DB->from( "legal_entity_clients" )
    ->where( "client_id", $requestData->client_id )
    ->fetch();


/**
 * Подсчёт суммы списания с карты и наличными в зависимости от выбранного типа оплаты
 */
$formFieldsUpdate[ "sum_cash" ][ "is_disabled" ] = true;
$formFieldsUpdate[ "sum_card" ][ "is_disabled" ] = true;

if( !$requestData->pay_method ) {

    $formFieldsUpdate[ "sum_card" ][ "is_visible" ] = false;
    $formFieldsUpdate[ "sum_cash" ][ "is_visible" ] = false;

}

if ( $requestData->pay_method == "card" ) {

    $formFieldsUpdate[ "sum_cash" ][ "is_visible" ] = false;
    $formFieldsUpdate[ "sum_card" ][ "is_visible" ] = true;

    $sum_card = $amountOfPhysicalPayments ?? $saleSummary;
    $sum_cash = 0;


} // if. $requestData->pay_method == "card"

if ( $requestData->pay_method == "parts" ) {

    $formFieldsUpdate[ "sum_card" ][ "is_visible" ] = true;
    $formFieldsUpdate[ "sum_cash" ][ "is_visible" ] = true;

    $formFieldsUpdate[ "sum_cash" ][ "is_disabled" ] = false;

    $sum_card = $amountOfPhysicalPayments - $sum_cash;
    $sum_card = $sum_cash >= ($amountOfPhysicalPayments ?? $saleSummary) ? 0 : $sum_card;

} // if. $requestData->pay_method == "parts"

if ( $requestData->pay_method == "cash" ) {

    $formFieldsUpdate[ "sum_card" ][ "is_visible" ] = false;
    $formFieldsUpdate[ "sum_cash" ][ "is_visible" ] = true;
    $sum_cash = $amountOfPhysicalPayments ?? $saleSummary;
    $sum_card = 0;

} // if. $requestData->pay_method == "cash"

if ( $requestData->pay_method == "legalEntity" && $clientEntity ) {

    $formFieldsUpdate[ "sum_card" ][ "is_visible" ] = false;
    $formFieldsUpdate[ "sum_cash" ][ "is_visible" ] = false;
    $formFieldsUpdate[ "sum_entity" ][ "value" ] = $saleSummary;
    $formFieldsUpdate[ "sum_entity" ][ "is_visible" ] = true;

    $sum_cash = 0;
    $sum_card = 0;

} else {

    $formFieldsUpdate[ "sum_entity" ][ "is_visible" ] = false;

}



/**
 * Заполнение и отправка формы
 */
$clientDetails = $API->DB->from( "clients" )
    ->where( "id", $requestData->client_id ?? 1 )
    ->fetch();



$formFieldsUpdate[ "sum_cash" ][ "value" ] = max( $sum_cash, 0 );
$formFieldsUpdate[ "sum_card" ][ "value" ] = max( $sum_card, 0 );

if ( $requestData->action !== "deposit" )
    $formFieldsUpdate[ "summary" ][ "value" ] = $saleSummary;


if ( $isReturn ) {

    $formFieldsUpdate[ "sum_deposit" ][ "is_visible" ] = false;
    $formFieldsUpdate[ "sum_bonus" ][ "is_visible" ] = false;

} // if ( isReturn )

$API->returnResponse( $formFieldsUpdate );
