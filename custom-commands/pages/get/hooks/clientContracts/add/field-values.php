<?php

if ( ( $requestData->context->form == "clientContracts" ) && $requestData->context->row_id )
    $formFieldValues[ "client_id" ] = [
        "value" => $requestData->context->row_id,
        "is_visible" => false
    ];
    $formFieldValues[ "user_id" ] = [
        "value" => $API::$userDetail->id,
    ];