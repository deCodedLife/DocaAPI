<?php

/**
 * Формирование списка отмененных посещений
 */

/**
 * Формирование фильтра
 */
$filter = [];
$filter[ "is_active" ] = "N";
if ( $requestData->cancelledDate_start ) $filter[ "cancelledDate >= ?" ] = $requestData->cancelledDate_start . " 00:00:00";
if ( $requestData->cancelledDate_end ) $filter[ "cancelledDate <= ?" ] = $requestData->cancelledDate_end . " 23:59:59";
if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;
if ( $requestData->reason_id ) $filter[ "reason_id" ] = $requestData->reason_id;

/**
 * Список посещений
 */
$visits = $API->DB->from( "visits" )
    ->where( $filter );

/**
 * Сформированный список
 */
$returnVisits = [];

foreach ( $visits as $visit ) {

    /**
     * Получение сотрудников из заявки
     */
    $visits_users = $API->DB->from( "visits_users" )
        ->where( "visit_id",  $visit[ "id" ]);

    $users = [];

    foreach ( $visits_users as $visit_users ) {

        $user = $API->DB->from( "users" )
            ->where( "id",  $visit_users[ "user_id"] )
            ->limit( 1 )
            ->fetch();

        $users[] = [

            "title" => $user[ "last_name" ],
            "value" => (int)$visit_users[ "user_id" ]

        ];

    }

    /**
     * Получение услуг из заявки
     */
    $visits_services = $API->DB->from( "visits_services" )
        ->where( "visit_id",  $visit[ "id" ]);

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
     * Получение операторов
     */
    $operator = $API->DB->from( "users" )
        ->where( "id",  $visit[ "operator" ] )
        ->limit( 1 )
        ->fetch();

    $operatorValue = [

        "title" => $operator[ "last_name" ],
        "value" => (int)$visit[ "operator" ]

    ];


    /**
     * Получение клиентов из заявки
     */
    $visits_clients = $API->DB->from( "visits_clients" )
        ->where( "visit_id",  $visit[ "id" ]);
    
    $clients = [];

    foreach ( $visits_clients as $visit_clients ) {

        $client = $API->DB->from( "clients" )
            ->where( "id",  $visit_clients[ "client_id"] )
            ->limit( 1 )
            ->fetch();

        $clients[] = [

            "title" => $client[ "last_name" ],
            "value" => (int)$visit_clients[ "client_id" ]

        ];

    }

    /**
     * Получение операторов
     */
    $reason = $API->DB->from( "cancelReasons" )
        ->where( "id",  $visit[ "reason_id" ] )
        ->limit( 1 )
        ->fetch();

    $reasonValue = [

        "title" => $reason[ "title" ],
        "value" => (int)$visit[ "reason_id" ]

    ];

    $returnVisits[] = [
        "cancelledDate" => $visit[ "cancelledDate" ],
        "operator" => $operatorValue,
        "id" => (int) $visit[ "id" ],
        "start_at" => $visit[ "start_at" ],
        "users_id" => $users,
        "clients_id" => $clients,
        "services_id" => $services,
        "reason_id" => $reasonValue,
    ];

}

$response[ "data" ] = $returnVisits;

$response[ "detail" ] = [
    "pages_count" => ceil(count( $response[ "data" ] ) / $requestData->limit )  ,
    "rows_count" => $requestData->limit
];
