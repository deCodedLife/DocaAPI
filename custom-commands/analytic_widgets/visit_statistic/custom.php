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
    "visits_count" => 0,

    /**
    * Сумма посещений
    */
    "visits_sum" => 0,

    /**
    * Средний чек
    */
    "medium_visit_price" => 0,

    /**
    * Минимальная цена
    */
    "min_visit_price" => 0,

    /**
    * Максимальная цена
    */
    "max_visit_price" => 0,

    /**
    * Дата последнего посещения
    */
    "last_visit_date" => ""

];

/**
* Получение посещений Сотрудника
*/
$clientVisits = $API->sendRequest( "history_visits", "get", $requestData );
/**
* Формирование графика посещений
*/
foreach ( $clientVisits as $userVisit ) {

$clientStatistic[ "visits_count" ]++;
$clientStatistic[ "visits_sum" ] += (float) $userVisit->price;

} // foreach. $userVisits


function num_word( $value, $words, $show = true ) { // function. num_word() for declension of nouns after the numeral

    $num = $value % 100;

    if ( $num > 19 ) {

        $num = $num % 10;

    }

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
            "value" => num_word( $clientStatistic[ "visits_count" ], [ 'посещение', 'посещения', 'посещений' ]),
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
            "value" => number_format( intval( $clientStatistic[ "visits_sum" ] ), 0, '.', ' '),
            "description" => "Сумма посещений",
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
