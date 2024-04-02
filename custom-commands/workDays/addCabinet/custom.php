<?php

//$API->returnResponse( json_encode( $requestData ), 500 );

//ini_set( "display_errors", true );

require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/workdays/createEvents.php";
require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/workdays/validate.php";

$API->DB->update( "scheduleEvents" )
    ->set( "cabinet_id", $requestData->cabinet_id )
    ->where( [
        "rule_id" => $requestData->id,
        "event_from >= ?" => date( "Y-m-d", strtotime( $requestData->event_from ) ) . " 00:00:00",
        "event_to <= ?" => date( "Y-m-d", strtotime( $requestData->event_from ) ) . " 23:59:59"
    ] )
    ->execute();

$API->DB->update( "workDays" )
    ->set( "cabinet_id", $requestData->cabinet_id )
    ->where( "id", $requestData->id )
    ->execute();

$API->addEvent( "schedule" );
$API->addEvent( "day_planning" );

//$API->returnResponse( "Заглушка", 500 );

//{"id":449226,"event_from":"2024-02-05","store_id":62,"cabinet_id":1139}