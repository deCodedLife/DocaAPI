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
    $usersFilter = [];

    $servicesFilter[ "is_active" ] = "Y";
    if ( $requestData->start_at ) $filter[ "date >= ?" ] = $requestData->start_at . " 00:00:00";
    if ( $requestData->end_at ) $filter[ "date <= ?" ] = $requestData->end_at . " 23:59:59";
    if ( $requestData->store_id ) $filter[ "store_id" ] = $requestData->store_id;
    if ( $requestData->user_id ) $usersFilter[ "user_id" ] = $requestData->user_id;

    /**
     * Список услуг из пользователя
     */
    $services_users = $API->DB->from( "services_users" )
        ->where( $usersFilter );

    /**
     * Список услуг из посещения
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
        $discount = 0;
        foreach ( $companyVisitsServices as $companyVisitsService ) {

            if ( $companyVisitsService[ "service_id" ] == $service[ "id" ]) {

                $visit = $API->DB->from( "visits" )
                    ->where( "id", $companyVisitsService[ "visit_id" ] )
                    ->limit( 1 )
                    ->fetch();

                $countServices = 0;

                $visitsServices = $API->DB->from( "visits_services" )
                    ->where( "visit_id", $companyVisitsService[ "visit_id" ] );

                foreach ( $visitsServices as $visitService ) {

                    $countServices++;

                }
                $discount += $visit[ "discount_value"] / $countServices ;
                $count++;

            }

        }

        $returnVisits[] = [
            "id" => $service[ "id" ],
            "title" => $service[ "title" ],
            "price" => $service[ "price" ],
            "count" => $count,
            "discount_value" => $discount ,
            "date" => $service[ "date" ],
            "store_id" => $service[ "store_id" ],
            "category_id" => $service[ "category_id" ],
            "sum" => $service[ "price" ] * $count
        ];


    }

    $response[ "data" ] = $returnVisits;

} // if. $requestData->context->block === "list"
