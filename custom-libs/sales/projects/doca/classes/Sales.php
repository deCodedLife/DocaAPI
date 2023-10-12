<?php

namespace Sales;

class Sales
{
    public static function getServices( array $visitsID ) {
        global $API;

        $servicesOutput = [];
        $visitsServices = $API->DB->from( "services" )
            ->innerJoin( "visits_services on visits_services.sale_id = services.id" )
            ->where( "visits_services.visit_id", $visitsID );

        $API->returnResponse( (array) $visitsServices );


    }
}