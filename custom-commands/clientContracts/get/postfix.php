<?php

foreach ( $response[ "data" ] as $key => $item ) {

    $clientContracts = $API->DB->from( "clientContracts" )
        ->where( "id", $item[ "id" ] )
        ->limit( 1 )
        ->fetch();

    $response[ "data" ][ $key ][ "href" ] = "https://" . $_SERVER[ "HTTP_HOST" ] . $clientContracts[ "linkFile" ];

}