<?php
/**
 * Сформированный список
 */
$returnVisits = [];

foreach ( $response[ "data" ] as $visit ) {

    $visits_services = $API->DB->from( "visits_services" )
        ->where( "visit_id", $visit[ "id" ] );

    $visit[ "period" ] = date( 'Y-m-d H:i', strtotime( $visit[ "start_at" ] ) ) . " - " . date( "H:i", strtotime( $visit[ "end_at" ] ) );

    foreach ( $visits_services as $visit_service) {

        $service = $API->DB->from( "services" )
            ->where( "id", $visit_service[ "service_id" ] )
            ->limit( 1 )
            ->fetch();

        $visit[ "services_id" ][] = [
            "title" => $service[ "title" ],
            "value" => $service[ "id" ]
        ];

        $visit[ "category_id" ][] = [
            "title" => $service[ "category_id" ],
            "value" => (int)$service[ "category_id" ]
        ];

    }

    $returnVisits[] = $visit;

}

if ( $requestData->category ) {

    foreach ( $returnVisits as $index => $returnVisit ) {

        $visits_services = $API->DB->from( "visits_services" )
            ->where( "visit_id", $returnVisit[ "id" ] );

        $service_exists = false;

        foreach ( $visits_services as $visit_service) {

            $service = $API->DB->from( "services" )
                ->where( "id", $visit_service[ "service_id" ] )
                ->limit( 1 )
                ->fetch();

            if ( $service[ "category_id" ] == $requestData->category )
                $service_exists = true;

        }

        if ( $service_exists == false ) unset( $returnVisits[ $index ] );

    }

}

if ( $requestData->service && !empty( $requestData->service ) ) {

    foreach ( $returnVisits as $index => $returnVisit ) {

        $visits_services = $API->DB->from( "visits_services" )
            ->where( "visit_id", $returnVisit[ "id" ] );

        $service_exists = false;

        foreach ( $visits_services as $visit_service) {

            if ( in_array( (int)$visit_service[ "service_id" ], $requestData->service  ) )
                $service_exists = true;

        }
        if ( $service_exists == false ) unset( $returnVisits[ $index ] );

    }

}


$response[ "data" ] = array_values($returnVisits);

