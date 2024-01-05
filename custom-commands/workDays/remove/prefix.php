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

$API->DB->deleteFrom( "workDaysWeekdays" )
    ->where( "rule_id", $requestData->id )
    ->execute();

$begin = DateTime::createFromFormat( "Y-m-d H:i:s", "{$workdayInfo[ "event_from" ]}" );
$end = DateTime::createFromFormat( "Y-m-d H:i:s", "{$workdayInfo[ "event_to" ]}" );

$API->DB->deleteFrom( "scheduleEvents" )
    ->where( [
        "user_id" => $workdayInfo[ "user_id" ],
        "event_from > ?" => $begin->format( "Y-m-d 00:00:00" ),
        "event_to < ?" => $begin->format( "Y-m-d 23:59:59" ),
        "store_id" => $workdayInfo[ "store_id" ]
    ] )
    ->execute();