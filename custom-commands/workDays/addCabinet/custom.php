<?php

//$API->returnResponse( json_encode( $requestData ), 500 );

$API->DB->update( "scheduleEvents" )
    ->set( "cabinet_id", $requestData->cabinet_id )
    ->where( [
        "rule_id" => $requestData->id,
        "event_from >= ?" => $requestData->event_from . " 00:00:00",
        "event_to <= ?" => $requestData->event_from . " 23:59:59"
    ] )
    ->execute();
//$API->returnResponse( "Заглушка", 500 );

//{"id":449226,"event_from":"2024-02-05","store_id":62,"cabinet_id":1139}