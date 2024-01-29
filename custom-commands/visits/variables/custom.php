<?php

$productsList = [];
$summary = 0;

function getService( $id, $user_id = null ) {

    global $API;

    $innerPropertyRows = $API->sendRequest( "services", "get", [
        "id" => $id
    ] );

    $service = (array) $innerPropertyRows[ 0 ];
    $service[ "with_discount" ] = $service[ "price" ];


    if ( $service[ "article" ] == "null" ) $service[ "article" ] = "-";
    if ( !$user_id ) return $service;

    $personalDetails = $API->DB->from( "workingTime" )
        ->where( [
            "row_id" => $service[ "id" ],
            "user" => $user_id
        ] )
        ->fetch();

    if ( $personalDetails )  $service[ "price" ] = $personalDetails[ "price" ];

    $service[ "with_discount" ] = $service[ "price" ];

    return $service;

}

foreach ( $requestData->context->object->services_id as $service ) {

    $service = getService(
        $service->value,
        $requestData->context->object->user_id->value
    );
    $productsList[] = $service;
    $summary += $service[ "price" ];

}

$saleDetails = $API->DB->from( "salesList" )
    ->innerJoin( "saleVisits on saleVisits.sale_id = salesList.id" )
    ->where( [
        "saleVisits.visit_id" => $requestData->id,
        "salesList.action" => "sell"
    ] )
    ->fetch();

if ( !$saleDetails ) $API->returnResponse( [
    "services_id" => $productsList,
    "price" => $summary
] );


$saleProducts = $API->DB->from( "salesProductsList" )
    ->where( "sale_id", $saleDetails[ "id" ] );

$productsList = [];

foreach ( $saleProducts as $saleProduct ) {

    $service = getService( $saleProduct[ "product_id" ] );

    $service[ "price" ] = $saleProduct[ "cost" ];
    $service[ "discount" ] = round( $saleProduct[ "discount" ], 2 );
    $service[ "with_discount" ] = round( $saleProduct[ "cost" ] - $saleProduct[ "discount" ], 2 );

    $productsList[] = $service;

}

$API->returnResponse( [
    "services_id" => $productsList,
    "price" => $saleDetails[ "sum_deposit" ] + $saleDetails[ "sum_card" ] + $saleDetails[ "sum_entity" ] + $saleDetails[ "sum_cash" ]
] );