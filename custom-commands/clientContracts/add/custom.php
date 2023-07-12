<?php

/**
 * Подписание договора
 */
$API->DB->update( "clients" )
    ->set( "is_contract", "Y" )
    ->where( "id", $requestData->row_id )
    ->execute();