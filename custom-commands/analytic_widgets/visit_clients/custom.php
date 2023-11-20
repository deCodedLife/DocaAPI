<?php

/**
 * @file
 * Отчет "Клиенты посещавшие специалистов
 */

/**
 * Детальная информация об отчете
 */
$reportStatistic = [

    /**
     * Сумма продаж
     */
    "visits_sum" => 0,

    /**
     * Сумма продаж
     */
    "services_user_percents" => 0,

    /**
     * Сумма продаж
     */
    "clients_count" => 0,

];

/**
 * Получение списка посещений
 */
$visitsClients = $API->sendRequest( "visit_clients", "get", $requestData );

/**
 * Обрабботка списка
 */
foreach ( $visitsClients as $visitsClient ) {

    $reportStatistic[ "visits_sum" ] += $visitsClient->price;
    $reportStatistic[ "clients_count" ]++;

    /**
     * Обход услуг в посещении
     */
    foreach ( $visitsClient->services_id as $visitService ) {

        /**
         * Получение услуг сотрудника с процентом от продаж
         */
        $servicesUserPercents = $API->DB->from( "services_user_percents" )
            ->where( [
                "service_id" => $visitService->value,
                "row_id" => $visitsClient->user_id,
                "created_at <=?" => $visitsClient->start_at
            ] )
            ->limit( 1 )
            ->fetch();

        /**
         * Обход услуг сотрудника с процентом от продаж
         */
        if ( $servicesUserPercents &&  $servicesUserPercents[ "percent" ] != 0 ) {

            /**
             * Получение продажи посещения
             */
            $saleVisits = $API->DB->from( "saleVisits" )
                ->where( [
                    "visit_id" => $visitsClient->id,
                ] );

            foreach ( $saleVisits as $saleVisit ) {

                /**
                 * Получение услуг в продаже
                 */
                $salesProductsList = $API->DB->from( "salesProductsList" )
                    ->where( [
                        "sale_id" => $saleVisit[ "sale_id" ],
                        "product_id" => $visitService->value
                    ] )
                    ->limit( 1 )
                    ->fetch();


                /**
                 * Подсчет суммы продаж с %
                 */
                $reportStatistic[ "services_user_percents" ] += $salesProductsList[ "cost" ];

            } // foreach .$saleVisits

        } else if ( $servicesUserPercents && $servicesUserPercents[ "fix_sum" ] != 0 ) {

            /**
             * Подсчет суммы продаж если фикс
             */
            $reportStatistic["services_user_percents"] += $servicesUserPercents[ "fix_sum" ];

        } // if ( $servicesUserPercents &&  $servicesUserPercents[ "percent" ] != 0 )

    }

} // foreach .$userServices

$API->returnResponse(

    [
        [
            "value" => number_format( intval( $reportStatistic["visits_sum"] ), 0, '.', ' ' ),
            "description" => "Сумма продаж",
            "size" => "1",
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
            "value" => number_format( intval( $reportStatistic["services_user_percents"] ), 0, '.', ' ' ),
            "description" => "Сумма продаж с  %",
            "size" => "1",
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
            "value" => $reportStatistic[ "clients_count" ],
            "description" => "Количество клиентов",
            "icon" => "",
            "size" => "1",
            "prefix" => "",
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
