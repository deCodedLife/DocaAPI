<?php

// 1 Получить текущего пользователя
// 2 Получить филлиал
// 3 Получить данные по филиалу

$start = ( $requestData->start_at ?? date( 'Y-m-d' ) ) . " 00:00:00";
$end = ( $requestData->end_at ?? date( 'Y-m-d' ) ) . " 23:59:59";

$userDetails = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch();

$storeID = $requestData->store_id ?? $userDetails[ "store_id" ] ?? 62;

$cashboxFilter = [
    "store_id" => $storeID
];

if ( $requestData->start_at ) {

    $dayCount = 1;
    $cashboxDate = DateTime::createFromFormat( "Y-m-d", $requestData->start_at );
    if ( date( "l", strtotime( $start ) ) === "Monday" ) $dayCount = 2;
    $cashboxDate->modify( "-$dayCount day" );
    $cashboxFilter[ "created_at" ] = $cashboxDate->format( "Y-m-d 00:00:00" );

}

$incomeBalance = $API->DB->from( "cashboxBalances" )
    ->where( $cashboxFilter )
    ->orderBy( "created_at DESC" )
    ->limit( 1 )
    ->fetch();

$filters = [
    "action" => "sell",
    "status" => "done",
    "created_at >= ?" => $start,
    "created_at <= ?" => $end
];

if ( $requestData->store_id ) $filters[ "store_id" ] = $requestData->store_id;

$payments = $API->DB->from( "salesList" )
    ->where( $filters );

unset( $filters[ "action" ] );
unset( $filters[ "pay_method" ] );
unset( $filters[ "status" ] );

$expenses = $API->DB->from( "expenses" )
    ->where( $filters );

$summary = 0;
$expenses_summary = 0;

foreach ( $expenses as $expense ) {

    $expenses_summary += $expense[ "price" ];

}

foreach ( $payments as $payment ) {

    $summary += $payment[ "sum_cash" ];

}

$incomeBalance[ "balance" ] = $incomeBalance[ "balance" ] ?? 0;
$summary += $incomeBalance[ "balance" ] - $expenses_summary;

$API->returnResponse(

    [
        [
            "value" => number_format( floatval( $incomeBalance[ "balance" ] ), 2, '.', ' ' ),
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
            "value" => number_format( floatval( $summary ), 2, '.', ' '),
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
