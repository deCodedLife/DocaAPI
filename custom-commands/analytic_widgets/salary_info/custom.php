<?php

/**
 * Период, за который высчитывается зарплата
 */
//ini_set("display_errors", true);
$filter = [];
if ( $requestData->start_at ) $filter[ "start_at >= ?" ] = $requestData->start_at;
if ( $requestData->end_at ) $filter[ "end_at <= ?" ] = $requestData->end_at;
if ( $requestData->user_id ) $filter[ "user_id" ] = $requestData->user_id;
$filter[ "status" ] = "ended";


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
 * Колличество услуг
 */
$services_count = 0;

/**
 * Колличество посещений
 */

$visits_count = 0;

/**
 * Детальная информация о пользователе
 */

$userDetail = $API->DB->from( "users" )
    ->where( "id", $requestData->user_id )
    ->limit( 1 )
    ->fetch();

$salary_fixed = $userDetail[ "salary" ];


$visits = $API->DB->from( "visits" )
    ->where( $filter );


$visitsList = [];
foreach ( $visits as $visit ) $visitsList[] = $visit;


if ( $requestData->category ) {

    foreach ( $visitsList as $index => $returnVisit ) {

        $visits_services = $API->DB->from( "visits_services" )
            ->where( "visit_id", $returnVisit[ "id" ] );

        $service_exists = false;

        foreach ( $visits_services as $visit_service) {

            $service = $API->DB->from( "services" )
                ->where( "id", $visit_service[ "service_id" ] )
                ->limit( 1 )
                ->fetch();

            if ( $service[ "category_id" ] == $requestData->category )
                $service_exists = true;

        }

        if ( $service_exists == false ) unset( $visitsList[ $index ] );

    }

}

if ( $requestData->service && !empty( $requestData->service ) ) {

    foreach ( $visitsList as $index => $returnVisit ) {

        $visits_services = $API->DB->from( "visits_services" )
            ->where( "visit_id", $returnVisit[ "id" ] );

        $service_exists = false;

        foreach ( $visits_services as $visit_service) {

            if ( in_array( (int)$visit_service[ "service_id" ], $requestData->service  ) )
                $service_exists = true;

        }
        if ( $service_exists == false ) unset( $visitsList[ $index ] );

    }

}

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

    /**
     * Обработка услуг
     */

    if ( !empty($visitsList) ) {

        foreach ($visitsList as $visit) {

            /**
             * Подсчет колличества посещений
             */
            $visits_count++;

            $visitServices = $API->DB->from("visits_services")
                ->where("visit_id", $visit["id"]);

            foreach ($visitServices as $service) {

                /**
                 * Подсчет колличества услуг
                 */
                $services_count++;

                /**
                 * Детальная информация об услуге
                 */

                $serviceDetail = $API->DB->from("services")
                    ->where("id", $service["service_id"])
                    ->limit(1)
                    ->fetch();

                /**
                 * Процент услуги
                 */
                $servicePercent = $API->DB->from("services_user_percents")
                    ->where([
                        "row_id" => $requestData->user_id,
                        "service_id" => $service["service_id"]
                    ])
                    ->limit(1)
                    ->fetch();

                if (!$servicePercent) continue;


                if ($servicePercent["fix_sum"]) $salary_kpi_value += $servicePercent["fix_sum"];
                if ($servicePercent["percent"]) $salary_kpi_percent += $serviceDetail["price"] / 100 * $servicePercent["percent"];

            } // foreach. $visit[ "services_id" ]

        } // foreach. $visits
    }

} // if. $userDetail[ "is_percent" ] === "Y"

if ( !empty( $visitsList ) ) {


    foreach ($visitsList as $visit) {

        /**
         * Подсчет колличества посещений
         */
        $visits_count++;

        $visitServices = $API->DB->from("visits_services")
            ->where("visit_id", $visit["id"]);

        foreach ($visitServices as $service) {

            /**
             * Подсчет колличества услуг
             */
            $services_count++;

        } // foreach. $visit[ "services_id" ]

    } // foreach. $visits

}

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
        ],
        [
            "size" => 2,
            "value" => $visits_count,
            "description" => "Количество посещений",
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
        "size" => 2,
        "value" => $services_count,
        "description" => "Количество услуг",
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
        ]
    ]

);


