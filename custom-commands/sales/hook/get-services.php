<?php

/**
 * Получение детальной информации об услуге
 *
 * @param $serviceID
 * @return mixed
 */

function getServiceDetails( $serviceID ) {

    global $API;
    return $API->DB->from( "services" )
        ->where( "id", $serviceID )
        ->fetch();

} // function. getServiceDetails $serviceID



/**
 * Получение информации обо всех услугах в посещениях
 */

foreach ( $saleVisits as $saleVisit ) {

    $visitServices = $API->DB->from( "visits_services" )
        ->where( "visit_id", $saleVisit[ "id" ] );

    foreach ( $visitServices as $visitService ) {
        $saleServices[] = getServiceDetails($visitService["service_id"]);
        $allServices[] = end( $saleServices );
    }

} // foreach. $saleVisits as $saleVisit



/**
 * Если тип операции - "возврат", тогда собирается информация только о
 * прикреплённых к данной операции услугах
 */

if ( $is_return ) {

    $saleServices = [];

    foreach ( $requestData->pay_object as $saleID )
        $saleServices[] = getServiceDetails( $saleID );

} // if. $is_return