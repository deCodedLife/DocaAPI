<?php
/**
 * @file
 * Список "продажа услуг
 */

/**
 * Сформированный список
 */
$returnVisits = [];

/**
 * Фильтр Услуг
 */
$servicesFilter = [];

/**
 * Фильтр Продаж
 */
$salesFilter = [];

/**
 * Формирование фильтров
 */
$salesFilter[ "status" ] = "done";
$salesFilter[ "type" ] = "service";
$salesFilter[ "action" ] = "sell";
if ( $requestData->start_at ) $salesFilter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $salesFilter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";
if ( $requestData->store_id ) $salesFilter[ "store_id" ] = $requestData->store_id;


$servicesFilter[ "is_active" ] = "Y";
if ( $requestData->id ) $servicesFilter[ "id" ] = $requestData->id;
if ( $requestData->category_id ) $servicesFilter[ "category_id" ] = $requestData->category_id;

/**
 * Список услуг
 */
$services = $API->DB->from( "services" )
    ->where($servicesFilter);


/**
 * Получение продаж
 */

$salesList = $API->DB->from( "salesList" )
    ->leftJoin( "salesProductsList ON salesProductsList.sale_id = salesList.id" )
    ->select(null)->select([
        "salesList.id",
        "salesProductsList.product_id",
        "salesProductsList.cost",
        "salesProductsList.amount"
    ])
    ->where( $salesFilter )
    ->orderBy( "salesList.created_at desc" )
    ->limit ( 0 );

foreach ( $services as $service ) {

    $count = 0;
    $sum = 0;

    foreach ( $salesList as $sale ) {

        if ( $sale[ "product_id" ] == $service[ "id" ]) {
            
            $count += $sale[ "amount" ];
            $sum += $sale[ "amount" ] * $sale[ "cost" ];

        }

    }

    $returnVisits[] = [
        "id" => $service[ "id" ],
        "title" => $service[ "title" ],
        "price" => $sum / $count,
        "discount_value" => $sum / $count * $count - $sum,
        "count" => $count,
        "date" => $service[ "date" ],
        "sum" => $sum
    ];

}

$response["data"] = $returnVisits;
