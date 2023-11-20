<?php

/**
 * @file
 * Закрытие модального окна
 */

$requestData->phone = str_replace( " ", "", $requestData->phone );
$requestData->phone = str_replace( "+", "", $requestData->phone );
$requestData->phone = str_replace( "(", "", $requestData->phone );
$requestData->phone = str_replace( ")", "", $requestData->phone );
$requestData->phone = str_replace( "-", "", $requestData->phone );


$API->DB->deleteFrom( "callHistory" )
    ->where( [
        "api_id" => 0,
        "user_id" => $requestData->user_id,
        "client_phone" => $requestData->phone
    ] )
    ->execute();