<?php
/**
 * @ file
 * Отчет статистики клиентов
 */

/**
 * График новых клиетнов
 */
$clientsRegGraph = [];

/**
 * График посещений клиентов
 */
$clientVisitsGraph = [];

/**
 * График прихода
 */
$cashFlowGraph = [];

/**
 * График Среднего чека
 */
$averageСhequeGraph = [];


/**
 * Колличество посещений
 */
$visitsCount = 0;

/**
 * Приход
 */
$cashFlow = 0;

/**
 * Средний чек
 */
$averageСheque = 0;


/**
 * Фильтр клиентов
 */
$clientsFilter = [];

/**
 * Фильтр посещений
 */
$visitsFilter = [];

/**
 * Формирование фильтра клиентов
 */
$clientsFilter[ "is_active" ] = "Y";
if ( $requestData->start_at ) $clientsFilter[ "created_at >= ?" ] = $requestData->start_at . " 00:00:00";
if ( $requestData->end_at ) $clientsFilter[ "created_at <= ?" ] = $requestData->end_at . " 23:59:59";
if ( $requestData->start_ear ) $clientsFilter[ "birthday >= ?" ] = $requestData->start_ear;
if ( $requestData->end_ear ) $clientsFilter[ "birthday <= ?" ] = $requestData->end_ear;

/**
 * Получение клиентов
 */
$clients = $API->DB->from( "clients" )
    ->where( $clientsFilter );

foreach ( $clients as $client ) {

    /**
     * График новых клиентов
     */
    $regDate = date( "Y-m-d", strtotime( $client[ "created_at" ] ) );
    $clientsRegGraph[ $regDate ]++;

    /**
     * Формирование фильтра для посещений
     */
    if ( $requestData->store_id ) $visitsFilter[ "visits.store_id" ] = $requestData->store_id;
    $visitsFilter[ "visits_clients.client_id" ] = $client[ "id" ];
    $visitsFilter[ "visits.is_payed" ] = "Y";

    /**
     * Получение посещений Клиента
     */
    $clientsVisits = $API->DB->from( "visits" )
        ->leftJoin( "visits_clients ON visits_clients.visit_id = visits.id" )
        ->select( null )->select( [ "visits.id", "visits.start_at", "visits.store_id", "visits.price", "visits.status", "visits.is_payed" ] )
        ->where( $visitsFilter )
        ->orderBy( "visits.start_at desc" )
        ->limit( 0 );

    /**
     * Обход посещений клиента
     */
    foreach ( $clientsVisits as $clientsVisit ) {

        /**
         * График посещений
         */
        $visitDate = date( "Y-m-d", strtotime( $clientsVisit[ "start_at" ] ) );
        $clientVisitsGraph[ $visitDate ]++;
        $visitsCount++;

        /**
         * График прихода
         */
        $cashFlow += $clientsVisit[ "price" ];
        $cashFlowGraph[ $visitDate ] += $clientsVisit[ "price" ];

    } // foreach. $userVisits

    /**
     * Обход посещений клиента
     */
    foreach ( $clientsVisits as $clientsVisit ) {

        /**
         * График среднего чека
         */
        $visitDate = date( "Y-m-d", strtotime( $clientsVisit[ "start_at" ] ) );
        $averageСhequeGraph[ $visitDate ] += round( $cashFlowGraph[ $visitDate ] / $clientVisitsGraph[ $visitDate ] );

    } // foreach. $userVisits

} // foreach. $clients

if ( $visitsCount != 0 ) {

    $averageСheque = $cashFlow / $visitsCount;

} // if ( $visitsCount != 0 )


$API->returnResponse(

    [
        [
            "value" => count( $clients ),
            "description" => "Новые клиенты",
            "icon" => "",
            "prefix" => "",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "background" => "",
            "detail" => [
                "type" => "details_char",
                "settings" => [
                    "char" => [
                        "x" => array_keys($clientsRegGraph),
                        "lines" => [
                            [
                                "title" => "Новых клиентов",
                                "values" => $clientsRegGraph
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            "value" => $visitsCount,
            "description" => "Посещения",
            "icon" => "",
            "prefix" => "",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "background" => "",
            "detail" => [
                "type" => "details_char",
                "settings" => [
                    "char" => [
                        "x" => array_keys($clientVisitsGraph),
                        "lines" => [
                            [
                                "title" => "Посещений",
                                "values" => $clientVisitsGraph
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            "value" => $cashFlow,
            "description" => "Приход",
            "icon" => "",
            "prefix" => "₽",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "background" => "",
            "detail" => [
                "type" => "details_char",
                "settings" => [
                    "char" => [
                        "x" => array_keys($cashFlowGraph),
                        "lines" => [
                            [
                                "title" => "Приход",
                                "values" => $cashFlowGraph
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            "value" => round( $averageСheque, 2 ),
            "description" => "Средний чек",
            "icon" => "",
            "prefix" => "₽",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "background" => "",
            "detail" => [
                "type" => "details_char",
                "settings" => [
                    "char" => [
                        "x" => array_keys($averageСhequeGraph),
                        "lines" => [
                            [
                                "title" => "Средний чек",
                                "values" => $averageСhequeGraph
                            ]
                        ]
                    ]
                ]
            ]

        ]
    ]

);
