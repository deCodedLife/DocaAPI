<?php

$currentStore = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch() ?? 1;

$formFieldValues[ "client_id" ] = $requestData->context->client_id;
$formFieldValues[ "employee_id" ] = $API::$userDetail->id;
$formFieldValues[ "pay_type" ] = "deposit";
$formFieldValues[ "visits_ids" ] = [];
$formFieldValues[ "store_id" ] = $currentStore;
$formFieldValues[ "pay_object" ] = [];
$formFieldValues[ "online_receipt" ] = true;
$formFieldValues[ "is_combined" ] = false;
$formFieldValues[ "pay_method" ] = "card";