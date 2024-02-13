<?php

/**
 * @file
 * Формирование списка клиентов посещавшие специалистов
 */


if ( !$requestData->user_id ) $API->returnResponse( [] );


/**
 * Сформированный список
 */
$returnVisits = [];


foreach ( $response[ "data" ] as $visit ) {

    /**
     * Массив клиентов
     */
    $clients = [];

    /**
     * Массив услуг
     */
    $services = [];


    /**
     * Получение услуг из заявки
     */
    $visits_services = $API->DB->from( "visits_services" )
        ->where( "visit_id",  $visit[ "id" ]);

    foreach ( $visits_services as $visit_services ) {

        $service = $API->DB->from( "services" )
            ->where( "id",  $visit_services[ "service_id"] )
            ->limit( 1 )
            ->fetch();

        $services[] = [

            "title" => $service[ "title" ],
            "value" => (int)$service[ "id" ]

        ];

    }

    /**
     * Получение клиентов из заявки
     */
    $visits_clients = $API->DB->from( "visits_clients" )
        ->where( "visit_id",  $visit[ "id" ]);

    foreach ( $visits_clients as $visit_clients ) {

        $client = $API->DB->from( "clients" )
            ->where( "id",  $visit_clients[ "client_id"] )
            ->limit( 1 )
            ->fetch();

        $clients[] = [

            "fio" => $client[ "last_name" ] . " " . $client[ "first_name" ] . " " . $client[ "patronymic" ],
            "id" => (int)$visit_clients[ "client_id" ]

        ];

    }

    $returnVisits[] = [

        "id" => $visit[ "id" ],
        "user_id" => $visit[ "user_id" ],
        "client_id" => $clients[ 0 ][ "id" ],
        "fio" => $clients[ 0 ][ "fio" ],
        "services_id" => array_values($services),
        "price" => $visit[ "price" ],
        "period" => date( 'Y-m-d H:i', strtotime( $visit[ "start_at" ] ) ) . " - " . date( "H:i", strtotime( $visit[ "end_at" ] ) )

    ];

}

$response[ "data" ] = $returnVisits;
