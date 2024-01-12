<?php

require_once $API::$configs[ "paths" ][ "public_app" ] . "/custom-libs/workdays/createEvents.php";

$workdayInfo = $API->DB->from( "workDays" )
    ->where( "id", $requestData->id )
    ->fetch();

$visits = $API->DB->from( "visits" )
    ->where( [
        "user_id" => $workdayInfo[ "user_id" ],
        "start_at >= ?" => $workdayInfo[ "event_from" ],
        "end_at <= ?" => $workdayInfo[ "event_to" ],
        "is_active" => 'Y'
    ] );

$visit = $API->DB->from( "visits" )
    ->where( [
        "user_id" => $workdayInfo[ "user_id" ],
        "start_at >= ?" => $workdayInfo[ "event_from" ],
        "end_at <= ?" => $workdayInfo[ "event_to" ],
        "is_active" => 'Y'
    ] )
    ->limit( 1 )
    ->fetch();

if ( count( $visits ) != 0 ) $API->returnResponse( "У сотрудника есть посещения ${$visit[ "id" ]}", 500 );

$API->DB->deleteFrom( "workDaysWeekdays" )
    ->where( "rule_id", $requestData->id )
    ->execute();

$begin = DateTime::createFromFormat( "Y-m-d H:i:s", "{$workdayInfo[ "event_from" ]}" );
$end = DateTime::createFromFormat( "Y-m-d H:i:s", "{$workdayInfo[ "event_to" ]}" );

removeEvents(
    $begin->format( "Y-m-d H:i:s" ),
    $end->format( "Y-m-d H:i:s" ),
    $workdayInfo[ "store_id" ],
    $workdayInfo[ "user_id" ],
    $workdayInfo[ "is_rule" ],
    $workdayInfo[ "is_weekend" ]
);