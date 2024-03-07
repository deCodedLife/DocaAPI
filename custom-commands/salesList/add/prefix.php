<?php

$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];


$API->returnResponse( $requestData, 500 );

if ( $requestData->action == "sell" )
    require ( $publicAppPath . "/custom-commands/salesList/add/validation.php" );


/**
 * Создание транзакции
 */
require ( $publicAppPath . "/custom-commands/salesList/add/create-transaction.php" );


$API->returnResponse();