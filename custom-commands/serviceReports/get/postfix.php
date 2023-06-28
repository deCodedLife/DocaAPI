<?php

/**
 * Формирование списка посещений
 */


if ( $requestData->context->block === "list" ) {

    /**
     * Формирование фильтров
     */
    $filter = [];
    $servicesFilter = [];

    if ( $requestData->start_at ) $filter[ "date >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $filter[ "date <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;
    if ( $requestData->category_id ) $servicesFilter[ "category_id" ] = $requestData->category_id;
    if ( $requestData->id ) $servicesFilter[ "id" ] = $requestData->id;
    $servicesFilter[ "is_active" ] = "Y";

    /**
     * Список услуг в посещении
     */
    $companyVisitsServices = $API->DB->from( "visits_services" )
        ->where( $filter );

    /**
     * Список услуг
     */
    $services = $API->DB->from( "services" )
        ->where( $servicesFilter );

    /**
     * Сформированный список
     */
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
