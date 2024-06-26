<?php

/**
 * Период, за который высчитывается зарплата
 */

global $visits_ids;

$sqlFilter = [];

function getServicesIds( $category ): array {

    global $API;

    $sqlFilter = "SELECT id FROM services WHERE category_id = $category";
    $servicesList = mysqli_query( $API->DB_connection, $sqlFilter );
    $services_ids = [];

    foreach ( $servicesList as $service ) $services_ids[] = intval( $service[ "id" ] );
    return $services_ids;

}

function getVisitsIds( $table, $start_at, $end_at, $user_id ): array {

    global $API, $requestData;

    $sqlFilter = "
    SELECT $table.id as id
    FROM $table
    WHERE
        start_at >= '$start_at' AND
        end_at <= '$end_at' AND
        ( user_id = $user_id OR assist_id = $user_id ) AND
        is_active = 'Y' AND
        is_payed = 'Y' AND
        status = 'ended'";

    if ( property_exists( $requestData, "service" ) && $requestData->service && $table == "equipmentVisits" )
        $sqlFilter .= " AND service_id = $requestData->service";

    $visitsList = mysqli_query( $API->DB_connection, $sqlFilter );
    $visits_ids = [];


    foreach ( $visitsList as $visit ) $visits_ids[] = intval( $visit[ "id" ] );

    if ( property_exists( $requestData, "service" ) && $requestData->service && $table == "visits" )
        $visits_ids = visits\serviceFilter( $requestData->service, $visits_ids );

    return $visits_ids;

}

function getPaymentServices( $type, $sqlFilter, $start_at, $end_at, $user_id ): array {

    global $API, $visits_ids;

    $table = $type == "equipmentVisits" ? "salesEquipmentVisits" : "saleVisits";
    $sqlFilter[ "$table.visit_id" ] = getVisitsIds( $type, $start_at, $end_at, $user_id );

    $visits_ids += count( $sqlFilter[ "$table.visit_id" ] );
    if ( empty( $sqlFilter[ "$table.visit_id" ] ) ) $sqlFilter[ "$table.visit_id" ] = [ 0 ];

    $allServices = $API->DB->from( "salesProductsList" )
        ->innerJoin( "salesList on salesList.id = salesProductsList.sale_id" )
        ->innerJoin( "$table on $table.sale_id = salesList.id" )
        ->where( $sqlFilter );

    foreach ( $allServices as $service ) {
        $servicesList[] = visits\getFullService( $service[ "product_id" ], $user_id );
    }
    return  $servicesList ?? [];

}

$sqlFilter = [
    "salesList.action" => "sell",
    "salesList.status" => "done"
];

$start_at = date( "Y-m-d", strtotime( $requestData->start_at ) ) . " 00:00:00";
$end_at = date( "Y-m-d", strtotime( $requestData->end_at ) ) . " 23:59:59";

if ( $requestData->start_at ) $sqlFilter[ "salesList.created_at >= ?" ] = $start_at;
if ( $requestData->end_at ) $sqlFilter[ "salesList.created_at <= ?" ] = $end_at;

if ( $requestData->category ) $sqlFilter[ "salesProductsList.product_id" ] = getServicesIds( $requestData->category );
if ( $requestData->service )  $sqlFilter[ "salesProductsList.product_id" ] = $requestData->service;

$allServices = array_merge(
    getPaymentServices( "visits", $sqlFilter, $start_at, $end_at, $requestData->user_id ),
    getPaymentServices( "equipmentVisits", $sqlFilter, $start_at, $end_at, $requestData->user_id ),
);

//$API->returnResponse( $allServices );

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


$salaryType = $userDetail[ "salary_type" ];
$salary_fixed = $userDetail[ "salary" ];

$additionalWidgetTitle = "% от продаж";
$additionalWidgetValue = 0;

$services_count = count( $allServices );
$visits_count = $visits_ids;

//-------------------------------------------------------------------------------------




function rate_percent ( $user_id, $visitsServices ): float {

    global $API;

    /**
     * Общая сумма продаж
     */
    $total = 0;

    $sales_percent = [];
    $sales_fixed = [];

    /**
     * Получение списка kpi по услугам
     */
    $userServices = $API->DB->from( "services_user_percents" )
        ->where( "row_id", $user_id );

    foreach ( $userServices as $service ) {

        if ( $service[ "percent" ] ) {

            $sales_percent[ $service[ "service_id" ] ] = intval( $service[ "percent" ] );
            continue;

        }

        if ( $service[ "fix_sum" ] ) {

            $sales_fixed[ $service[ "service_id" ] ] = intval( $service[ "fix_sum" ] );

        }

    }
//    $API->returnResponse( $visitsServices );

    foreach ( $visitsServices as $visitsService ) {

        if ( isset( $sales_percent[ $visitsService[ "id" ] ] ) ) {

            $servicePercent = $sales_percent[ $visitsService[ "id" ] ];

//            $price = intval( $visitsService[ "cost" ] * $visitsService[ "amount" ] );
            $total += $visitsService[ "price" ] / 100 * $servicePercent;
            continue;

        }

        if ( isset( $sales_fixed[ $visitsService[ "id" ] ] ) ) {

            $servicePercent = $sales_fixed[ $visitsService[ "id" ] ];
            $total += $servicePercent;

        }

    } // foreach. $visits


    return $total;

}

/**
 * Процент от продаж
 */

if ( $salaryType == "rate_percent" ) {

    $additionalWidgetTitle = "% от продаж";
    $additionalWidgetValue = rate_percent( $requestData->user_id, $allServices );
    $salary_kpi_value = $additionalWidgetValue;

} // if. $userDetail[ "is_percent" ] === "Y"


//-------------------------------------------------------------------------------------


if ( $salaryType == "rate_kpi" ) {

    $additionalWidgetTitle = "Итого по KPI";
    $kpi = [];

    $publicApp = $API::$configs[ "paths" ][ "public_app" ];
    require_once( "$publicApp/custom-libs/kpi/visits.php" );
    require_once( "$publicApp/custom-libs/kpi/sales.php" );
    require_once( "$publicApp/custom-libs/kpi/services.php" );

    $highestKPI = [];

    foreach ( $kpi as $record ) {

        if ( $record[ "bonus" ] < $highestKPI[ $record[ 'kpi_type' ] ] ) continue;
        $highestKPI[ $record[ 'kpi_type' ] ] = $record[ "bonus" ];

    }

    foreach ( $highestKPI as $key => $record ) {
        $additionalWidgetValue += $record;
    }

    $salary_kpi_value = $additionalWidgetValue;

}

//$API->returnResponse( $additionalWidgetValue );
$additionalWidgetValue = number_format( $additionalWidgetValue, 0, ".", " " );


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
            "value" => $additionalWidgetValue,
            "description" => $additionalWidgetTitle,
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


