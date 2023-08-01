<?php

/**
 * Получение детальной информации о посещении
 */

$visitDetails = $API->DB->from( "visits" )
    ->where( "id", $pageDetail[ "row_id" ] )
    ->limit( 1 )
    ->fetch();

/**
 * Получение информации о продаже
 */
$saleDetails =  $API->DB->from( "saleVisits" )
    ->where( "visit_id", $pageDetail[ "row_id" ] )
    ->fetch();


/**
 * Получение детальной информации о сотруднике
 */
$userDetails = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();

// Правка для dev аккаунта
if ( !$userDetails[ "store_id" ] ) $userDetails[ "store_id" ] = $API->DB->from( "stores" )->limit( 1 )->fetch()["id"];


/**
 * Подсчёт итоговой стоимости посещения
 */
$paymentSummary = $visitDetails[ "price" ];

/**
 * Получение id клиента
 */
$client = $API->DB->from( "visits_clients" )
    ->where( "visit_id", $pageDetail[ "row_id" ] )
    ->limit( 1 )
    ->fetch();


/**
 * Заполнение полей стандартными значениями
 */
$formFieldValues = [
    "sum_cash" => $paymentSummary,
    "pay_method" => "cash",
    "store_id" => (int) $userDetails[ "store_id" ],
    "client_id" => $client[ "client_id" ],
    "online_receipt" => true
];

$formFieldValues[ "summary" ] = $paymentSummary;
$formFieldValues[ "visits_ids" ][] = $pageDetail[ "row_id" ];


/**
 * Заполнение полей из продаж
 */
if ( $visitDetails[ "is_payed" ] == "Y" || ( $saleDetails && $saleDetails[ "status" ] != "error" ) ) {

    /**
     * Заполнение полей запросом в таблицу
     */
    $sale =
        $API->DB->from( "salesList" )
            ->innerJoin( "saleVisits ON saleVisits.sale_id = salesList.id" )
            ->where( "saleVisits.visit_id", $pageDetail[ "row_id" ] )
            ->fetch();

    if ( $sale ) {

        $formFieldValues = $sale;

        /**
         * Приведение данных к правильным типам
         */
        $formFieldValues[ "summary" ] = (float) $formFieldValues[ "summary" ];
        $formFieldValues[ "sum_cash" ] = (float) $formFieldValues[ "sun_cash" ];
        $formFieldValues[ "sum_card" ] = (float) $formFieldValues[ "sum_card" ];
        $formFieldValues[ "sum_bonus" ] = (float) $formFieldValues[ "sum_bonus" ];
        $formFieldValues[ "sum_deposit" ] = (float) $formFieldValues[ "sum_deposit" ];
        $formFieldValues[ "is_combined" ] = $formFieldValues[ "is_combined" ] == "Y";
        $formFieldValues[ "online_receipt" ] = $formFieldValues[ "online_receipt" ] == "Y";

        foreach ( $API->DB->from( "saleVisits" )
                      ->where( "sale_id", $saleDetails[ "sale_id" ] ) as $saleVisit )
            $formFieldValues[ "visits_ids" ][] = $saleVisit[ "visit_id" ];

    }

}

$formFieldValues[ "action" ] = "sell";