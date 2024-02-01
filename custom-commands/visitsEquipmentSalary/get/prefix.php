<?php

function getServicesIds( $category ): array {

    global $API;

    $sqlFilter = "SELECT id FROM services WHERE category_id = $category";
    $servicesList = mysqli_query( $API->DB_connection, $sqlFilter );
    $services_ids = [];

    foreach ( $servicesList as $service ) $services_ids[] = intval( $service[ "id" ] );
    return $services_ids;

}

function getVisitsIds( $table, $start_at, $end_at, $user_id ): array {

    global $API;

    $sqlFilter = "
    SELECT $table.id as id 
    FROM $table 
    WHERE 
        created_at >= '$start_at' AND 
        end_at <= '$end_at' AND 
        ( user_id = $user_id OR assist_id = $user_id ) AND
        is_active = 'Y' AND
        is_payed = 'Y' AND
        status = 'ended'";

    $visitsList = mysqli_query( $API->DB_connection, $sqlFilter );
    $visits_ids = [];

    foreach ( $visitsList as $visit ) $visits_ids[] = intval( $visit[ "id" ] );
    return $visits_ids;

}

$requestData->id = getVisitsIds(
    "equipmentVisits",
    $requestData->start_at,
    $requestData->end_at,
    $requestData->user_id
);

$requestData->context->user_id = $requestData->user_id;

unset( $requestData->start_at );
unset( $requestData->end_at );
unset( $requestData->user_id );
unset( $requestData->status );

if ( empty( $requestData->id ) ) $requestData->id = [ 0 ];
$requestSettings[ "filter" ][ "id" ] = $requestData->id;