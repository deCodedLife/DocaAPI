<?php

$productsList = [];
$summary = 0;


$service = $requestData->context->object->service_id;

$service = visits\getFullService(
    $service->value,
    $requestData->context->object->user_id->value
);

$productsList[] = $service;
$summary += $service[ "price" ];


$saleDetails = $API->DB->from( "salesList" )
    ->innerJoin( "salesEquipmentVisits on salesEquipmentVisits.sale_id = salesList.id" )
    ->where( [
        "salesEquipmentVisits.visit_id" => $requestData->context->object->id,
        "salesList.action" => "sell"
    ] )
    ->fetch();

if ( !$saleDetails ) $API->returnResponse( [
    "service_id" => $productsList,
    "price" => $summary
] );


$saleProducts = $API->DB->from( "salesProductsList" )
    ->where( "sale_id", $saleDetails[ "id" ] );

$productsList = [];

foreach ( $saleProducts as $saleProduct ) {

    $service = visits\getFullService( $saleProduct[ "product_id" ] );

    $service[ "price" ] = $saleProduct[ "cost" ];
    $service[ "discount" ] = round( $saleProduct[ "discount" ], 2 );
    $service[ "with_discount" ] = round( $saleProduct[ "cost" ] - $saleProduct[ "discount" ], 2 );

    $productsList[] = $service;

}

$API->returnResponse( [
    "services_id" => $productsList,
    "price" => $saleDetails[ "sum_deposit" ] + $saleDetails[ "sum_card" ] + $saleDetails[ "sum_entity" ] + $saleDetails[ "sum_cash" ]
] );