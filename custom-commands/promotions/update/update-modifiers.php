<?php



require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/discounts/index.php";
use Сashbox\IModifier as IModifier;



if ( is_array( $requestData->services )  )              IModifier::removeByPattern( $requestData->id, new IModifier( null, "services" ) );
if ( is_array( $requestData->servicesGroups ) )         IModifier::removeByPattern( $requestData->id, new IModifier( null, "services", true ) );
if ( is_array( $requestData->requiredServices ) )       IModifier::removeByPattern( $requestData->id, new IModifier( null, "services", false, true ) );
if ( is_array( $requestData->requiredServicesGroups ) ) IModifier::removeByPattern( $requestData->id, new IModifier( null, "services", true, true ) );
if ( is_array( $requestData->excludedServices ) )       IModifier::removeByPattern( $requestData->id, new IModifier( null, "services", false, false, true ) );
if ( is_array( $requestData->excludedServicesGroups ) ) IModifier::removeByPattern( $requestData->id, new IModifier( null, "services", true, false, true ) );
if ( is_array( $requestData->clientsGroups ) )          IModifier::removeByPattern( $requestData->id, new IModifier( null, "clients",  true ) );



foreach ( $requestData->services as $service )
    IModifier::writeModifier( $requestData->id, new IModifier( $service, "services" ) );

foreach ( $requestData->servicesGroups as $serviceGroup )
    IModifier::writeModifier( $requestData->id, new IModifier( $serviceGroup, "services", true ) );

foreach ( $requestData->requiredServices as $service )
    IModifier::writeModifier( $requestData->id, new IModifier( $service, "services", false, true ) );

foreach ( $requestData->requiredServicesGroups as $serviceGroup )
    IModifier::writeModifier( $requestData->id, new IModifier( $serviceGroup, "services", true, true ) );

foreach ( $requestData->excludedServices as $service )
    IModifier::writeModifier( $requestData->id, new IModifier( $service, "services", false, false, true ) );

foreach ( $requestData->excludedServicesGroups as $serviceGroup )
    IModifier::writeModifier( $requestData->id, new IModifier( $serviceGroup, "services", true, false, true ) );

foreach ( $requestData->clientsGroups as $clientGroup )
    IModifier::writeModifier( $requestData->id, new IModifier( $clientGroup, "clients", true ) );


