<?php

/**
 * Подстановка ФИО
 */

foreach ( $response[ "data" ] as $key => $row ) {

    $row[ "fio" ] = $row[ "last_name" ] . " " . $row[ "first_name" ] . " " . $row[ "patronymic" ];
    $response[ "data" ][ $key ] = $row;

} // foreach. $response[ "data" ]


if ( $API->isPublicAccount() ) {

    $siteClients = [];

    foreach ( $response[ "data" ] as $client ) {

        $phoneFormat = "+" . sprintf("%s (%s) %s-%s-%s",
                substr( $client[ "phone" ], 0, 1 ),
                substr( $client[ "phone" ], 1, 3 ),
                substr( $client[ "phone" ], 4, 3 ),
                substr( $client[ "phone" ], 7, 2 ),
                substr( $client[ "phone" ], 9 )
            );

        $siteClients[] = [

            "id" => $client[ "id" ],
            "fio" => $client[ "fio" ],
            "phone" => $phoneFormat,

        ];

    }

    $response[ "data" ] = $siteClients;

}