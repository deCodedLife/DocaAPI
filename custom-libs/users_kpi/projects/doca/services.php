<?php namespace KPI;


global $API;
require_once LIB_PATH . "/classes/KPI.php";


function handleRequest() {

    global $API, $requestData;
    $kpi_type = "count";
    $target_row = "service_id";

    if ( !$requestData->id ) return;
    if ( !$requestData->services ) return;

    foreach ( $requestData->services as $service ) {

        if ( !$service->service ) return;
        if ( !$service->required_value ) return;
        if ( !$service->kpi_value ) return;

        $kpiID = $API->DB->insertInto( KPI_TABLE )
            ->values( [
                "user_id" => $requestData->id,
                "type" => $kpi_type,
                "target_row" => $target_row,
                "required_value" => $service->required_value,
                "kpi_value" => $service->kpi_value
            ] )
            ->execute();

        $API->DB->insertInto( KPI_RELATED )
            ->values( [
                "kpi_id" => $kpiID,
                "property" => "visits_services.visit_id",
                "priority" => 0,
                "value" => "visits.id",
                "related" => "Y"
            ] )
            ->execute();

        $API->DB->insertInto( KPI_RELATED )
            ->values( [
                "kpi_id" => $kpiID,
                "property" => "visits.is_payed",
                "priority" => 1,
                "value" => "Y",
                "related" => "N"
            ] )
            ->execute();

        $API->DB->insertInto( KPI_RELATED )
            ->values( [
                "kpi_id" => $kpiID,
                "property" => "visits.user_id",
                "priority" => 2,
                "value" => $requestData->id,
                "related" => "N"
            ] )
            ->execute();



    }

}

handleRequest();
