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
        "sum_bonus" => $requestData->sum_bonus,
        "sum_deposit" => $requestData->sum_deposit,
        "sum_card" => $requestData->sum_card,
        "sum_cash" => $requestData->sum_cash,
        "status" => "waiting",
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

foreach ( $requestData->visits_ids as $visit ) {

    $API->DB->insertInto( "saleVisits" )
        ->values( [
            "sale_id" => $saleID,
            "visit_id" => $visit
        ] )
        ->execute();

} // foreach. $requestData->visits_ids as $visit



/**
 *  Заполнение услуг транзакции
 */
$products = [];

foreach ( $requestData->products as $index => $service ) {

    $detailedInfo = $API->DB->from( "services" )
        ->where( "id", $service )
        ->fetch();

    $products[] = $detailedInfo;

}

foreach ( $products as $service ) {

    $API->DB->insertInto( "salesProductsList" )
        ->values( [
            "title" => $service[ "title" ],
            "type" => "service",
            "sale_id" => $saleID,
            "product_id" => (int) $service[ "id" ],
            "cost" => (float) $service[ "price" ],
            "amount" => 1
        ] )
        ->execute();

} // foreach. $requestData->pay_object as $service
