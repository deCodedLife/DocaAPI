<?php

/**
 * Формирование списка посещений
 */


if ( $requestData->context->block === "list" ) {

    /**
     * Сформированный список
     */
    $filter = [];
    $servicesFilter = [];
    $servicesFilter[ "is_active" ] = "Y";
    $usersFilter = [];
    if ( $requestData->start_at ) $filter[ "date >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $filter[ "date <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;
    if ( $requestData->user_id ) $usersFilter[ "user_id" ] = $requestData->user_id;

    $companyVisitsServices = $API->DB->from( "visits_services" )
        ->where( $filter );

    $services = $API->DB->from( "services" )
        ->where( $servicesFilter );

    $services_users = $API->DB->from( "services_users" )
        ->where( $usersFilter );

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
            "price" => $service[ "price" ],
            "count" => $count,
            "discount_value" => 1,
            "date" => $service[ "date" ],
            "store_id" => $service[ "store_id" ],
            "category_id" => $service[ "category_id" ],
            "sum" => $service[ "price" ] * $count
        ];


    }

    $response[ "data" ] = $returnVisits;

} // if. $requestData->context->block === "list"
