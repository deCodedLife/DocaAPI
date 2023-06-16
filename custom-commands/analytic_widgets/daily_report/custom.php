<?php


/**
* Суточный отчет
*/


/**
 * Получение фильтров
 */

$filter = [];
$expensesFilter = [];

/**
 * Получение начала и конца текущего дня
 */
if ( $requestData->start_at ) $filter[ "end_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $filter[ "end_at <= ?" ] = $requestData->end_at . " 23:59:59";
if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;
if ( $requestData->store_id ) $expensesFilter[ "store_id" ] = $requestData->store_id;
if ( $requestData->start_at ) $expensesFilter[ "date >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $expensesFilter[ "date <= ?" ] = $requestData->end_at . " 23:59:59";

/**
 * function. num_word() for declension of nouns after the numeral
 */

function num_word( $value, $words, $show = true ) {

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

/**
* Статистика компании
*/

$companyStatistic = [

    /**
    * Количество посещений
    */
    "visits_count" => 0,

    /**
    * Сумма посещений
    */
    "visits_sum" => 0,

    /**
     * Сумма продаж товаров
     */
    "products_sum" => 0,

    /**
     * Сумма расходов
     */
    "expenses_sum" => 0,
];


/**
* Получение посещений
*/

$companyVisits = $API->DB->from( "visits" )
    ->where( $filter );


/**
* Формирование графика посещений
*/

foreach ( $companyVisits as $userVisit ) {

$companyStatistic[ "visits_count" ]++;
$companyStatistic[ "visits_sum" ] += (float) $userVisit[ "price" ];

} // foreach. $companyVisits


/**
 * Получение продаж товаров ( доделать после реализации функционала продаж товара через раздел касса )
 */



/**
 * Получение расходов
 */

$companyExpenses = $API->DB->from( "expenses" )
    ->where( $expensesFilter );

/**
 * Формирование суммы расходов
 */

foreach ( $companyExpenses as $expens ) {

    $companyStatistic[ "expenses_sum" ] += (float) $expens[ "price" ];

} // foreach. $companyExpenses


$API->returnResponse(

    [
        [
            "value" => num_word( $companyStatistic[ "visits_count" ], [ 'посещение', 'посещения', 'посещений' ]),
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
            "value" => $companyStatistic[ "visits_sum" ] + $companyStatistic['products_sum'],
            "description" => "Поступления",
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
            "value" => $companyStatistic[ "expenses_sum" ],
            "description" => "Расход",
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
            "value" =>$companyStatistic[ "visits_sum" ] + $companyStatistic['products_sum'] - $companyStatistic[ "expenses_sum" ],
            "description" => "Итог",
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
