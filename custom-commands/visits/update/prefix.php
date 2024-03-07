<?php

/**
 * Расчет свободности Исполнителей, Клиентов и Кабинетов
 */

if ( !$requestData->is_alert )
{
    /**
     * Валидация посещения
     */
    $publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
    require_once ( $publicAppPath . "/custom-libs/visits/validate.php" );
}

if ( !$requestData->clients_id )
{

    $visits_clients = $API->DB->from( "visits_clients")->
        where( "visit_id", $requestData->id );

    $clients = [];

    foreach ( $visits_clients as $visits_client ) {

        $clients[] = (int)$visits_client[ "client_id" ];

    }

    $requestData->clients_id = $clients;

}

if ( $requestData->status == "waited" ) $requestData->dateIssueCoupon = date("Y-m-d H:i:s");