<?php

/**
 * Подстановка ID клиента
 */
if ( ( $requestData->context->form == "deposit" ) && $requestData->context->row_id )
    $formFieldValues[ "id" ] = $requestData->context->row_id;