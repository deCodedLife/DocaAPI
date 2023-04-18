<?php

$updates = (array) $requestData;
unset( $updates[ "id" ] );
unset( $updates[ "services" ] );
unset( $updates[ "servicesGroups" ] );
unset( $updates[ "requiredServices" ] );
unset( $updates[ "requiredServicesGroups" ] );
unset( $updates[ "clientsGroups" ] );
unset( $updates[ "excludedServices" ] );
unset( $updates[ "excludedServicesGroups" ] );

if ( empty( $updates ) == false ) {

    $API->DB->update( "promotions" )
        ->set( $updates )
        ->where( "id", $requestData->id )
        ->execute();

} // if empty( $updates ) == false


require $API::$configs[ "paths" ][ "public_app" ] . "/custom-commands/promotions/update/update-modifiers.php";


$API->returnResponse();
