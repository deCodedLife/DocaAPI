<?php

/**
 * Формирование списка посещений
 */


if ( $requestData->context->block === "list" ) {
    /**
     * Сформированный список
     */
    $filter = [];
    if ( $requestData->start_at ) $filter[ "date >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $filter[ "date <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;
    if ( $requestData->category_id ) $filter[ "category_id" ] = $requestData->category_id;
    if ( $requestData->service_id ) $filter[ "service_id" ] = $requestData->service_id;

    $companyVisitsServices = $API->DB->from( "visits_services" )
        ->where( $filter );

    $returnVisits = [];

    foreach ( $response[ "data" ] as $service ) {

        $count = 0;

        foreach ( $companyVisitsServices as $companyVisitsService ) {

            if ( $companyVisitsService[ "service_id" ] == $service[ "id" ]) {

                $count++;

            }

        }

        $returnVisits[] = [
            "id" => $service[ "id" ],
            "title" => $service[ "title" ],
            "count" => $count,
            "date" => $service[ "date" ],
            "store_id" => $service[ "store_id" ],
            "category_id" => $service[ "category_id" ],
            "sum" => $service[ "price" ] * $count
        ];

    }

    $response[ "data" ] = $returnVisits;

} // if. $requestData->context->block === "list"
