<?php

/**
 * Формирование списка пользователей
 */


if ( $requestData->context->block === "list" ) {

    /**
     * Сформированный список
     */
    $filter = [];
    $servicesFilter = [];
    if ( $requestData->start_at ) $filter[ "date >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $filter[ "date <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;
    if ( $requestData->category_id ) $servicesFilter[ "category_id" ] = $requestData->category_id;
    if ( $requestData->users_id ) $filterUsers[ "users_id" ] = $requestData->users_id;

    $servicesFilter[ "is_active" ] = "Y";

    $companyVisitsServices = $API->DB->from( "visits_services" )
        ->where( $filter );

    $services = $API->DB->from( "services" )
        ->where( $servicesFilter );

    $returnVisits = [];

    foreach ( $services as $service ) {

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
