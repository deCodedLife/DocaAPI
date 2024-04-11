<?php

$formFieldValues[ "cabinet_id" ] = [ "is_visible" => true ];
$pageScheme[ "structure" ][ 0 ][ "settings" ][ "data" ][ "id" ] = $requestData->context->rule_id;

$ruleDetails = $API->DB->from( "workDays" )
    ->where( "id", $requestData->context->rule_id )
    ->fetch();

if ( $ruleDetails[ "is_weekend" ] === 'Y' ) {

    $formFieldValues[ "event_from" ][ "is_visible" ] = false;
    $formFieldValues[ "event_to" ][ "is_visible" ] = false;
    $formFieldValues[ "is_weekend" ][ "value" ] = true;

} else $formFieldValues[ "is_weekend" ][ "value" ] = false;


if ( $ruleDetails[ "is_rule" ] === 'Y' ) {

    $workdaysDetails = $API->DB->from( "workDaysWeekdays" )
        ->where( "rule_id", $requestData->context->rule_id );

    foreach ( $workdaysDetails as $detail )
        $workdays[] = $detail[ "workday" ];

    $formFieldValues[ "work_days" ] = [
        "is_disabled" => false,
        "is_visible" => true,
        "value" => $workdays ?? []
    ];
}

if ( $ruleDetails[ "is_rule" ] === 'N' ) {

    $formFieldValues[ "work_days" ] = [
        "is_disabled" => true,
        "is_visible" => false,
        "value" => []
    ];

}

