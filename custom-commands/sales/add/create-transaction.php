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

$saleID = $API->DB->insertInto( "sales" )
    ->values( [
        "employee" => (int) $API::$userDetail->id,
        "pay_type" => $requestData->pay_type,
        "bonus_sum" => $requestData->return_bonuses === 'N' ? 0 : $requestData->bonus_sum,
        "deposit_sum" => $requestData->return_deposit === "N" ? 0 : $requestData->deposit_sum,
        "card_sum" => $requestData->card_sum,
        "cash_sum" => $requestData->cash_sum,
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

    $API->DB->insertInto( "salesVisits" )
        ->values( [
            "sale_id" => $saleID,
            "visit_id" => $visit
        ] )
        ->execute();

} // foreach. $requestData->visits_ids as $visit



/**
 *  Заполнение услуг транзакции
 */

foreach ( $requestData->pay_object as $service ) {

    $API->DB->insertInto( "salesServices" )
        ->values( [
            "sale_id" => $saleID,
            "service_id" => $service
        ] )
        ->execute();

} // foreach. $requestData->pay_object as $service
