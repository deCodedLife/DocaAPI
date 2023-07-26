<?php

/**
 * @file
 * Отчет "группы услуг
 */
$returnServices = [];

/**
 * Фильтр для продаж
 */
$salesFilter[ "status" ] = "done";
$salesFilter[ "type" ] = "service";
$salesFilter[ "action" ] = "sell";

/**
 * Получение списка продаж
 */
$salesList = $API->DB->from( "salesList" )
    ->leftJoin( "salesProductsList ON salesProductsList.sale_id = salesList.id" )
    ->select( null )->select( [
        "salesList.id",
        "salesList.created_at",
        "salesProductsList.product_id",
        "salesProductsList.cost",
        "salesProductsList.amount"
    ] )
    ->where( $salesFilter )
    ->orderBy( "salesList.created_at desc" )
    ->limit( 0 );

/**
 * Получение груп услуг
 */
$serviceGroups = $API->DB->from( "serviceGroups" )
    ->where( "is_active", "Y" );

/**
 * Получение текущего времени
 */
$currentDateTime = new DateTime();

/**
 * Обход груп услуг
 */
foreach ( $serviceGroups as $serviceGroup ) {

    /**
     * Обход продаж
     */
    foreach ( $salesList as $sale ) {

        /**
         * Получение детальной информации об услуги
         */
        $serviceDetail = $API->DB->from( "services" )
            ->where( "id",  $sale[ "product_id" ] )
            ->limit( 1 )
            ->fetch();

        /**
         * Заполнение списка
         */
        if ( $serviceDetail[ "category_id" ] == $serviceGroup[ "id" ] ) {

            $returnServices[$serviceGroup[ "id" ]][ "title" ] = $serviceGroup[ "title" ];

            if ( $sale[ "created_at" ] >= $currentDateTime->format( "Y-m-01 00:00:00" ) ){

                $returnServices[$serviceGroup[ "id" ]][ "sum_one" ] = $returnServices[$serviceGroup[ "id" ]][ "sum_one" ] + $sale[ "amount" ] * $sale[ "cost" ];

            }

            if ( $sale[ "created_at" ] >= $currentDateTime->modify( "-1 month" )->format( "Y-m-01 00:00:00" ) ) {

                $returnServices[$serviceGroup[ "id" ]][ "sum_two" ] = $returnServices[$serviceGroup[ "id" ]][ "sum_two" ] + $sale[ "amount" ] * $sale[ "cost" ];

            }

            if ( $sale[ "created_at" ] >= $currentDateTime->modify( "-2 month" )->format( "Y-m-01 00:00:00" ) ) {

                $returnServices[$serviceGroup[ "id" ]][ "sum_three" ] = $returnServices[$serviceGroup[ "id" ]][ "sum_three" ] + $sale[ "amount" ] * $sale[ "cost" ];

            }

        }
    }


} // foreach .$services

$response[ "data" ] = array_values($returnServices);
