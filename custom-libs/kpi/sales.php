<?php
//
//global $API, $requestData;
//require_once( __DIR__ . "/user_sales.php" );
//
//$start_at = date( "Y-m-d", strtotime( $requestData->start_at ) ) . " 00:00:00";
//$end_at = date( "Y-m-d", strtotime( $requestData->end_at ) ) . " 23:59:59";
//
//
///**
// *  Определение переменных
// */
//$kpi_type = "KPI продаж";
//$kpi_article = "sales";
//
//
///**
// * Получение настроек KPI
// */
//$kpi_sales = $API->DB->from( "kpi_sales" )
//    ->where( "row_id", $requestData->user_id );
//
//
///**
// * Выгрузка списка
// */
//foreach ( $kpi_sales as $kpi_sale ) {
//
//    $kpi[] = [
//        "type" => "$kpi_type {$kpi_sale[ "required_value" ]}%",
//        "services_summary" => $sales_summary,
//        "services_count" => $sales_count,
//        "percent" => ( $sales_summary * 100 / $kpi_sale[ "summary" ] ),
//        "promotion" => "-",
//        "bonus" => $sales_summary < $kpi_sale[ "summary" ] ? "0" : $kpi_sale[ "kpi_value" ],
//        "kpi_type" => $kpi_article
//    ];
//
//} // foreach ( $kpi_services as $kpi )