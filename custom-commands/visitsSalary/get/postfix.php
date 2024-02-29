<?php

//ini_set( "display_errors", true );

$sqlFilter = [];
$visits_ids = [];
$relations = [];

foreach ( $response[ "data" ] as $visit ) $visits_ids[] = $visit[ "id" ];
if ( empty( $visits_ids ) ) $API->returnResponse( [] );

if ( $requestData->category ) $sqlFilter[ "salesProductsList.product_id" ] = visits\getServicesIds( $requestData->category );
if ( $requestData->service )  $sqlFilter[ "salesProductsList.product_id" ] = $requestData->service;
$sqlFilter[ "saleVisits.visit_id" ] = $visits_ids;
$sqlFilter[ "salesList.action" ] = "sell";
$sqlFilter[ "salesList.status" ] = "done";

$allServices = $API->DB->from( "salesProductsList" )
    ->innerJoin( "salesList on salesList.id = salesProductsList.sale_id" )
    ->select( [ "salesList.summary" ] )
    ->innerJoin( "saleVisits on saleVisits.sale_id = salesList.id" )
    ->select( [ "saleVisits.visit_id as visit_id" ] )
    ->where( $sqlFilter );
$sqlFilter = [];

//$API->returnResponse( count( $allServices ) );


if ( $requestData->service ) $sqlFilter[ "services.id" ] = $requestData->service;
if ( $requestData->category ) $sqlFilter[ "services.category_id" ] = $requestData->category;
$sqlFilter[ "visits_services.visit_id" ] = $visits_ids;


foreach ( $allServices as $visitsService ) {

    $relations[ $visitsService[ "visit_id" ] ][] = $visitsService;

}

$sales_percent = [];
$sales_fixed = [];

/**
 * Получение списка kpi по услугам
 */
$userServices = $API->DB->from( "services_user_percents" )
    ->where( "row_id", $requestData->context->user_id );

foreach ( $userServices as $service ) {

    if ( $service[ "percent" ] ) {

        $sales_percent[ $service[ "service_id" ] ] = intval( $service[ "percent" ] );
        continue;

    }

    if ( $service[ "fix_sum" ] ) {

        $sales_fixed[ $service[ "service_id" ] ] = intval( $service[ "fix_sum" ] );

    }

}


foreach ( $response[ "data" ] as $key => $visit ) {

    $visit[ "period" ] = date( 'Y-m-d H:i', strtotime( $visit[ "start_at" ] ) ) . " - " . date( "H:i", strtotime( $visit[ "end_at" ] ) );

    $services = $relations[ $visit[ "id" ] ];

    if ( !$services ) {
        $visit[ "summary" ] = $visit[ "price" ];
        $visit[ "percent" ] = 0;
        $response[ "data" ][ $key ] = $visit;
        continue;
    }

    $total = 0;
    $summary = $services[ 0 ][ "summary" ];
    $servicesList = [];

    foreach ( $services as $service ) {

        $serviceID = intval( $service[ "product_id" ] );

        $servicesList[] = [
            "title" => $service[ "title" ],
            "value" =>  $serviceID
        ];

        if ( isset( $sales_percent[ $serviceID ] ) ) {

            $servicePercent = $sales_percent[ $serviceID ];

            $price = intval( $service[ "cost" ] * $service[ "amount" ] );
            $total += $price / 100 * $servicePercent;
            continue;

        }

        if ( isset( $sales_fixed[ $serviceID ] ) ) {

            $servicePercent = $sales_fixed[ $serviceID ];
            $total += $servicePercent;

        }

    }

    $visit[ "services_id" ] = $servicesList;
    $visit[ "summary" ] = $summary;
    $visit[ "percent" ] = $total;

    $response[ "data" ][ $key ] = $visit;

}