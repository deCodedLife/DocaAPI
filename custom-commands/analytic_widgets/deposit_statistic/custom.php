<?php


/**
 * Отчет "Статистика клиента
 */

/**
 * Статистика клиента
 */
$clientStatistic = [

    /**
     * Количество посещений
     */
    "deposit_count" => 0,

    /**
     * Сумма посещений
     */
    "deposit_sum" => 0,


];

$clientInfo = $API->DB->from( "clients" )
    ->where( "id", $requestData->client_id )
    ->fetch();

/**
 * Получение посещений Сотрудника
 */
$salesList = $API->DB->from( "salesList" )
    ->where( "(sum_deposit > :sum_deposit OR action = :action)", [
        ":sum_deposit" => 0,
        ":action" => "deposit",
    ] )
    ->where( "client_id = :client_id", [
        ":client_id" => $requestData->client_id,
    ] )
    ->fetchAll( "id" );


/**
 * Формирование пополнений
 */
$clientStatistic[ "deposit_count" ] = count( $salesList );


function num_word( $value, $words, $show = true ) { // function. num_word() for declension of nouns after the numeral

    $num = $value % 100;

    if ( $num > 19 ) $num = $num % 10;

    $out = ( $show ) ?  $value . ' ' : '';
    switch ( $num ) {

        case 1:  $out .= $words[0]; break;

        case 2:
        case 3:
        case 4:  $out .= $words[1]; break;
        default: $out .= $words[2]; break;

    }

    return $out;
}

$API->returnResponse(

    [
        [
            "value" => num_word ( number_format( $clientStatistic[ "deposit_count" ], 0, '.', ' ' ), [ 'операция', 'операции', 'операций' ]),
            "description" => "всего",
            "icon" => "",
            "prefix" => "",
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
            "value" => number_format ( $clientInfo[ "deposit" ], 0, '.', ' ' ),
            "description" => "Баланс",
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
