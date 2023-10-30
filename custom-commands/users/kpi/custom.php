<?php
// ini_set("display_errors", true);

/**
 * KPI
 */
$returnKpi = [];

/**
 * KPI по количеству проданных услуг
 */
$range = [];

/**
 * KPI по количеству проданных услуг - Тип отображения
 */
$range[ "type" ] = "range";

/**
 * KPI по количеству проданных услуг - Заголовок
 */
$range[ "title" ] = "KPI по количеству проданных услуг";

/**
 * KPI продаж
 */
$progressbar = [];

/**
 * KPI продаж - Тип отображения
 */
$progressbar[ "type" ] = "progressbar";

/**
 * KPI продаж - Заголовок
 */
$progressbar[ "title" ] = "KPI продаж";

/**
 * Сумма продаж
 */
$sum = 0;

/**
 * Список  KPI продаж
 */
$kpiSales = $API->DB->from( "kpi_sales" )
    ->where( "row_id", $requestData->id );

/**
 * KPI по количеству проданных услуг
 */
$kpiServices = $API->DB->from( "kpi_services" )
    ->where( "row_id", $requestData->id );

/**
 * Запрос на получение оплаченных продаж услуг сотрудника
 */
$salesList = $API->DB->from( "salesList" )
    ->leftJoin( "salesProductsList ON salesProductsList.sale_id = salesList.id" )
    ->leftJoin( "saleVisits ON saleVisits.sale_id = salesList.id" )
    ->leftJoin( "visits ON visits.id = saleVisits.visit_id" )
    ->select(null)->select([
        "salesList.id",
        "salesList.status",
        "salesList.created_at",
        "salesProductsList.product_id",
        "salesProductsList.cost",
        "salesProductsList.amount",
        "visits.user_id",
        "saleVisits.visit_id",
    ])
    ->where( [
        "salesProductsList.type" => "service",
        "salesList.status" => "done",
        "visits.user_id" => $requestData->id,
        "salesList.created_at >= ?" =>  date( 'Y-m-1' ) . " 00:00:00",
        "salesList.created_at <= ?" =>  date( 'Y-m-d' ) . " 23:59:59"
    ] )
    ->orderBy( "salesList.created_at desc" )
    ->limit ( 0 );

/**
 * Обход продаж
 */
foreach ( $salesList as $sale ) {

    $sum += $sale[ "cost" ] * $sale[ "amount" ];

}

/**
 * Обход KPI
 */
foreach ( $kpiSales as $kpiSale ) {

    $percent = ( $sum * 100 ) / $kpiSale[ "summary" ];

    $progressbar[ "values" ][] = [

        "title" => $sum . " руб.",
        "percent" => (int)$percent,
        "reward" => $kpiSale[ "kpi_value" ] . " руб.",

    ];

}

/**
 * Наполнение выдачи KPI продаж
 */
$returnKpi[] = $progressbar;

/**
 * Обход KPI проданных услуг
 */
foreach ( $kpiServices as $kpiService ) {

    /**
     * Колличество проданных услуг
     */
    $countSaleProducts = 0;

    /**
     * Список прожад сотрудника
     */
    $salesList = $API->DB->from( "salesList" )
        ->leftJoin( "salesProductsList ON salesProductsList.sale_id = salesList.id" )
        ->leftJoin( "saleVisits ON saleVisits.sale_id = salesList.id" )
        ->leftJoin( "visits ON visits.id = saleVisits.visit_id" )
        ->select(null)->select([
            "salesList.id",
            "salesList.status",
            "salesList.created_at",
            "salesProductsList.product_id",
            "salesProductsList.cost",
            "salesProductsList.amount",
            "visits.user_id",
            "saleVisits.visit_id",
        ])
        ->where( [
            "salesProductsList.product_id" => $kpiService[ "service" ],
            "salesProductsList.type" => "service",
            "salesList.status" => "done",
            "salesList.created_at >= ?" =>  date( 'Y-m-1' ) . " 00:00:00",
            "salesList.created_at <= ?" =>  date( 'Y-m-d' ) . " 23:59:59",
            "visits.user_id" => $requestData->id
        ] )
        ->orderBy( "salesList.created_at desc" )
        ->limit ( 0 );

    /**
     * Обход прожад сотрудника
     */
    foreach ( $salesList as $sale ) {

        $countSaleProducts += $sale[ "amount" ];

    }

    /**
     * Получение детальной информации об услуги
     */
    $serviceDetail = $API->DB->from( "services" )
        ->where( "id", $kpiService[ "service" ] )
        ->limit( 1 )
        ->fetch();

    /**
     * Наполнение списка продаж услуг сотрудника
     */
    $range[ "values" ][] = [

        "title" => $serviceDetail[ "title" ],
        "current" => $countSaleProducts,
        "reach" => (int) $kpiService[ "required_value" ],
        "reward" => $kpiService[ "kpi_value" ] . " руб."

    ];

}

/**
 * Наполнение списка продаж услуг сотрудника
 */
$returnKpi[] = $range;

/**
 * Ответ
 */
$API->returnResponse( $returnKpi );


