<?php


if ( $requestData->context->block == "form_list" || $requestData->context->block == "select" ) {

    $services_price = [];

    if ( $API->request->data->users_id ) {

        $workingTime = $API->DB->from( "workingTime" )
            ->where( "user", $API->request->data->users_id );

        foreach ( $workingTime as $row ) $services_price[ intval( $row[ "row_id" ] ) ] = $row;

    }

    foreach ( $response[ "data" ] as $key => $service ) {

        $serviceCustomPrice = $services_price[ intval( $service[ "id" ] ) ];
        if ( !$serviceCustomPrice ) continue;

        $service[ "price" ] = $serviceCustomPrice[ "price" ];
        $response[ "data" ][ $key ] = $service;

    }

}

