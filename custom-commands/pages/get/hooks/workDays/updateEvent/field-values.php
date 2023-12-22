<?php

$formFieldValues[ "cabinet_id" ] = [ "is_visible" => true ];

$ruleDetails = $API->DB->from( "workDays" )
    ->where( "id", $pageDetail[ "row_id" ] )
    ->fetch();

if ( $ruleDetails[ "is_weekend" ] === 'Y' ) {

    $formFieldValues[ "event_from" ][ "is_visible" ] = false;
    $formFieldValues[ "event_to" ][ "is_visible" ] = false;

}

if ( $ruleDetails[ "is_rule" ] === 'Y' ) $formFieldValues[ "work_days" ][ "is_visible" ] = true;
if ( $ruleDetails[ "is_rule" ] === 'N' ) $formFieldValues[ "work_days" ][ "is_visible" ] = false;

$workdaysDetails = $API->DB->from( "workDaysWeekdays" )
    ->where( "rule_id", $pageDetail[ "row_id" ] );

$workdays = [];
foreach ( $workdaysDetails as $detail ) $workdays[] = $detail[ "workday" ];
$formFieldValues[ "work_days" ][ "value" ] = $workdays;