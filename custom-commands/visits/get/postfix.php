<?php

foreach ( $response[ "data" ] as $key => $visit ) {

    $visit[ "period" ] = date( 'Y-m-d H:i', strtotime( $visit[ "start_at" ] ) ) . " - " . date( "H:i", strtotime( $visit[ "end_at" ] ) );
    $response[ "data" ][ $key ] = $visit;

}

