<?php

/**
 * @file
 * Хуки на Добавление правила
 */
$formFieldsUpdate = [];

if ( $requestData->store_id ) {

    $formFieldsUpdate[ "cabinet_id" ] = [ "is_visible" => true ];

}


$API->returnResponse( $formFieldsUpdate );
