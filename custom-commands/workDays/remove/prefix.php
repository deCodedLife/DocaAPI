<?php

$workdayInfo = $API->DB->from( "workDays" )
    ->where( "id", $requestData->id )
    ->fetch();

$visits = $API->DB->from( "visits" )
    ->where( [
        "user_id" => $workdayInfo[ "user_id" ],
        "start_at >= ?" => $workdayInfo[ "event_from" ],
        "end_at <= ?" => $workdayInfo[ "event_to" ]
    ] );

if ( count( $visits ) != 0 ) $API->returnResponse( "У сотрудника есть посещения", 500 );