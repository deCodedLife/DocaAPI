<?php

foreach ( $response[ "data" ] as $row ) $services_ids[] = intval( $row[ "value" ] );

$servicesRows = $API->DB->from( "services" )
    ->where( "id", $services_ids ?? [] );

$customPriceRows = $API->DB->from( "workingTime" )
    ->where( [
        "user" => $API->request->data->users_id
    ] );

foreach ( $servicesRows as $row ) $servicesDetails[ intval( $row[ "id" ] ) ] = $row;
foreach ( $customPriceRows as $row ) $servicesDetails[ intval( $row[ "row_id" ] ) ][ "price" ] = $row[ "price" ];
foreach ( $response[ "data" ] as $key => $row ) {

    if ( $API->request->data->users_id ) {

        $hasUser = $API->DB->from( "services_users" )
            ->where( [
                "service_id" => $row[ "id" ],
                "user_id" => $API->request->data->users_id
            ] )
            ->fetch();

        if ( !$hasUser ) {

            unset( $response[ "data" ][ $key ] );
            continue;

        }

    }

    /**
     * Формирование title записи
     */
    if ( isset( $servicesDetails[ intval( $row[ "id" ] ?? $row[ "value" ] ) ] ) ) {

        $row[ "price" ] = $servicesDetails[ intval( $row[ "id" ] ?? $row[ "value" ] ) ][ "price" ];
        $row[ "menu_title " ] = "{$row[ "title" ]} ({$servicesDetails[ intval( $row[ "id" ] ?? $row[ "value" ] ) ][ "price" ]}₽)";
        $row[ "title " ] = "{$row[ "title" ]} ({$servicesDetails[ intval( $row[ "id" ] ?? $row[ "value" ] ) ][ "price" ]}₽)";
        $response[ "data" ][ $key ] = $row;

    }


} // foreach. $response[ "data" ]
