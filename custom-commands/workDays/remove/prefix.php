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

$visit = $API->DB->from( "visits" )
    ->where( [
        "user_id" => $workdayInfo[ "user_id" ],
        "start_at >= ?" => $workdayInfo[ "event_from" ],
        "end_at <= ?" => $workdayInfo[ "event_to" ]
    ] )
    ->limit( 1 )
    ->fetch();

if ( count( $visits ) != 0 ) $API->returnResponse( "У сотрудника есть посещения ${$visit[ "id" ]}", 500 );
