<?php
/**
 * Сформированный список
 */
$returnVisits = [];

foreach ( $response[ "data" ] as $visit ) {

        $visits_services = $API->DB->from( "visits_services" )
            ->where( "visit_id", $visit[ "id" ] );
        
        $visit[ "period" ] = "c " . $visit[ "start_at" ] . " по " . $visit[ "end_at" ];

        foreach ( $visits_services as $visit_service) {

            $service = $API->DB->from( "services" )
                ->where( "id", $visit_service[ "service_id" ] )
                ->limit( 1 )
                ->fetch();

            $visit[ "services_id" ][] = [
                "title" => $service[ "title" ],
                "value" => $service[ "id" ]
            ];

        }

        $returnVisits[] = $visit;

}

$response[ "data" ] = $returnVisits;
