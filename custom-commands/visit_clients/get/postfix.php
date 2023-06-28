<?php

/**
 * Формирование списка клиентов посещавшие специалистов
 */

if ( $requestData->context->block === "list" ) {

    /**
     * Сформированный список
     */
    $returnVisits = [];

    foreach (  $response[ "data" ] as $visit ) {

        /**
         * Получение услуг из заявки
         */
        $visits_services = $API->DB->from( "visits_services" )
            ->where( "visit_id",  $visit[ "id" ]);

        /**
         * Масив услуг
         */
        $services = [];

        foreach ( $visits_services as $visit_services ) {

            $service = $API->DB->from( "services" )
                ->where( "id",  $visit_services[ "service_id"] )
                ->limit( 1 )
                ->fetch();

            $services[] = [

                "title" => $service[ "title" ],
                "value" => (int)$visit_services[ "service_id" ]

            ];

        }


        /**
         * Получение клиентов из заявки
         */
        $visits_clients = $API->DB->from( "visits_clients" )
            ->where( "visit_id",  $visit[ "id" ]);

        /**
         * Масив клиентов
         */
        $clients = [];

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

            "client_id" => $clients[ 0 ][ "id" ],
            "fio" => $clients[ 0 ][ "fio" ],
            "end_at" => $visit[ "end_at" ],
            "price" => $visit[ "price" ],
            "services_id" => $services,
            "start_at" => $visit[ "start_at" ]

        ];

    }

    $response[ "data" ] = $returnVisits;

} // if. $requestData->context->block === "list"
