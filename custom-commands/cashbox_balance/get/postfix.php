<?php

//$API->returnResponse( json_encode($requestData), 500 );

$today = new DateTime();

/**
 * Получение списка продаж
 */
$payments = $API->DB->from( "salesList" )
    ->where( [
        "created_at >= ?" => $today->format( "Y-m-d" ) . " 00:00:00",
        "created_at <= ?" => $today->format( "Y-m-d" ) . " 23:59:59"
    ] );


/**
 * Если возвращаемый тип - список
 */
if ( $requestData->context->block === "list" ) {

    $listData = [];

    /**
     * Обход всех продаж
     */
    foreach ( $payments as $payment ) {

        $listItem = [];
        $listItem[ "date" ] = $payment[ "created_at" ];

        if ( $payment[ "action" ] === "sell" ) $listItem[ "operation_type" ] = "Продажа";
        if ( $payment[ "action" ] === "sellReturn" ) $listItem[ "operation_type" ] = "Возврат";

        $listItem[ "client_id" ] = $payment[ "client_id" ];

        $client = mysqli_fetch_array(mysqli_query(
            $API->DB_connection,
            "SELECT * FROM clients WHERE id = {$payment[ "client_id" ]}"
        ));

        $listItem[ "client" ][] = [
            "title" => $client[ "last_name" ],
            "value" => $client[ "id" ]
        ];

        $listItem[ "summary" ] = $payment[ "summary" ];

        $employee = mysqli_fetch_array( mysqli_query(
            $API->DB_connection,
            "SELECT * FROM users WHERE id = {$payment[ "employee" ]}"
        ) );

        $listItem[ "operator" ] = $employee[ "last_name" ];
        $listData[] = $listItem;

    } // foreach ( $payments as $payment )

    $response[ "data" ] = $listData;

} // if ( $requestData->context->block === "list" )