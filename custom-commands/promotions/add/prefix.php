<?php

require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/discounts/index.php";



/**
 * Запись объектов акции в совмещённую таблицу
 *
 * @param int $promotion_id
 * @param Сashbox\IModifier $modifier
 * @return void
 */
function writeModifier( int $promotion_id, Сashbox\IModifier $modifier, $is_test = false ): void
{

    global $API;

    if ( $is_test )
        $API->returnResponse( json_encode( [
            "promotion_id" => $promotion_id,
            "type" => $modifier->Type,
            "object_id" => $modifier->ObjectID,
            "is_excluded" => $modifier->IsExcluded ? 'Y' : 'N',
            "is_required" => $modifier->IsRequired ? 'Y' : 'N',
            "is_group" => $modifier->IsGroup ? 'Y' : 'N',
        ] ), 400 );

    // {"promotion_id":39,"type":"target","object_id":24,"is_excluded":"N","is_required":"N","is_group":"N"}

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



foreach ( $requestData->services as $service )
    writeModifier(
        $promotion_id,
        new Сashbox\IModifier( $service )
    );

$API->returnResponse( json_encode( new Cashbox\IModifier() ), 400 );

foreach ( $requestData->servicesGroups as $serviceGroup )
    writeModifier(
        $promotion_id,
        new Cashbox\IModifier( $serviceGroup, true ), true
    );




foreach ( $requestData->requiredServices as $service )
    writeModifier(
        $promotion_id,
        new Cashbox\IModifier( $service, false, true )
    );

foreach ( $requestData->requiredServicesGroups as $serviceGroup )
    writeModifier(
        $promotion_id,
        new Cashbox\IModifier( $serviceGroup, true, true )
    );



foreach ( $requestData->excludedServices as $service )
    writeModifier(
        $promotion_id,
        new Cashbox\IModifier( $service, false, false, true )
    );

foreach ( $requestData->excludedServicesGroups as $serviceGroup )
    writeModifier(
        $promotion_id,
        new Cashbox\IModifier( $serviceGroup, true, false, true )
    );




foreach ( $requestData->clientsGroups as $clientGroup ) {

    $clientModifier = new Cashbox\IModifier( $clientGroup );
    $clientModifier->Type = OBJECTS_CATEGORIES[ 1 ];

    writeModifier( $promotion_id, $clientModifier );

}




$API->returnResponse();