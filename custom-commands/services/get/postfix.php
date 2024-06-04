<?php

foreach ( $response[ "data" ] as $key => $service ) {

    if ( $service[ "num" ] == "99999999ZZZZZZZ" ) $response[ "data" ][ $key ][ "num" ] = "";

}

if ( $requestData->context->block == "form_list" || $requestData->context->block == "select" ) {

    $services_price = [];

    if ( $API->request->data->users_id ) {

        $workingTime = $API->DB->from( "workingTime" )
            ->where( "user", $API->request->data->users_id );

        foreach ( $workingTime as $row ) $services_price[ intval( $row[ "row_id" ] ) ] = $row;

    }

    foreach ( $response[ "data" ] as $key => $service ) {

        $price = $services_price[ intval( $service[ "id" ] ) ];
        if ( !$price ) $price = $service[ "price" ];


        $response[ "title" ] = "{$service[ "article" ]} {$service[ "title" ]} + {$price}";
        $response[ "data" ][ $key ] = $service;

    }

}

