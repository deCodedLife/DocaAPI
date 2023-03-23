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


if ( $requestData->services ) {

    $modifier = new Сashbox\IModifier( 0 );

    $API->deleteFrom( "promotionObjects" )
        ->where( [
            "is_excluded" => $modifier->IsExcluded ? 'Y' : 'N',
            "is_required" => $modifier->IsRequired ? 'Y' : 'N',
            "is_group" => $modifier->IsGroup ? 'Y' : 'N',
            "promotion_id" => $requestData->id
        ] )
        ->execute();

    foreach ( $requestData->services as $service )
        writeModifier(
            $requestData->id,
            new Сashbox\IModifier( $service )
        );

}



if ( $requestData->servicesGroups ) {

    $modifier = new Сashbox\IModifier( 0, true );

    $API->deleteFrom( "promotionObjects" )
        ->where( [
            "is_excluded" => $modifier->IsExcluded ? 'Y' : 'N',
            "is_required" => $modifier->IsRequired ? 'Y' : 'N',
            "is_group" => $modifier->IsGroup ? 'Y' : 'N',
            "promotion_id" => $requestData->id
        ] )
        ->execute();

    foreach ( $requestData->servicesGroups as $serviceGroup )
        writeModifier(
            $requestData->id,
            new Сashbox\IModifier( $serviceGroup, true )
        );

}


if ( $requestData->requiredServices ) {

    $modifier = new Сashbox\IModifier( 0, false, true );

    $API->deleteFrom( "promotionObjects" )
        ->where( [
            "is_excluded" => $modifier->IsExcluded ? 'Y' : 'N',
            "is_required" => $modifier->IsRequired ? 'Y' : 'N',
            "is_group" => $modifier->IsGroup ? 'Y' : 'N',
            "promotion_id" => $requestData->id
        ] )
        ->execute();

    foreach ( $requestData->requiredServices as $service )
        writeModifier(
            $requestData->id,
            new Сashbox\IModifier( $service, false, true )
        );

}



if ( $requestData->requiredServicesGroups ) {

    $modifier = new Сashbox\IModifier( 0, true, true );

    $API->deleteFrom( "promotionObjects" )
        ->where( [
            "is_excluded" => $modifier->IsExcluded ? 'Y' : 'N',
            "is_required" => $modifier->IsRequired ? 'Y' : 'N',
            "is_group" => $modifier->IsGroup ? 'Y' : 'N',
            "promotion_id" => $requestData->id
        ] )
        ->execute();

    foreach ( $requestData->requiredServicesGroups as $serviceGroup )
        writeModifier(
            $requestData->id,
            new Сashbox\IModifier( $serviceGroup, true, true )
        );

}



if ( $requestData->excludedServices ) {

    $modifier = new Сashbox\IModifier( 0, false, false, true );

    $API->deleteFrom( "promotionObjects" )
        ->where( [
            "is_excluded" => $modifier->IsExcluded ? 'Y' : 'N',
            "is_required" => $modifier->IsRequired ? 'Y' : 'N',
            "is_group" => $modifier->IsGroup ? 'Y' : 'N',
            "promotion_id" => $requestData->id
        ] )
        ->execute();

    $API->returnResponse( "Test", 400 );

    foreach ( $requestData->excludedServices as $service )
        writeModifier(
            $requestData->id,
            new Сashbox\IModifier( $service, false, false, true )
        );

}
$API->returnResponse( "Test", 400 );



if ( $requestData->excludedServicesGroups ) {

    $modifier = new Сashbox\IModifier( 0, true, false, true );

    $API->deleteFrom( "promotionObjects" )
        ->where( [
            "is_excluded" => $modifier->IsExcluded ? 'Y' : 'N',
            "is_required" => $modifier->IsRequired ? 'Y' : 'N',
            "is_group" => $modifier->IsGroup ? 'Y' : 'N',
            "promotion_id" => $requestData->id
        ] )
        ->execute();

    foreach ( $requestData->excludedServicesGroups as $serviceGroup )
        writeModifier(
            $requestData->id,
            new Сashbox\IModifier( $serviceGroup, true, false, true )
        );

}



if ( $requestData->clientsGroups ) {

    $modifier = new Сashbox\IModifier( 0 );
    $modifier->Type = "clients";

    $API->deleteFrom( "promotionObjects" )
        ->where( [
            "is_excluded" => $modifier->IsExcluded ? 'Y' : 'N',
            "is_required" => $modifier->IsRequired ? 'Y' : 'N',
            "is_group" => $modifier->IsGroup ? 'Y' : 'N',
            "promotion_id" => $requestData->id
        ] )
        ->execute();

    foreach ( $requestData->clientsGroups as $clientGroup ) {

        $clientModifier = new Сashbox\IModifier( $clientGroup );
        $clientModifier->Type = "clients";

        writeModifier( $requestData->id, $clientModifier );

    }

}


