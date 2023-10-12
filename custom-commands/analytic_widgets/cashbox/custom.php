<?php

// 1 Получить текущего пользователя
// 2 Получить филлиал
// 3 Получить данные по филиалу

$start = date( 'Y-m-d' ) . " 00:00:00";
$end = date( 'Y-m-d' ) . " 23:59:59";

$userDetails = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();

$storeID = $userDetails[ "store_id" ] ?? 1;

$incomeBalance = $API->DB->from( "cashboxBalances" )
    ->where( "store_id", $storeID )
    ->orderBy( "created_at DESC" )
    ->limit( 1 )
    ->fetch();

$payments = $API->DB->from( "salesList" )
    ->where( [
        "action" => "sell",
        "created_at >= ?" => $start,
        "created_at <= ?" => $end
    ] );

$expenses = $API->DB->from( "expenses" )
    ->where( [
        "created_at >= ?" => $start,
        "created_at <= ?" => $end
    ] );

$summary = 0;
$expenses_summary = 0;

foreach ( $expenses as $expense ) {

    $expenses_summary += $expense[ "price" ];

}

foreach ( $payments as $payment ) {

    $summary += $payment[ "summary" ];

}

$incomeBalance[ "balance" ] = $incomeBalance[ "balance" ] ?? 0;
$summary += $incomeBalance[ "balance" ] - $expenses_summary;

$API->returnResponse(

    [
        [
            "value" => number_format( intval( $incomeBalance[ "balance" ] ), 0, '.', ' ' ),
            "description" => "Входящий остаток",
            "icon" => "",
            "prefix" => "₽",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "type" => "char",
            "background" => "",
            "detail" => []
        ],
        [
            "value" => number_format( intval( $summary ), 0, '.', ' '),
            "description" => "Исходящий остаток",
            "icon" => "",
            "prefix" => "₽",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "type" => "char",
            "background" => "",
            "detail" => []
        ]
    ]

);
