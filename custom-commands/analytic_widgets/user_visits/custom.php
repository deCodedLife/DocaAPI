<?php

/**
 * Отчет "Кол-во посещений у Сотрудника"
 */


/**
 * График посещений Сотрудника
 */
$userVisitsGraph = [];


/**
 * Получение посещений Сотрудника
 */
$userVisits = $API->DB->from( "visits" )
    ->leftJoin( "visits_users ON visits_users.visit_id = visits.id" )
    ->select( null )->select( [ "visits.id", "visits.start_at", "visits.is_active", "visits.status" ] )
    ->where( [
        "visits_users.user_id" => $requestData->user_id,
        "visits.start_at >= ?" => date(
            "Y-m-d", strtotime( "-30 days", strtotime( date( "Y-m-d" ) ) )
        )
    ] )
    ->orderBy( "visits.start_at desc" )
    ->limit( 0 );


/**
 * Формирование графика посещений
 */

foreach ( $userVisits as $userVisit ) {

    $visitDate = date( "Y-m-d", strtotime( $userVisit[ "start_at" ] ) );
    $userVisitsGraph[ $visitDate ]++;

} // foreqch. $userVisits


$API->returnResponse(

    [
        [
            "value" => count( $userVisits ) . " посещений",
            "description" => "за 30 дней",
            "icon" => "",
            "prefix" => "",
            "postfix" => [
                "icon" => "",
                "value" => "",
                "background" => ""
            ],
            "type" => "char",
            "background" => "",
            "detail" => $userVisitsGraph

        ]
    ]

);