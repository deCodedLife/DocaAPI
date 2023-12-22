<?php

$formFieldValues[ "cabinet_id" ] = [ "is_visible" => true ];

$ruleDetails = $API->DB->from( "workDays" )
    ->where( "id", $pageDetail[ "row_id" ] )
    ->fetch();

if ( $ruleDetails[ "is_weekend" ] === 'Y' ) {

    $formFieldValues[ "event_from" ][ "is_visible" ] = false;
    $formFieldValues[ "event_to" ][ "is_visible" ] = false;

}