<?php

require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/discounts/index.php";



/**
 * Запись объектов акции в совмещённую таблицу
 *
 * @param int $promotion_id
 * @param Сashbox\IModifier $modifier
 * @return void
 */
function writeModifier( int $promotion_id, Сashbox\IModifier $modifier ): void
{

    global $API;

    $API->DB->insertInto( "promotionObjects" )
        ->values( [
            "promotion_id" => $promotion_id,
            "type" => $modifier->Type,
            "object_id" => $modifier->ObjectID,
            "is_excluded" => $modifier->IsExcluded ? 'Y' : 'N',
            "is_required" => $modifier->IsRequired ? 'Y' : 'N',
            "is_group" => $modifier->IsGroup ? 'Y' : 'N',
        ] )
        ->execute();

}



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



foreach ( $requestData->services as $key => $service )
    writeModifier(
        $promotion_id,
        new Сashbox\IModifier( $service )
    );



foreach ( $requestData->servicesGroups as $serviceGroup )
    writeModifier(
        $promotion_id,
        new Сashbox\IModifier( $serviceGroup, true )
    );



foreach ( $requestData->requiredServices as $service )
    writeModifier(
        $promotion_id,
        new Сashbox\IModifier( $service, false, true )
    );



foreach ( $requestData->requiredServicesGroups as $serviceGroup )
    writeModifier(
        $promotion_id,
        new Сashbox\IModifier( $serviceGroup, true, true )
    );



foreach ( $requestData->excludedServices as $service )
    writeModifier(
        $promotion_id,
        new Сashbox\IModifier( $service, false, false, true )
    );

foreach ( $requestData->excludedServicesGroups as $serviceGroup )
    writeModifier(
        $promotion_id,
        new Сashbox\IModifier( $serviceGroup, true, false, true )
    );



foreach ( $requestData->clientsGroups as $clientGroup ) {

    $clientModifier = new Сashbox\IModifier( $clientGroup );
    $clientModifier->Type = "clients";

    writeModifier( $promotion_id, $clientModifier );

}


$API->returnResponse();
