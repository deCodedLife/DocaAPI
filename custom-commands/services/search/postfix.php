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


    /**
     * Формирование title записи
     */
    if ( isset( $servicesDetails[ intval( $row[ "value" ] ) ] ) ) {

        $row[ "title" ] = "{$row[ "title" ]} ({$servicesDetails[ intval( $row[ "value" ] ) ][ "price" ]}₽)";
        $response[ "data" ][ $key ] = $row;

    }


} // foreach. $response[ "data" ]
