<?php

ini_set( 'display_errors', 1 );
$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];


/**
 * Проверка корректности введённых значений
 */

if ( $requestData->action == "sell" )
    require ( $publicAppPath . "/custom-commands/salesList/add/validation.php" );


/**
 * Создание транзакции
 */
require ( $publicAppPath . "/custom-commands/salesList/add/create-transaction.php" );


$API->returnResponse( true );