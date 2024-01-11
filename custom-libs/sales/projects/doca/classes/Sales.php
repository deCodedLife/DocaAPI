<?php

namespace Sales;

class Sales
{
    public static function getServices( array $visitsID ) {
        global $API, $requestData;

        $servicesOutput = [];
        $visitsServices = [];

        if ( $requestData->object !== "visits" ) {

            $services = [];

            foreach ( $visitsID as $visit ) {

                $service = $API->DB->from( $requestData->object )
                    ->where( "id", $visit )
                    ->fetch();

                if ( !$service ) continue;

                $services[] = $service[ "service_id" ];

            }

            $visitsServices = $API->DB->from( "services" )
                ->where( "id", $services );

        } else {

            $visitsServices = $API->DB->from( "services" )
                ->innerJoin( "visits_services on visits_services.sale_id = services.id" )
                ->where( "visits_services.visit_id", $visitsID );

        }

        $API->returnResponse( (array) $visitsServices );


    }
}