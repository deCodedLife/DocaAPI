<?php


/**
 * Фильтр Расписания по врачу
 */
if ( $requestData->context->block === "day_planning" ) {

    $requestData->start_at = date( "Y-m-d" );
    $requestData->users_id = $API::$userDetail->id;

} // if. $requestData->context->block === "day_planning"


if ( $requestData->context->block === "logs" ) {

    $returnLogs = [];


    foreach ( $response[ "data" ] as $visit ) {

        $returnLogs[] = [
            "id" => $visit[ "id" ],
            "table_name" => "Посещения",
            "status" => "info",
            "description" => "Посещение № " . $visit[ "id" ],
            "ip" => "176.52.40.31",
            "row_id" => $visit[ "id" ],
            "created_at" => $visit[ "start_at" ],
            "users_id" => $visit[ "users_id" ],
            "clients_id" => $visit[ "clients_id" ]
        ];

    }

    $response[ "data" ] = $returnLogs;

}
