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

    $returnServices[$user[ "id" ]][ "count_one" ] = 0;
    $returnServices[$user[ "id" ]][ "count_two" ] = 0;
    $returnServices[$user[ "id" ]][ "count_three" ] = 0;
    $returnServices[$user[ "id" ]][ "sum_one" ] = "0";
    $returnServices[$user[ "id" ]][ "sum_two" ] = "0";
    $returnServices[$user[ "id" ]][ "sum_three" ] = "0";
    /**
     * Получение посещений Сотрудника за 3 месяца
     */
    $visits3Month = $API->DB->from( "visits" )
        ->select( null )->select( [ "id", "user_id", "start_at",  "end_at", "is_active", "price" , "status" ] )
        ->where( [
            "start_at >=?" => $currentDateTime->modify( "-2 month" )->format( "Y-m-01 00:00:00" ),
            "is_payed" => "Y",
            "user_id" => $user[ "id" ],
        ] )
        ->orderBy( "start_at desc" )
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

            $returnServices[$user[ "id" ]][ "sum_one" ] = $returnServices[$user[ "id" ]][ "sum_one" ] + $visit[ "price" ];

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

function array_sort ( $array, $on, $order=SORT_ASC )
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

if ( $sort_by == "last_name" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "last_name", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "last_name", SORT_ASC ) );

}

if ( $sort_by == "count_one" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count_one", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count_one", SORT_ASC ) );

}

if ( $sort_by == "count_two" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count_two", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count_two", SORT_ASC ) );

}

if ( $sort_by == "count_three" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count_three", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "count_three", SORT_ASC ) );

}

if ( $sort_by == "sum_one" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_one", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_one", SORT_ASC ) );


}

if ( $sort_by == "sum_two" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_two", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_two", SORT_ASC ) );


}

if ( $sort_by == "sum_three" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_three", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "sum_three", SORT_ASC ) );


}
