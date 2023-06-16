<?php

$payments = $API->DB->from( "sales" )
    ->where( "is_active", 'Y' );

if ( $requestData->context->block === "list" ) {

    $listData = [];

    foreach ( $payments as $payment ) {

        $listItem = [];
        $listItem[ "date" ] = $payment[ "created_at" ];

        if ( $payment[ "pay_type" ] === "sell" ) $listItem[ "operation_type" ] = "Продажа";
        if ( $payment[ "pay_type" ] === "sellReturn" ) $listItem[ "operation_type" ] = "Возврат";

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
//        {"id":"92","employee":"1","pay_type":"sell","bonus_sum":"0","deposit_sum":"0","card_sum":"0","cash_sum":"26","status":"waiting","error":null,"terminal_code":null,"":"2023-04-06 21:58:03","store_id":"1","is_activ

//        $API->returnResponse( json_encode($payment), 500 );

        $listData[] = $listItem;

    }

    $response[ "data" ] = $listData;

}