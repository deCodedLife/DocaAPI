<?php

$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];

$formFieldValues[ "services_info" ] = $requestData->context->row_id;


/**
 * Проверка корректности введённых значений
 */

if ( $requestData->pay_type == "sell" )
    require ( $publicAppPath . "/custom-commands/sales/add/validation.php" );


/**
 * Создание транзакции
 */
require ( $publicAppPath . "/custom-commands/sales/add/create-transaction.php" );

$API->returnResponse();
