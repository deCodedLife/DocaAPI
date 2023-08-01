<?php

$workdayInfo = $API->DB->from( "workDays" )
    ->where( "id", $requestData->id )
    ->fetch();

$visits = $API->DB->from( "visits" )
    ->innerJoin( "visits_users on visits_users.visit_id = visits.id" )
    ->where( [
        "visits_users.user_id" => $workdayInfo[ "user_id" ],
        "visits.start_at >= ?" => $workdayInfo[ "event_from" ],
        "visits.end_at <= ?" => $workdayInfo[ "event_to" ]
    ] );

if ( count( $visits ) != 0 ) $API->returnResponse( "У сотрудника есть посещения", 500 );