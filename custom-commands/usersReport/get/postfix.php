<?php

/**
 * @file
 * Список "отчет сотруднки
 */

$returnServices = [];


/**
 * Текущая дата
 */

$currentDateTime = new DateTime();

foreach ( $response[ "data" ] as $user ) {

    $returnServices[$user[ "id" ]][ "last_name" ] = $user[ "last_name" ] . " " . mb_substr($user[ "first_name" ], 0, 1) . ". " . mb_substr($user[ "patronymic" ], 0, 1) . ".";


    /**
     * Получение посещений Сотрудника за 3 месяца
     */
    $visits3Month = $API->DB->from( "visits" )
        ->leftJoin( "visits_users ON visits_users.visit_id = visits.id" )
        ->select( null )->select( [ "visits.id", "visits_users.user_id", "visits.start_at",  "visits.end_at", "visits.is_active", "visits.price" , "visits.status" ] )
        ->where( [
            "start_at >=?" => $currentDateTime->modify( "-2 month" )->format( "Y-m-01 00:00:00" ),
            "is_payed" => "Y",
            "visits_users.user_id" => $user[ "id" ],
        ] )
        ->orderBy( "visits.start_at desc" )
        ->limit( 0 );

    /**
     * Обход рабочих Сотрудника за 3 месяца
     */
    $workDays3Month = $API->DB->from( "workDays" )
        ->where( [
            "user_id" => $user[ "id" ],
            "event_from >=? " => $currentDateTime->modify( "-2 month" )->format( "Y-m-01 00:00:00" ),
        ] );


    /**
     * Обход рабочих Сотрудника за 3 месяца
     */
    foreach ( $workDays3Month as $workDay ) {

        if ( $workDay[ "event_from" ] >= $currentDateTime->format( "Y-m-01 00:00:00" ) ) {
            $returnServices[$user[ "id" ]][ "count_one" ] = $returnServices[$user[ "id" ]][ "count_one" ] + 1;
            $returnServices[$user[ "id" ]][ "count_two" ] = $returnServices[$user[ "id" ]][ "count_two" ] + 1;
            $returnServices[$user[ "id" ]][ "count_three" ] = $returnServices[$user[ "id" ]][ "count_three" ] + 1;
        } elseif ( $workDay[ "event_from" ] >= $currentDateTime->modify( "-1 month" )->format( "Y-m-01 00:00:00" ) ) {
            $returnServices[$user[ "id" ]][ "count_two" ] = $returnServices[$user[ "id" ]][ "count_two" ] + 1;
            $returnServices[$user[ "id" ]][ "count_three" ] = $returnServices[$user[ "id" ]][ "count_three" ] + 1;
        } elseif ( $workDay[ "event_from" ] >= $currentDateTime->modify( "-2 month" )->format( "Y-m-01 00:00:00" ) ) {
            $returnServices[$user[ "id" ]][ "count_three" ] = $returnServices[$user[ "id" ]][ "count_three" ] + 1;
        }

    }
    /**
     * Обход посещений Сотрудника за 3 месяца
     */
    foreach ( $visits3Month as $visit ) {

        if ( $visit[ "start_at" ] >= $currentDateTime->format( "Y-m-01 00:00:00" ) ) {
            $returnServices[$user[ "id" ]][ "sum_one" ] = $returnServices[$user[ "id" ]][ "sum_one" ] + 1;
            $returnServices[$user[ "id" ]][ "sum_two" ] = $returnServices[$user[ "id" ]][ "sum_two" ] + $visit[ "price" ];
            $returnServices[$user[ "id" ]][ "sum_three" ] = $returnServices[$user[ "id" ]][ "sum_three" ] + $visit[ "price" ];
        } elseif ( $visit[ "start_at" ] >= $currentDateTime->modify( "-1 month" )->format( "Y-m-01 00:00:00" ) ) {
            $returnServices[$user[ "id" ]][ "sum_two" ] = $returnServices[$user[ "id" ]][ "sum_two" ] + $visit[ "price" ];
            $returnServices[$user[ "id" ]][ "sum_three" ] = $returnServices[$user[ "id" ]][ "sum_three" ] + $visit[ "price" ];
        } elseif ( $visit[ "start_at" ] >= $currentDateTime->modify( "-2 month" )->format( "Y-m-01 00:00:00" ) ) {
            $returnServices[$user[ "id" ]][ "sum_three" ] = $returnServices[$user[ "id" ]][ "sum_three" ] + $visit[ "price" ];
        }

    }

}

$response[ "data" ] = array_values($returnServices);
