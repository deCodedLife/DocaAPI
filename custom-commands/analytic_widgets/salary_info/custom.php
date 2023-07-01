<?php

/**
 * Период, за который высчитывается зарплата
 */
$start_at = date( "Y-m-" ) . "01 00:00:00";
$end_at = date( "Y-m-t" ) . " 23:59:59";

/**
 * Фиксированная часть зарплаты
 */
$salary_fixed = 0;

/**
 * Процент выполнения KPI
 */
$salary_kpi_percent = 0;

/**
 * Бонус за выполнение KPI
 */
$salary_kpi_value = 0;


/**
 * Детальная информация о пользователе
 */

$userDetail = $API->DB->from( "users" )
    ->where( "id", $requestData->user_id )
    ->limit( 1 )
    ->fetch();

$salary_fixed = $userDetail[ "salary" ];


/**
 * Процент от продаж
 */

if ( $userDetail[ "is_percent" ] === "Y" ) {

    /**
     * Общая сумма продаж
     */
    $totalSell = 0;


    /**
     * Список посещений
     */
    $visits = $API->sendRequest(
        "visits",
        "get",
        [
            "users_id" => $requestData->user_id,
            "start_at" => $start_at,
            "end_at" => $end_at,
            "status" => "ended"
        ]
    );


    /**
     * Обработка услуг
     */

    foreach ( $visits as $visit ) {

        foreach ( $visit->services_id as $service ) {

            /**
             * Детальная информация об услуге
             */

            $serviceDetail = $API->DB->from( "services" )
                ->where( "id", $service->value )
                ->limit( 1 )
                ->fetch();


            /**
             * Процент услуги
             */
            $servicePercent = $API->DB->from( "services_user_percents" )
                ->where( [
                    "row_id" => $requestData->user_id,
                    "service_id" => $service->value
                ] )
                ->limit( 1 )
                ->fetch();

            if ( !$servicePercent ) continue;


            if ( $servicePercent[ "fix_sum" ] ) $salary_kpi_value += $servicePercent[ "fix_sum" ];
            if ( $servicePercent[ "percent" ] ) $salary_kpi_percent += $serviceDetail[ "price" ] / 100 * $servicePercent[ "percent" ];

        } // foreach. $visit[ "services_id" ]

    } // foreach. $visits

} // if. $userDetail[ "is_percent" ] === "Y"


$API->returnResponse(

    [
        [
            "size" => 1,
            "value" => number_format( $salary_fixed, 0, ".", " " ),
            "description" => "Оклад",
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
            "size" => 1,
            "value" => number_format( $salary_kpi_value + $salary_kpi_percent, 0, ".", " " ),
            "description" => "% от продаж",
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
            "size" => 2,
            "value" => number_format( $salary_fixed + $salary_kpi_value + $salary_kpi_percent, 0, ".", " " ),
            "description" => "К выплате",
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