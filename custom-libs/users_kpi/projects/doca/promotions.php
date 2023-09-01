<?php namespace KPI;


global $API;
require_once LIB_PATH . "/classes/KPI.php";


function handleRequest() {

    global $API, $requestData;
    $kpi_type = "count";
    $target_row = "id";

    if ( !$requestData->id ) return;
    if ( !$requestData->sales ) return;

    foreach ( $requestData->sales as $sale ) {

        if ( !$sale->service ) return;
        if ( !$sale->required_value ) return;
        if ( !$sale->kpi_value ) return;

        $kpiID = $API->DB->insertInto( KPI_TABLE )
            ->values( [
                "user_id" => $requestData->id,
                "type" => $kpi_type,
                "target_row" => $target_row,
                "required_value" => $sale->required_value,
                "kpi_value" => $sale->kpi_value
            ] )
            ->execute();

        /**
         * 1 получить посещения человека
         * 2 получить оплаченные
         * 3 посчитать сумму
         */
        $API->DB->insertInto( KPI_RELATED )
            ->values( [
                "kpi_id" => $kpiID,
                "property" => "visits.is_payed",
                "priority" => 0,
                "value" => "Y",
                "related" => "N"
            ] )
            ->execute();

        $API->DB->insertInto( KPI_RELATED )
            ->values( [
                "kpi_id" => $kpiID,
                "property" => "visits.id",
                "priority" => 1,
                "value" => "visits.id",
                "related" => "Y"
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
