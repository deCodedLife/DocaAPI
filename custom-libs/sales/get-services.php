<?php

if ( empty( $visits ) ) $API->returnResponse( "get-visits should be first" );

$visits_ids = array_keys( $visits[ "visits" ] );
$visits_ids = empty( $visits_ids ) ? [ 0 ] : $visits_ids;

$equipment_ids = array_keys( $visits[ "equipmentVisits" ] );
$equipment_ids = empty( $equipment_ids ) ? [ 0 ] : $equipment_ids;

$services = array_merge(
    $API->DB->from( "services" )
        ->innerJoin( "visits_services on visits_services.service_id = services.id" )
        ->where( "visits_services.visit_id", $visits_ids )
        ->fetchAll(),
    $API->DB->from( "services" )
        ->leftJoin( "equipmentVisits on equipmentVisits.service_id = services.id" )
        ->where( "equipmentVisits.id", $equipment_ids )
        ->fetchAll()
);

//$API->returnResponse( $services );

foreach ( $services as $key => $service ) {

    $user_id = $API->DB->from( "visits" )
        ->where( "id", $requestData->id )
        ->fetch()[ "user_id" ];

    $serviceDetails = $API->DB->from( "workingTime" )
        ->where( [
            "row_id" => $service[ "id" ],
            "user" => $user_id
        ] )
        ->fetch();

    if ( $serviceDetails ) {

        $service[ "price" ] = $serviceDetails[ "price" ];
        $services[ $key ] = $service;

    }

}

$products = $API->DB->from( "products" )
    ->where( "id", $requestData->doca_products ?? [0] )
    ->fetchAll();

foreach ( $products as $product ) $productsPrice += $product[ "price" ] * $product[ "amount" ];