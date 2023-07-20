<?php

/**
 * @file
 * Список "продажа услуг
 */

/**
 * Сформированный список
 */
$returnServices = [];

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
 * Получение услуг
 */

$services = $API->DB->from( "services" )
    ->where( $servicesFilter );

/**
 * Получение продаж
 */

$salesList = $API->DB->from( "salesList" )
    ->leftJoin( "salesProductsList ON salesProductsList.sale_id = salesList.id" )
    ->select( null )->select( [
        "salesList.id",
        "salesProductsList.product_id",
        "salesProductsList.cost",
        "salesProductsList.amount"
    ] )
    ->where( $salesFilter )
    ->orderBy( "salesList.created_at desc" )
    ->limit( 0 );

foreach ( $services as $service ) {

    /**
     * Колличество услуги в продажах
     */
    $count = 0;

    /**
     * Сумма услуги в продажах
     */
    $sum = 0;

    /**
     * Обход Продаж
     */
    foreach ( $salesList as $sale ) {

        /**
         * Проверка наличия услуги в продажах
         */
        if ( $sale[ "product_id" ] == $service[ "id" ] ) {

            $count += $sale[ "amount" ];
            $sum += $sale[ "amount" ] * $sale[ "cost" ];

        } //  if ( $sale[ "product_id" ] == $service[ "id" ])

    } // foreach .$salesList

    /**
     * Проверка на наличие услуги в продажах
     */
    if ( $count != 0 ) {

        $returnServices[] = [
            "id" => $service[ "id" ],
            "title" => $service[ "title" ],
            "count" => $count,
            "date" => $service[ "date" ],
            "sum" => $sum
        ];

    } // if ( $count != 0 )

} // foreach .$services

$response[ "data" ] = $returnServices;
