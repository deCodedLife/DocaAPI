<?php


/**
 * Получение детальной информации о пользователе
 */

$userDetails = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();

if ( !$userDetails[ "store_id" ] ) $userDetails[ "store_id" ] = $API->DB->from( "stores" )->limit( 1 )->fetch()["id"];



/**
 *  Создание транзакции
 */
$saleID = $API->DB->insertInto( "salesList" )
    ->values( [
        "employee_id" => (int) $API::$userDetail->id,
        "action" => $requestData->action,
        "sum_bonus" => $requestData->sum_bonus ?? 0,
        "sum_deposit" => $requestData->sum_deposit ?? 0,
        "sum_card" => $requestData->sum_card,
        "sum_cash" => $requestData->sum_cash,
        "status" => $requestData->pay_method == "legalEntity" ? "done" : "waiting",
        "store_id" => (int) $userDetails[ "store_id" ],
        "pay_method" => $requestData->pay_method,
        "online_receipt" => $requestData->online_receipt,
        "summary" => $requestData->summary,
        "client_id" => $requestData->client_id,
    ] )
    ->execute();


/**
 *  Заполнение посещений транзакции
 */

foreach ( $requestData->visits_ids ?? [] as $visit ) {

    $API->DB->insertInto( "saleVisits" )
        ->values( [
            "sale_id" => $saleID,
            "visit_id" => $visit
        ] )
        ->execute();

} // foreach. $requestData->visits_ids as $visit


foreach ( $requestData->products as $product ) {

    $product = (array) $product;
    $product[ "sale_id" ] = $saleID;

    $API->DB->insertInto( "salesProductsList" )
        ->values( $product )
        ->execute();

} // foreach. $requestData->pay_object as $service


