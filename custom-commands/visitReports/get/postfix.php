<?php

foreach ( $response[ "data" ] as $key => $item ) {

    $visitReports = $API->DB->from( "visitReports" )
        ->where( "id", $item[ "id" ] )
        ->limit( 1 )
        ->fetch();

    $response[ "data" ][ $key ][ "href" ] = "https://" . $_SERVER[ "HTTP_HOST" ] . $visitReports[ "linkFile" ];

}