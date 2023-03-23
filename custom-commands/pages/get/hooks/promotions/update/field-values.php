<?php

const OBJECTS_CATEGORIES = [
    "target",
    "clients"
];
$formFieldValues = [];

$details = $API->DB->from( "promotions" )
    ->where( "id", $pageDetail[ "row_id" ] )
    ->fetch();

$objects = $API->DB->from( "promotionObjects" )
    ->where( "promotion_id", $details[ "id" ] );

function sortObjects( $promotion, $type, $object ): array {

    if ( $object[ "is_group" ] == 'Y' ) $type .= "Groups";

    if ( $object[ "is_excluded" ] == 'Y' ) {
        $promotion[ "excluded" . $type ][] = (int) $object[ "object_id" ];
        return $promotion;
    }

    if ( $object[ "is_required" ] == 'Y' ) {
        $promotion[ "required" . $type ][] = (int) $object[ "object_id" ];
        return $promotion;
    }

    $promotion[ lcfirst( $type ) ][] = (int) $object[ "object_id" ];
    return $promotion;

}

foreach ( $objects as $object ) {

    if ( $object[ "type" ] == OBJECTS_CATEGORIES[ 0 ] )
        $formFieldValues = sortObjects( $formFieldValues, "Services", $object );

    if ( $object[ "type" ] == OBJECTS_CATEGORIES[ 1 ] )
        $formFieldValues = sortObjects( $formFieldValues, "ClientsGroups", $object );

}

$formFieldValues[ "services" ]  = $formFieldValues[ "services" ] ?? [ "title" => "", "value" => "" ];
$formFieldValues[ "servicesGroups" ]  = $formFieldValues[ "servicesGroups" ] ?? [ "title" => "", "value" => "" ];
$formFieldValues[ "requiredServices" ]  = $formFieldValues[ "requiredServices" ] ?? [ "title" => "", "value" => "" ];
$formFieldValues[ "requiredServicesGroups" ]  = $formFieldValues[ "requiredServicesGroups" ] ?? [ "title" => "", "value" => "" ];
$formFieldValues[ "clientsGroups" ]  = $formFieldValues[ "clientsGroups" ] ?? [ "title" => "", "value" => "" ];
$formFieldValues[ "excludedServices" ]  = $formFieldValues[ "excludedServices" ] ?? [ "title" => "", "value" => "" ];
$formFieldValues[ "excludedServicesGroups" ]  = $formFieldValues[ "excludedServicesGroups" ] ?? [ "title" => "", "value" => "" ];