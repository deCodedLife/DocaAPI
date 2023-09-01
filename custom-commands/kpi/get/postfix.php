<?php

global $API, $requestData;
ini_set( "display_errors", true );

$userDetails = $API->DB->from( "users" )
    ->where( "id", $requestData->user_id )
    ->fetch();

$kpi = [];

$publicApp = $API::$configs[ "paths" ][ "public_app" ];
require_once( "$publicApp/custom-libs/kpi/sales.php" );
require_once( "$publicApp/custom-libs/kpi/services.php" );
require_once( "$publicApp/custom-libs/kpi/promotions.php" );

$response[ "data" ] = $kpi;