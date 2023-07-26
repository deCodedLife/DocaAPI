<?php

$currentStore = $API->DB->from( "users" )
    ->where( "id", $API::$userDetail->id )
    ->fetch() ?? 1;

//$API->returnResponse( json_encode($requestData), 500 );
/// {"context":{"form":"deposit","row_id":1},"page":"clients\/deposit"}
/// {"context":{"form":"bonus","row_id":1},"page":"clients\/deposit"}

$formFieldValues[ "client_id" ] = $requestData->context->row_id;
$formFieldValues[ "employee_id" ] = $API::$userDetail->id;
$formFieldValues[ "action" ] = "deposit";

$formFieldValues[ "store_id" ] = $currentStore;
$formFieldValues[ "products" ] = [];

$formFieldValues[ "online_receipt" ] = true;
$formFieldValues[ "pay_method" ] = "card";

$formFieldValues[ "sum_cash" ] = 0;
$formFieldValues[ "sum_card" ] = 0;

$formFieldValues[ "summary" ] = [ "is_disabled" => false, "value" => 0 ];