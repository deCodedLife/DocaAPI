<?php

require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/discounts/index.php";
use Сashbox\IModifier as IModifier;



$promotion_id = $API->DB->insertInto( "promotions" )
    ->values( [
        "title" => $requestData->title,
        "promotion_type" => $requestData->promotion_type,
        "value" => $requestData->value ?? 0,
        "min_order" => $requestData->min_order,
        "begin_at" => $requestData->begin_at,
        "end_at" => $requestData->end_at,
        "comment" => $requestData->comment,
        "min_check" => $requestData->min_check,
        "valid_period" => $requestData->valid_period,
        "bonus_sum" => $requestData->bonus_sum,
        "type" => $requestData->type,
    ] )
    ->execute();



foreach ( $requestData->services as $service )
    IModifier::writeModifier( $promotion_id, new IModifier( $service, "services" ) );

foreach ( $requestData->servicesGroups as $serviceGroup )
    IModifier::writeModifier( $promotion_id, new IModifier( $serviceGroup, "services", true ) );

foreach ( $requestData->requiredServices as $service )
    IModifier::writeModifier( $promotion_id, new IModifier( $service, "services", false, true ) );

foreach ( $requestData->requiredServicesGroups as $serviceGroup )
    IModifier::writeModifier( $promotion_id, new IModifier( $serviceGroup, "services", true, true ) );

foreach ( $requestData->excludedServices as $service )
    IModifier::writeModifier( $promotion_id, new IModifier( $service, "services", false, false, true ) );

foreach ( $requestData->excludedServicesGroups as $serviceGroup )
    IModifier::writeModifier( $promotion_id, new IModifier( $serviceGroup, "services", true, false, true ) );

foreach ( $requestData->clientsGroups as $clientGroup )
    IModifier::writeModifier( $promotion_id, new IModifier( $clientGroup, "clients", true ) );


$API->returnResponse();
