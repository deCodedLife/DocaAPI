<?php

foreach ( $response[ "data" ] as $row ) {

    if ( $row[ "replenished" ] < 0 ) $row[ "action" ] = "Списание";
    if ( $row[ "replenished" ] > 0 ) $row[ "action" ] = "Пополнение";

    $returnRows[] = $row;

} // foreach. $response[ "data" ]
$response[ "data" ] = $returnRows;
