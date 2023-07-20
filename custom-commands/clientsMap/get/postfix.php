<?php
/**
 * Сформированный список
 */
$returnVisits = [];

foreach ( $response[ "data" ] as $client ) {

    if ( $client[ "geolocation" ] ) {

        $client[ "title" ] = $client[ "first_name" ] . " " . $client[ "last_name" ] . " " .  $client[ "patronymic" ];
        $client[ "url" ] = "/clients/update/" . $client[ "id" ];

        $returnVisits[] = $client;

    }

}

$response[ "data" ] = $returnVisits;
