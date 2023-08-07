<?php

// 1 Получить текущего пользователя
// 2 Получить филлиал
// 3 Получить данные по филиалу

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
        "created_at >= ?" => date( 'Y-m-d' ) . " 00:00:00",
        "created_at <= ?" => date( 'Y-m-d' ) . " 23:59:59"
    ] );

$summary = 0;

foreach ( $payments as $payment ) {

    $summary += $payment[ "summary" ];

}

$incomeBalance[ "balance" ] = $incomeBalance[ "balance" ] ?? 0;
$summary += $incomeBalance[ "balance" ];

$API->returnResponse(

    [
        [
            "value" => $incomeBalance[ "balance" ],
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
            "value" => $summary,
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
